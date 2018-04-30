def getPollData(pollId,conn):
	# create cursor to execute mysql queries
	cur = conn.cursor(dictionary=True)
	# select all poll information
	selectStmt = ("SELECT * FROM Polls WHERE poll_id=%s")
	cur.execute(selectStmt,(pollId,))
	pollData = cur.fetchone()
	# close connection, no longer needed
	cur.close()
	if(isMultiActionPollType(pollData['pollType'])):
		actionInfo = getActionInfo(pollId,conn)
		pollData['actionInfo'] = actionInfo['actionInfo']
		pollData['actionCount'] = actionInfo['actionCount']
	return pollData

def getActionInfo(pollId,conn):
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

def getEligibleVoteCount(pollData,conn):
	# setup cursor to execut command
	cur = conn.cursor(dictionary=True)
	# get restrictions
	restrictions = getVotingRestrictions(pollData)
	selectStmt = 'SELECT count(Voters.user_id) AS eligible FROM Voters INNER JOIN Users ON Users.user_id=Voters.user_id'
	selectStmt = selectStmt + '{0} Voters.poll_id={1} GROUP BY Voters.poll_id'.format(restrictions,pollData['poll_id'])
	# execute select query
	cur.execute(selectStmt)
	row = cur.fetchone()
	# close connection
	cur.close()
	return row['eligible']

def getVoteCounts(pollData,conn):
	# voting options
	FOR = 1
	AGAINST = 2
	ABSTAIN = 3
	SATISFACTORY_QUALIFICATIONS = 4
	# get database information
	restrictions = getVotingRestrictions(pollData)
	eligible = getEligibleVoteCount(pollData,conn)
	pollDataTable = pollTypeToTable(pollData['pollType'])
	# setup vote count containers
	actionNum = vote = ''
	voteCounts = {}
	multiActionVoteCounts = {'1': None, '2': None, '3': None}
	# setup cursor for executing queries
	cur = conn.cursor
	# get vote counts starting with multi-action poll types
	if(isMultiActionPollType(pollData['pollType'])):
		for actionNum in range(1,4):
			# create select statements
			selectStmt = 'SELECT {0}.vote,{0}.action_num FROM {0} INNER JOIN Users ON Users.user_id={0}.user_id WHERE {1} {0}.poll_id={2}'.format(pollDataTable,restrictions,pollData['poll_id'])
			selectStmt = selectStmt + ' AND {0}.action_num={1}'.format(pollDataTable,actionNum)
			# get results
			cur.execute(selectStmt)
			for row in cur:
				actionNum = str(row['actionNum'])
				vote = row['vote']
				# returns None if multiActionVoteCounts['actionNum'] does not exist
				if(multiActionVoteCounts.get(actionNum,None) == None):
					multiActionVoteCounts[actionNum] = 
					# value exists
					if(vote == FOR):
						multiActionVoteCounts[actionNum]['for'] += 1
					elif(vote == AGAINST): 
						multiActionVoteCounts[actionNum]['against'] += 1
					elif(vote == ABSTAIN): 
						multiActionVoteCounts[actionNum]['abstain'] += 1
					elif(vote == SATISFACTORY_QUALIFICATIONS):
						multiActionVoteCounts[actionNum]['qualifications'] += 1
				




def pollTypeToTable(pollType):
	tables = {'Merit' : 'Merit_Data', 'Promotion' : 'Associate_Promotion_Data',
				'Reappointment' : 'Reappointment_Data', 'Fifth Year Review' : 'Fifth_Year_Review_Data',
				'Fifth Year Appraisal' : 'Fifth_Year_Appraisal_Data', 'Other' : 'Other_Poll_Data'}
	return tables[pollType]

def intToRoman(num):
	if(num == ""):
		return ""
	# setup array with roman numerals
	roman = []
	roman.insert(1,'I'); roman.insert(2,'II'); roman.insert(3,'III'); roman.insert(4,'IV'); roman.insert(5,'V'); 
	roman.insert(6,'VI'); roman.insert(7,'VII'); roman.insert(8,'VIII'); roman.insert(9,'IX')
	return roman[num]

def isMultiActionPollType(pollType):
	if(pollType == 'Merit' or pollType == 'Promotion' or pollType=='Other'):
		return True
	else:
		return False