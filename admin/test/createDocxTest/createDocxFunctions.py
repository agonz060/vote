import mysql.connector
import time
from datetime import date

def setColumnWidth(column,width):
	#column.width = width
	for cell in column.cells:
		cell.width = width

def getTodaysDate():
	return date.today().strftime('%B %d, %Y')

def getPollData(conn,pollId):
	try:
		# create cursor to execute mysql queries
		cur = conn.cursor(dictionary=True)
		# select all poll information
		selectStmt = ("SELECT * FROM Polls WHERE poll_id=%s")
		cur.execute(selectStmt,(pollId,))
		pollData = cur.fetchone()
		# close connection, no longer needed
		cur.close()
	except mysql.connector.Error as err:
		print('Error getting poll data: {}'.format(err))

	if(isMultiActionPollType(pollData['pollType'])):
		actionInfo = getActionInfo(conn,pollId)
		pollData['actionInfo'] = actionInfo['actionInfo']
		pollData['actionCount'] = actionInfo['actionCount']
	return pollData

def getActionInfo(conn,pollId):
	# create list to store action info
	actionCount = 0
	actionInfo = {}
	actionInfoTemp = []
	# set up cursor & select statement
	cur = conn.cursor(dictionary=True)
	selectStmt = ("SELECT action_num,fromTitle,fromStep,toTitle,toStep,accelerated FROM Poll_Actions WHERE poll_id=%s ORDER BY action_num ASC")
	cur.execute(selectStmt,(pollId,))
	for row in cur:
		actionCount += 1
		actionInfoTemp.append(row)
	# close connection before return action info
	cur.close()
	actionInfo['actionInfo'] = actionInfoTemp
	actionInfo['actionCount'] = actionCount
	return actionInfo

def getActionDescriptions(pollData):
	# description is a dictionary that will contain an action's description while descriptions is  list containing the action descriptions
	actionDescription = None
	actionData = descriptions = []	
	if(isMultiActionPollType(pollData['pollType'])):
		actionInfo = pollData['actionInfo']
		actionCount = pollData['actionCount']
		index = 0
		while(index < actionCount):
			# store description information in actionData to produce descriptions 
			actionData = actionInfo[index]
			actionData['dept'] = pollData['dept']
			actionData['pollType'] = pollData['pollType']
			actionData['profName'] = pollData['name']
			actionData['effDate'] = pollData['effDate']
			if(actionData['accelerated']):
				actionDescription = 'Recommendation for {profName}\'s Accelerated {pollType} from {fromTitle} {fromStep} to {toTitle} {toStep} in the {dept} department, effective {effDate}'.format(**actionData)
			else:
				actionDescription = 'Recommendation for {profName}\'s {pollType} from {fromTitle} {fromStep} to {toTitle} {toStep} in the {dept} department, effective {effDate}'.format(**actionData)
			# increment index and store action description
			index += 1
			descriptions.append(actionDescription)
	else:
		actionDescription = 'Recommendation for {name}\'s {pollType} in the {dept} department, effective {effDate}'.format(**pollData)
		descriptions.append(actionDescription)
	return  descriptions

def getEligibleVoteCount(conn,pollData):
	# get restrictions
	restrictions = getVotingRestrictions(pollData)
	try:
		# setup cursor to execut command
		cur = conn.cursor(dictionary=True)
		selectStmt = 'SELECT count(Voters.user_id) AS eligible FROM Voters INNER JOIN Users ON Users.user_id=Voters.user_id'
		selectStmt = selectStmt + ' WHERE {0} Voters.poll_id={1} GROUP BY Voters.poll_id'.format(restrictions,pollData['poll_id'])
		# execute select query
		cur.execute(selectStmt)
		row = cur.fetchone()
		# close connection
		cur.close()
	except mysql.connector.Error as err:
		print('Error getting eligible vote count: {}'.format(err))
	return row['eligible']

def getVotingRestrictions(pollData):
	EVALUATION_FORM = 3
	PROFESSOR_TITLE = 'Users.title'
	restrictions = ''
	if(pollData['assistantForm'] == EVALUATION_FORM):
		restrictions = '({0} != \'Assistant Professor\''.format(PROFESSOR_TITLE)
	if(pollData['associateForm'] == EVALUATION_FORM):
		if(restrictions):
			restrictions = restrictions + ' AND {0} != \'Associate Professor\''.format(PROFESSOR_TITLE)
		else: 
			restrictions = '({0} != \'Associate Professor\''.format(PROFESSOR_TITLE)
	if(pollData['fullForm'] == EVALUATION_FORM):
		if(restrictions):
			restrictions = restrictions + ' AND {0} != \'Full Professor\''.format(PROFESSOR_TITLE)
		else:
			restrictions = '({0} != \'Full Professor\''.format(PROFESSOR_TITLE)
	if(restrictions):
		restrictions = restrictions + ') AND '

	return restrictions

def getVoteCounts(conn,pollData):
	# voting options
	FOR = 1
	AGAINST = 2
	ABSTAIN = 3
	SATISFACTORY_QUALIFICATIONS = 4
	# get database information
	restrictions = getVotingRestrictions(pollData)
	eligible = getEligibleVoteCount(conn,pollData)
	dataTable = pollTypeToTable(pollData['pollType'])
	# Setup vote count variables
	vote = 0
	multiActionVoteCounts = {'1': None, '2': None, '3': None}
	voteCounts = {'for': 0, 'against': 0, 'abstain': 0, 'qualifications': 0, 'eligible': eligible, 'total': 0}
	# get all eligible vote counts for poll 
	if(isMultiActionPollType(pollData['pollType'])):
		for actionNum in range(1,pollData['actionCount']+1):
			# select all data from multi-action data tables
			selectStmt = 'SELECT {0}.vote, {0}.action_num FROM {0} INNER JOIN Users ON Users.user_id={0}.user_id'.format(dataTable)
			selectStmt = selectStmt + ' WHERE {1} {0}.poll_id={2} AND {0}.action_num={3}'.format(dataTable,restrictions,pollData['poll_id'],actionNum)
			# convert action number to string to function as an index
			index = str(actionNum)
			try:
				cur = conn.cursor(dictionary=True)
				cur.execute(selectStmt)
				for row in cur:
					if(multiActionVoteCounts[index] == None):
						multiActionVoteCounts[index] = {'for': 0, 'against': 0, 'abstain': 0, 'qualifications': 0, 'eligible': eligible, 'total': 0}
					vote = row['vote']
					multiActionVoteCounts[index]['total'] += 1
					if(vote == FOR):
						multiActionVoteCounts[index]['for'] += 1
					elif(vote == AGAINST):
						multiActionVoteCounts[index]['against'] += 1
					elif(vote == ABSTAIN):
						multiActionVoteCounts[index]['abstain'] += 1
					elif(vote == SATISFACTORY_QUALIFICATIONS):
						multiActionVoteCounts[index]['qualifications'] += 1
				# close cursor
				cur.close()
				# transfer data to voteCounts 
				voteCounts = multiActionVoteCounts
			except mysql.connector.Error as err:
				check('Error getting poll data #1: {}'.format(err))
	else: # single action poll type
		selectStmt = 'SELECT {0}.vote, count({0}.vote) as voteCount FROM {0} INNER JOIN Users ON Users.user_id={0}.user_id'.format(dataTable)
		selectStmt = selectStmt + ' WHERE {1} {0}.poll_id={2} GROUP BY {0}.vote'.format(dataTable,restrictions,pollData['poll_id'])
		try:
			cur = conn.cursor(dictionary=True)
			cur.execute(selectStmt)
			for row in cur:
				vote = row['vote']
				voteCounts['total'] += 1
				if(vote == FOR):
					voteCounts['for'] += 1
				elif(vote == AGAINST):
					voteCounts['against'] += 1
				elif(vote == ABSTAIN):
					voteCounts['abstain'] += 1
				elif(vote == SATISFACTORY_QUALIFICATIONS):
					voteCounts['qualifications'] += 1
			# close cursor
			cur.close()
		except mysql.connector.Error as err:
			print('Error counting votes #2: {}'.format(err))
	return voteCounts



def getVotingOptions(pollData):
	# define all voting options
	positiveOpposedAbstain = ['Positive','Positive with Qualifications','Opposed','Abstain']
	inFavorOpposedAbstain = ['In favor', 'Opposed', 'Abstain']
	satisfactory = ['Satisfactory','Unsatisfactory','Abstain']
	satisfactoryWQualifications = ['Satisfactory','Satisfactory with Qualifications','Unsatisfactory','Abstain']

	# these voting options depend on poll type
	votingOptions = { 'Merit' : inFavorOpposedAbstain, 'Promotion' : inFavorOpposedAbstain, 'Reappointment' : inFavorOpposedAbstain,
						'Fifth Year Review' : satisfactoryWQualifications, 'Fifth Year Appraisal' : positiveOpposedAbstain, 'Other' : None }
	otherOptions = []
	# create options variable to store correct voting option choice
	options = None

	# get voting option from poll type
	options = votingOptions[pollData['pollType']]
	
	# these voting options are for when pollType == 'Other' and otherVotingOptions = <1/2/3>
	if(pollData['pollType'] == 'Other'):
		key = pollData['votingOptions'] - 1
		otherOptions.append(inFavorOpposedAbstain)
		otherOptions.append(positiveOpposedAbstain)
		otherOptions.append(satisfactoryWQualifications)
		options = otherOptions[key]

	return options

def getEvaluationRestrictions(pollData,multiActionPollType,actionNum):
	EVALUATION = 3
	selection = ''
	# if this is a multi-action poll type then the evaluation number must match the action number,
	# otherwise evaluations may still be made for single action votes as long as the professo's form = evaluation
	if(multiActionPollType):
		if(pollData['assistantForm'] == EVALUATION and pollData['assistantEvaluationNum'] == actionNum):
			selection = '(Users.title =\'Assistant Professor\''
		if(pollData['associateForm'] == EVALUATION and pollData['associateEvaluationNum'] == actionNum):
			if(selection):
				selection = selection + ' || Users.title =\'Associate Professor\''
			else:
				selection = '(Users.title=\'Associate Professor\''
		if(pollData['fullForm'] == EVALUATION and pollData['fullEvaluationForm'] == actionNum):
			if(selection):
				selection = selection + ' || Users.title =\'Full Professor\''
			else:
				selection = '(User.title =\'Full Professor\''
	else: # single action poll type
		if(pollData['assistantForm'] == EVALUATION):
			selection = '(Users.title =\'Assistant Professor\''
		if(pollData['associateForm'] == EVALUATION):
			if(selection):
				selection = selection + ' || Users.title =\'Associate Professor\''
			else:
				selection = '(Users.title=\'Associate Professor\''
		if(pollData['fullForm'] == EVALUATION):
			if(selection):
				selection = selection + ' || Users.title =\'Full Professor\''
			else:
				selection = '(User.title =\'Full Professor\''
	if(selection):
		selection = selection + ') AND '

	return selection

def getComments(conn,pollData,actionNum=0,confidentialEvals=0):
	# constants
	FALSE = 0
	COMMENT = 'voteCmt'
	
	# comment variables
	multiActionPollType = selectStmt = comments = evaluationRestrictions = None
	table = pollTypeToTable(pollData['pollType'])
	if(confidentialEvals):
		table = 'assistant_data'
	if(isMultiActionPollType(pollData['pollType'])):
		multiActionPollType = 1

	if(confidentialEvals == FALSE and multiActionPollType):
		comments = {}
		selectStmt = 'SELECT {0}.action_num,{0}.voteCmt FROM {0} WHERE {0}.poll_id={1}'.format(table,pollData['poll_id'])
		try:
			cur = conn.cursor(dictionary=True)
			cur.execute(selectStmt)
			for row in cur:
				actionNum = str(row['action_num'])
				if(comments.get(actionNum,None) == None): # create dictionary entry with key = actionNum
					comments[actionNum] = []
				comments[actionNum].append({COMMENT : row[COMMENT]})
			# close cursor
			cur.close()
		except mysql.connector.Error as err:
			print('Error getting multi-action vote comments: {}'.format(err))
	else: # confidential evaluations and single action poll types handled here
		comments = []
		if(confidentialEvals):
			restrictions = getEvaluationRestrictions(pollData,multiActionPollType,actionNum)
			selectStmt = 'SELECT voteCmt FROM {0} INNER JOIN Users on Users.user_id=assistant_data.user_id WHERE {1} assistant_data.poll_id={2}'.format(table,restrictions,pollData['poll_id'])
			print(selectStmt)
		else:
			selectStmt = 'SELECT * FROM {0} WHERE poll_id={1}'.format(table,pollData['poll_id'])
		try:
			cur = conn.cursor(dictionary=True)
			cur.execute(selectStmt)
			for row in cur:
				comments.append(row)
		except mysql.connector.Error as err:
			print('Error getting comments: {}'.format(err))
	return comments

def printReviewComments(doc,comments):
	# constants
	TRUE = 1
	COMMENT = 'voteCmt'
	QUALIFICATIONS = 'qualificationsCmt'

	# variables
	commentSet = qualificationsCmt = comment = displayComment = None

	# testing
	# print('Comment len: {}'.format(len(comments)))
	# print('Comments: {}'.format(comments))

	# print all comments
	for index in range(0,len(comments)):
		# reset variables
		displayComment = ''

		# gather both types of comments
		comment = comments[index][COMMENT]
		if(comment and len(comment) > 0):
			comment.strip() + '\n\n'
			displayComment = comment
			commentSet = TRUE

		qualificationsCmt = comments[index][QUALIFICATIONS]
		if(qualificationsCmt and len(qualificationsCmt) > 0):
			qualificationsCmt.strip() 
			displayComment += qualificationsCmt

		if(commentSet):
			doc.add_paragraph(displayComment,style='commmentParagraph')

 #{0}: {1}'.format(index,qualificationsCmt))



def printComments(doc,conn,pollData,actionNum,isEvaluation=0):
	# variables
	pollType = pollData['pollType']
	comments = getComments(conn,pollData,actionNum)
	
	# print comments
	if(pollType == 'Fifth Year Review'):
		printReviewComments(doc,comments)
	# elif(pollType == 'Appraisal'):
	# 	printAppraisalComments(doc,comments)
	# elif(isEvaluation or pollType == 'Reappointment' or pollType == isMultiActionPollType(pollType)):
	# 	printComments(doc,comments[str(actionNum)])





def checkForEvaluations(conn,pollData,actionNum):
	# set up variables
	selectStmt = ''
	evaluationInfo = {}
	multiActionPollType = isMultiActionPollType(pollData['pollType'])
	getEligibleVoteCount = actionEvaluationNum = commentCount = 0
	restrictions = getEvaluationRestrictions(pollData,multiActionPollType,actionNum)
	if(restrictions):
		# get all a count of all the voters who are eligible to make a confidential evaluation
		selectStmt = 'SELECT count(Voters.user_id) as eligible FROM Voters INNER JOIN Users ON Users.user_id=Voters.user_id WHERE {0} Voters.poll_id={1}'.format(restrictions,pollData['poll_id'])
		cur = conn.cursor(dictionary=True)
		cur.execute(selectStmt)
		row = cur.fetchone()
		evaluationInfo['eligible'] = row['eligible']
		# get comment count for current action
		selectStmt = 'SELECT count(assistant_data.voteCmt) AS commentCount FROM assistant_data INNER JOIN Users ON Users.user_id=assistant_data.user_id WHERE'
		selectStmt = selectStmt + ' {0} assistant_data.poll_id={1}'.format(restrictions,pollData['poll_id'])
		cur.execute(selectStmt)
		row = cur.fetchone()
		# store results
		evaluationInfo['commentCount'] = row['commentCount']
		# close cursor
		cur.close()
	else: # error occurred
		evaluationInfo = {'eligible' : 0, 'commentCount' : 0}

	return evaluationInfo

def formatActionResults(conn,pollData,actionNum):
	# constants
	TRUE = 1
	FALSE = 0
	TALLY_NOTICE = 'Tally of Votes: '
	SPACE_FORMAT2 = '  ' # 2 spaces
	SPACE_FORMAT4 = '    ' # 4 spaces
	ACTION_INDEX = str(actionNum)

	# setup result variables
	options = getVotingOptions(pollData)
	voteCounts = getVoteCounts(conn,pollData)
	multiActionPollType = isMultiActionPollType(pollData['pollType'])

	# create action results string
	actionResults = TALLY_NOTICE
	if(multiActionPollType):
		actionResults += str(voteCounts[ACTION_INDEX]['for']) + SPACE_FORMAT2
	else:
		actionResults += str(voteCounts['for']) + SPACE_FORMAT2

	# options[0] = <for>
	actionResults += options[0] + SPACE_FORMAT4

	# poll type may have different voting options 
	if(len(options) == 3):
		if(multiActionPollType):
			actionResults += str(voteCounts[ACTION_INDEX]['against']) + SPACE_FORMAT2 + options[1] + SPACE_FORMAT4
			actionResults += str(voteCounts[ACTION_INDEX]['abstain']) + SPACE_FORMAT2 + options[2] + SPACE_FORMAT4
		else:
			actionsResults += str(voteCounts['against']) + SPACE_FORMAT2 + options[1] + SPACE_FORMAT4
			actionResults += str(voteCounts['abstain']) + SPACE_FORMAT2 + options[2] + SPACE_FORMAT4
	else: # these actions contain 4 voting options 
		if(multiActionPollType): 
			actionResults += str(voteCounts[ACTION_INDEX]['qualifications']) + SPACE_FORMAT2 + options[1] + SPACE_FORMAT4
			actionResults += str(voteCounts[ACTION_INDEX]['against']) + SPACE_FORMAT2 + options[2] + SPACE_FORMAT4
			actionResults += str(voteCounts[ACTION_INDEX]['abstaint']) + SPACE_FORMAT2 + options[3] + SPACE_FORMAT4
		else: # 
			actionResults += str(voteCounts['qualifications']) + SPACE_FORMAT2 + options[1] + SPACE_FORMAT4
			actionResults += str(voteCounts['against']) + SPACE_FORMAT2 + options[2] + SPACE_FORMAT4
			actionResults += str(voteCounts['abstain']) + SPACE_FORMAT2 + options[3] + SPACE_FORMAT4

	return actionResults


def pollTypeToTable(pollType):
	tables = {'Merit' : 'Merit_Data', 'Promotion' : 'Associate_Promotion_Data',
				'Reappointment' : 'Reappointment_Data', 'Fifth Year Review' : 'Fifth_Year_Review_Data',
				'Fifth Year Appraisal' : 'Fifth_Year_Appraisal_Data', 'Other' : 'Other_Poll_Data'}
	return tables[pollType]

def intToRoman(num):
	if(num == ""):
		return ""
	# setup array with roman numerals
	roman = { '1' : 'I', '2' : 'II', '3' : 'III', '4' : 'IV', '5' : 'V',
				'6' : 'VI', '7' : 'VII', '8' : 'VIII', '9' : 'IX', '10' : 'X' }
	
	return roman[str(num)]

def isMultiActionPollType(pollType):
	if(pollType == 'Merit' or pollType == 'Promotion' or pollType=='Other'):
		return True
	else:
		return False