<?php
	session_start();
	require_once '../includes/connDB.php';
	require_once '../includes/functions.php';
	require_once "includes/sessionHandling.php";
	// if(!isAdmin()) {
 //        signOut();
 //    }

	// Start helper functions
	function intToRoman($num) {
        if($num=="") {
            return "";
        }
        $romanArr = array();
        $romanArr[1] = "I"; $romanArr[2] = "II"; $romanArr[3]= "III";
        $romanArr[4] = "IV"; $romanArr[5] = "V"; $romanArr[6]= "VI";
        $romanArr[7] = "VII"; $romanArr[8] = "VII"; $romanArr[9] = "IX";
        $romanArr[10] = "X";
        return $romanArr[$num];
    }
 //    function pollTypeToTable($pollType) {
	// 	switch($pollType) {
	// 		case "Merit":
	// 			return "Merit_Data";
	// 		case "Promotion":
	// 			return "Associate_Promotion_Data";
	// 		case "Reappointment":
	// 			return "Reappointment_Data";
	// 		case "Fifth Year Review":
	// 			return "Fifth_Year_Review_Data";
	// 		case "Fifth Year Appraisal":
	// 			return "Fifth_Year_Appraisal_Data";
	// 		case "Other":
	// 			return "Other_Poll_Data";
	// 		default:
	// 			return "";
	// 	}
	// }
    // function getActionCount($pollId) {
    //     global $conn;
    //     $actionCount = 0;
    //     $query = "SELECT count(action_num) FROM Poll_Actions WHERE poll_id=?";
    //     $stmt = mysqli_prepare($conn,$query) or die(mysqli_error($conn));
    //     mysqli_stmt_bind_param($stmt, "i", $pollId) or die(mysqli_error($conn));
    //     mysqli_stmt_execute($stmt) or die(mysqli_error($conn));
    //     mysqli_stmt_bind_result($stmt, $actionCount) or die(mysqli_error($conn));
    //     mysqli_stmt_fetch($stmt);
    //     mysqli_stmt_close($stmt);
    //     return $actionCount;
    // }

    // function getActionInfo($pollId) {
    //     global $conn;
    //     $actionInfoArray = array();
    //     $fromTitle = $fromStep = $toTitle = $toStep = $accelerated = "";

    //     $query = "SELECT fromTitle,fromStep,toTitle,toStep,accelerated FROM Poll_Actions WHERE poll_id=?";
    //     $stmt = mysqli_prepare($conn,$query) or die(mysqli_error($conn));
    //     mysqli_stmt_bind_param($stmt, "i", $pollId) or die(mysqli_error($conn));
    //     mysqli_stmt_execute($stmt) or die(mysqli_error($conn));
    //     mysqli_stmt_bind_result($stmt,$fromTitle,$fromStep,$toTitle,$toStep,$accelerated) or die(mysqli_error($conn));
    //     while(mysqli_stmt_fetch($stmt)) {
    //         $actionInfo = array( "fromTitle" => $fromTitle,
    //                         "fromStep" => $fromStep,
    //                         "toTitle" => $toTitle,
    //                         "toStep" => $toStep,
    //                         "accelerated" => $accelerated );
    //         $actionInfoArray[] = $actionInfo;
    //     }
    //     mysqli_stmt_close($stmt);
    //     return $actionInfoArray;
    // }
    function getActionDescriptions(&$pollData) {
    	// Poll types
    	$MERIT = "Merit";
    	$PROMOTION = "Promotion";
    	$OTHER = "Other";
    	// Description variables
    	$poll_id = $pollData['poll_id'];
		$pollType = $pollData['pollType'];
		$profName = $pollData['name'];
		$dept = $pollData['dept'];
		$effDate = $pollData['effDate'];
		$description = array('actionNum'=>0,'description'=>'');
		$descriptions = array();
		// Merit, Promotion, and Other poll types may have multiple actions
		if($pollType == $MERIT || $pollType == $PROMOTION || $pollType == $OTHER) {
			$actionInfo = getActionInfo($poll_id);
			$numActions = getActionCount($poll_id);
			$index = 0;
			while($index < $numActions) {
				$actionData = $actionInfo[$index];
				$fromTitle = $actionData['fromTitle'];
				$fromStep = $actionData['fromStep'];
				$toTitle = $actionData['toTitle'];
				$toStep = $actionData['toStep'];
				$accelerated = $actionData['accelerated'];
				if($accelerated) {
					$actionDescription = "Recommendation for $profName's Accelerated $pollType from $fromTitle $fromStep to $toTitle $toStep ";
					$actionDescription .= "in the $dept department, effective $effDate.";
					$descriptions[] = $actionDescription;
				} else {
					$actionDescription = "Recommendation for $profName's $pollType from $fromTitle $fromStep to $toTitle $toStep ";
					$actionDescription .= "in the $dept department, effective $effDate.";
					$descriptions[] = $actionDescription;
				}
				$index += 1;
			}
			return  $descriptions;
		} else { // rest of polls do not involve multiple actions
			$actionDescription = "Recommendation for $profName's $pollType in the $dept department, effective $effDate.";
			$descriptions[] = $actionDescription;
		}
		return $descriptions;
	}
	function getProfessorFormOptions($pollID) {
		global $conn;
		$options = array('assistant'=>0,'associate'=>0,'full'=>0);
		$query = "SELECT assistantForm, assistantEvaluationNum, associateForm, associateEvaluationNum, fullForm, fullEvaluationNum FROM Polls WHERE poll_id=$pollID";
		$result = $conn->query($query) or die($conn->error);
		$row = $result->fetch_assoc();
		$options['assistant'] = $row['assistantForm'];
		$options['assistantEvaluationNum'] = $row['assistantEvaluationNum'];
		$options['associate'] = $row['associateForm'];
		$options['associateEvaluationNum'] = $row['associateEvaluationNum'];
		$options['full'] = $row['fullForm'];
		$options['fullEvaluationNum'] = $row['fullEvaluationNum'];

		return $options;
	}
	function getVotingRestrictions($poll_id) {
		$EVALUATION_FORM = 3;
		$PROFESSOR_TITLE = "Users.title";
		$restrictions = null;
		$formOptions = getProfessorFormOptions($poll_id);
		if($formOptions['assistant'] == $EVALUATION_FORM) {
			$restrictions = "($PROFESSOR_TITLE !='Assistant Professor'";
		}
		if($formOptions['associate'] == $EVALUATION_FORM) {
			if(isset($restrictions)) {
				$restrictions .= " AND $PROFESSOR_TITLE !='Associate Professor'";
			} else {
				$restrictions = "($PROFESSOR_TITLE !='Associate Professor'";
			}
		}
		if($formOptions['full'] == $EVALUATION_FORM) {
			if(isset($restrictions)) {
				$restrictions .= " AND $PROFESSOR_TITLE != 'Full Professor'";
			} else {
				$restrictions = "($PROFESSOR_TITLE !='Full Professor'";
			}
		}
		if(isset($restrictions)) {
			$restrictions .= ') AND ';
		}

		return $restrictions;
	}
	function getVoteCounts($poll_id, $pollType) {
		global $conn;
		$VOTE_SET = 1;
		$FOR = 1;
		$AGAINST = 2;
		$ABSTAIN = 3;
		$SATISFACTORY_QUALIFICATIONS = 4;
		// Begin getting restrictions and eligible vote count
		$restrictions = getVotingRestrictions($poll_id);
		$eligible = getEligibleVoteCount($poll_id,$restrictions);
		$pollDataTable = getDataTable($pollType);
		// qualifications == a vote of : satisfactory with qualifications
		$multiActionVoteCounts = array("1"=>null,"2"=>null,"3"=>null);
		$voteCounts = array("for"=> 0, "qualifications"=>0, "eligible"=>$eligible,"against"=>0, "abstain"=>0, "total"=>0);
		$vote = $voteCount = $totalVotes = $multiActionVote = $actionNum = 0;
		// Get vote of all eligible participants in the vote identified by poll_id
		if($pollType == 'Merit' || $pollType == 'Promotion' || $pollType == 'Other') { // multi-action poll types
			for($actionNum=1; $actionNum <= 3; $actionNum++) {
				# select all data from multiaction data tables
				$stmt = "SELECT $pollDataTable.vote,$pollDataTable.action_num from $pollDataTable INNER JOIN Users ON Users.user_id=$pollDataTable.user_id WHERE $restrictions $pollDataTable.poll_id=$poll_id ";
				$stmt .= "AND $pollDataTable.action_num=$actionNum";
				#print "$stmt <hr>";
				# execute query
				$result = $conn->query($stmt) or die($conn->error);
				# use actionNum as index to store results
				$index = (string)$actionNum;
				# fetch results from database
				while($row = $result->fetch_assoc()) {
					#print_r($row); print "<hr>";
					# $multiActionVoteCounts[<action_num>] = <associative array of voting data>
					if($multiActionVoteCounts[$index] === NULL) {
						$multiActionVoteCounts[$index] = array("for"=> 0, "qualifications"=>0, "eligible"=>$eligible,"against"=>0, "abstain"=>0, "total"=>0);
					}
					$vote = $row['vote'];
					$multiActionVoteCounts[$index]['total'] += 1;
					if($vote == $FOR) {
						$multiActionVoteCounts[$index]['for'] += 1;
					} elseif($vote == $AGAINST) {
						$multiActionVoteCounts[$index]['against'] += 1;
					} elseif($vote == $ABSTAIN) {
						$multiActionVoteCounts[$index]['abstain'] += 1;
					} elseif($vote == $SATISFACTORY_QUALIFICATIONS) {
						$multiActionVoteCounts[$index]['qualifications'] += 1;
					}
				} // End of while(<fetch results>)
				#print_r($multiActionVoteCounts);
				$voteCounts = $multiActionVoteCounts;
			} // End of for(...)
		} else { // single action poll types
			$stmt = "SELECT $pollDataTable.vote, count($pollDataTable.vote) as voteCount FROM $pollDataTable INNER JOIN Users ON Users.user_id=$pollDataTable.user_id";
			$stmt .= " WHERE $restrictions $pollDataTable.poll_id=$poll_id GROUP BY $pollDataTable.vote";
			$result = $conn->query($stmt) or die($conn->error);
			while($row = $result->fetch_assoc()) {
				$vote = $row['vote'];
				$voteCounts['total'] += $row['voteCount'];
				if($vote == $FOR) {
					$voteCounts['for'] += 1;
				} elseif($vote == $AGAINST) {
					$voteCounts['against'] += 1;
				} elseif($vote == $ABSTAIN) {
					$voteCounts['abstain'] += 1;
				} elseif($vote == $SATISFACTORY_QUALIFICATIONS) {
					$voteCounts['qualifications'] += 1;
				}
			} // End of while(<fetch results>)
		} // End of else {...}
		//print_r($voteCounts);print "<hr>";
		return $voteCounts;
	}
	function getEligibleVoteCount($poll_id,$restrictions) {
		global $conn;
		$query = "SELECT count(Voters.user_id) AS Eligible FROM Voters INNER JOIN Users ON Users.user_id=Voters.user_id";
		$query = $query." WHERE $restrictions Voters.poll_id='$poll_id' GROUP BY Voters.poll_id";
		$result = $conn->query($query) or die($conn->error);
		$row = $result->fetch_assoc();

		return $row['Eligible'];
	}
	function printHeading($description,$format) {
		$date = date("m/d/y");
		$dateSplit = explode("/",$date);
		$month = getMonth($dateSplit[0]);
		$monthDateYear = $month . " $dateSplit[1], 20$dateSplit[2]";
		$DATE_HEADING = "DATE:";
		$REGARD_HEADING = "RE:";
		$NOTICE = "The following actions were voted seperately.";
		echo $format['rowOpen'];
			echo $format['columnSize1'];
				echo $DATE_HEADING;
			echo $format['divClose'];
			echo $format['columnSize11'];
				echo $monthDateYear;
			echo $format['divClose'];
			echo $format['columnSize1'];
				echo $REGARD_HEADING;
			echo $format['divClose'];
			echo $format['columnSize11'];
				echo $description[0];
			echo $format['divClose'];
			echo $format['columnSize1'];
			echo $format['divClose'];
			echo $format['columnSize11'];
				echo $NOTICE;
			echo $format['divClose'];
		echo $format['divClose'];
		echo $format['rowDivider'];
		echo $format['br'];

	}

	function getMonth($monthNum) {
		$months = array("01"=>"January","02"=>"Febuary","03"=>"March","04"=>"April","05"=>"May","06"=>"June");
		$months['07'] = "July";
		$months['08'] = "August";
		$months['09'] = "September";
		$months['10'] = "October";
		$months['11'] = "November";
		$months['12'] = "December";

		return $months[$monthNum];
	}

	function displayTallyOfVotes($options,$results,$actionNum,$multiActionPollType,&$format) {
		// results[key], key: for, against, abstain, qualifications, total
		//print "options:"; print_r($options);
		//print "results:"; print_r($results);
		// print "format:"; print_r($format);
		$SPACE = "&nbsp;&nbsp;";
		$SEPERATOR = "&nbsp;&nbsp;&nbsp;&nbsp;";
		$TALLY_OF_VOTES_NOTICE = "Tally of Votes: ";
		$fourVotingOptions = (count($options) == 4 ? 1 : 0);
		$actionNum = (string)$actionNum;
		//print_r($results); print "<hr>";
		# Begin displaying tally of votes
		echo $TALLY_OF_VOTES_NOTICE;

		echo $format['spanOpen'];
			if($multiActionPollType) {
				echo $results[$actionNum]['for'] . " ";
			} else {
			 	echo $results['for'] . " ";
			}
		echo $format['spanClose'];
		echo $options[0] . $SEPERATOR;
		// Four voting options 'for','qualifications', 'against', 'abstain'
		if($fourVotingOptions) {
			#print "action: $actionNum results: " . $results[$actionNum]['qualifications'];
			echo $format['spanOpen'];
				if($multiActionPollType) {
					echo $results[$actionNum]['qualifications'] . $SPACE;
				} else {
					echo $results['qualifications'] . $SPACE;
				}
			echo $format['spanClose'];
			echo $options[1] . $SEPERATOR;

			echo $format['spanOpen'];
				if($multiActionPollType) {
					echo $results[$actionNum]['against'] . $SPACE;
				} else {
					echo $results['against'] . $SPACE;
				}
			echo $format['spanClose'];
			echo $options[2] . $SEPERATOR;

			echo $format['spanOpen'];
				if($multiActionPollType) {
					echo $results[$actionNum]['abstain'] . $SPACE;
				} else {
					echo $results['abstain'] . $SPACE;
				}
			echo $format['spanClose'];
			echo $options[3] . $SEPERATOR;
		} else { // Three voting options 'for','against','abstain'
			echo $format['spanOpen'];
				if($multiActionPollType) {
					echo $results[$actionNum]['against'] . $SPACE;
				} else {
					echo $results['against'] . $SPACE;
				}
			echo $format['spanClose'];
			echo $options[1] . $SEPERATOR;

			echo $format['spanOpen'];
				if($multiActionPollType) {
					echo $results[$actionNum]['abstain'] . $SPACE;
				} else {
					echo $results['abstain'] . $SPACE;
				}
			echo $format['spanClose'];
			echo $options[2] . $SEPERATOR;
		}
	}

	// function getVotingOptions($pollType,$otherVotingOptionsSet=0) {
	// 	$positiveOpposedAbstain = array("Positive","Positive w/ Qualifications","Opposed","Abstain");
	// 	$inFavorOpposedAbstain = array("In favor","Opposed","Abstain");
	// 	$satisfactory = array("Satisfactory","Unsatisfactory","Abstain");
	// 	$satisfactoryWQualifications = array("Satisfactory","Satisfactory with Qualifications","Unsatisfactory","Abstain");
	// 	if($otherVotingOptionsSet) {
	// 		$key = $otherVotingOptionsSet - 1;
	// 		$otherVotingOptions[] = $inFavorOpposedAbstain;
	// 		$otherVotingOptions[] = $satisfactory;
	// 		$otherVotingOptions[] = $satisfactoryWQualifications;

	// 		return $otherVotingOptions[$key];
	// 	}
	// 	// Keys are the output of the pollTypeToTable() function, values are the votiing options
	// 	$options = array("merits"=>$inFavorOpposedAbstain,
	// 						"promotions" => $inFavorOpposedAbstain,
	// 						"reappointments" => $inFavorOpposedAbstain,
	// 						"fifth_year_reviews" =>$satisfactoryWQualifications,
	// 						"fifth_year_appraisals" => $positiveOpposedAbstain
	// 					 );
	// 	$key = getDataTable($pollType);
	// 	return $options[$key];
	// }
	function printReviewComments($comments,&$format) {
		$QUALIFICATIONS = "qualificationsCmt";
		$COMMENT = 'voteCmt';
		$printCount = 0;
		for($x=0; $x < count($comments); $x++) {
			$qualificationsCmt = $comments[$x][$QUALIFICATIONS];
			$comment = $comments[$x][$COMMENT];

			$qualificationsCmt = trim($qualificationsCmt);
			$comment = trim($comment);

			if(strlen($qualificationsCmt) > 0 || strlen($comment) > 0) {
				$printCount += 1;
				echo $format['commentColumnSize2'];
					echo $format['commentNumber'] . $printCount . ")";
				echo $format['pClose'];
				echo $format['commentColumnSize9'];
					if(strlen($qualificationsCmt) > 0 && strlen($comment) > 0) {
						echo "Qualifications comment(s): $qualificationsCmt";
						echo $format['br'];
						echo "Vote comment: $comment";
					} elseif(strlen($qualificationsCmt) > 0) {
						echo "Qualifications comment(s): $qualificationsCmt";
					} else {
						echo "Vote comment: $comment";
					}
				echo $format['pClose'];
			}
		}
	}
	function printAppraisalComments($comments,&$format) {
		$RESEARCH = 'researchCmts';
		$TEACHING = 'teachingCmts';
		$PUBLIC = 'pubServiceCmts';
		$printCount = 0;
		//print "Comments: "; print_r($comments);
		for($x=0; $x < count($comments); $x++) {
			$researchCmts = $comments[$x][$RESEARCH];
			$teachingCmts = $comments[$x][$TEACHING];
			$pubServiceCmts = $comments[$x][$PUBLIC];

			$researchCmts = trim($researchCmts);
			$teachingCmts = trim($teachingCmts);
			$pubServiceCmts = trim($pubServiceCmts);

			if(strlen($researchCmts) > 0 || strlen($teachingCmts) > 0 || strlen($pubServiceCmts) > 0) {
				$printCount += 1;
				echo $format['commentColumnSize2'];
					echo $format['commentNumber'] . $printCount . ")";
				echo $format['pClose'];
				echo $format['commentColumnSize9'];
					echo "Research comment(s): $researchCmts";
					echo $format['br'];
					echo "Teaching comment(s): $teachingCmts";
					echo $format['br'];
					echo "Public service comment(s): $pubServiceCmts";
				echo $format['pClose'];
			}
		}
		if($printCount == 0) {
			echo $format['noCommentsNotice'];
		}
	}

	function printNormalComments($comments,&$format) {
		$printCount = 0;
		for($x = 0; $x < count($comments); $x++) {
			$comment = $comments[$x]['voteCmt'];
			//print "C: $comment";
			$comment = trim($comment);

			if(strlen($comment) > 0) {
				$printCount += 1;
				echo $format['commentColumnSize2'];
					echo $format['commentNumber'] . $printCount . ")";
				echo $format['pClose'];
				echo $format['commentColumnSize9'];
					echo $comment;
				echo $format['pClose'];
			}
		}
		if($printCount == 0) {
			echo $format['noCommentsNotice'];
		}
	}

	function getComments($poll_id,$pollType,$actionNum=0,$confidentialEvals) {
		# Variables
		global $conn;
		$FALSE = 0;
		$COMMENT = "voteCmt";
		$comments = array();
		$evaluationRestrictions = $multiActionPollType = "";
		# Get data table name
		$dataTable = getDataTable($pollType);
		if($confidentialEvals) {
			$dataTable = "confidential_evals";
		}
		if($pollType == 'Merit' || $pollType == 'Promotion' || $pollType == 'Other') {
			$multiActionPollType = 1;
		}
		# Get comments
		//print "CE: $confidentialEvals";
		if($confidentialEvals == $FALSE && $multiActionPollType) { // multiaction poll types
			$stmt = "SELECT $dataTable.action_num,$dataTable.voteCmt FROM $dataTable WHERE $dataTable.poll_id=$poll_id";
			$result = $conn->query($stmt) or die($conn->error);
			$actionNum = $comment = "";
			// create an associative array $comments[$actionNum] = array(<vote_comments>), if necessary,
			// otherwise, add comments to indexed array
			while($row = $result->fetch_assoc()) {
				$actionNum = (string)$row['action_num'];
				if(isset($comments[$actionNum])) {
					$comments[$actionNum] = array();
				}
				// Stored this way for compatability w/ printNormalComments(...)
				$comments[$actionNum][] = array($COMMENT=>$row['voteCmt']);
			}
		} else { // Confidential evals and single action poll types handled here
			if($confidentialEvals) {
				$evaluationRestrictions = getEvaluationRestrictions($poll_id,$multiActionPollType,$actionNum);
				$stmt = "SELECT voteCmt FROM {$dataTable} INNER JOIN Users on Users.user_id=confidential_evals.user_id WHERE $evaluationRestrictions confidential_evals.poll_id={$poll_id}";
			} else {
				$stmt = "Select * from $dataTable WHERE poll_id=$poll_id";
			}
			$result = $conn->query($stmt) or die($conn->error);
			while($comment = $result->fetch_assoc()) {
				$comments[] = $comment;
			}
		}
		// print "comments: "; print_r($comments);
		return $comments;
	}

	function printResults(&$pollData) {
		// Variables used to format results
		// $HEADING_SPACING = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; // 8 spaces(2tabs) for text formating
		// $HTML_SPACE = "&nbsp;";
		$DIV_ROW_DIVIDER = "<div class='col-md-12'><p class='col-md-10' style='border-bottom-style: solid; border-bottom-color: #999999; margin-left: 80px; margin-right: 80px;'></p></div>";
		$formatting = array('columnSize11'=>"<div class='col-md-11' style='margin-bottom: 20px;'>",'columnSize1'=>"<div class='col-md-1'>",'rowDivider'=>$DIV_ROW_DIVIDER,'rowOpen'=>"<div class='row' style='margin-left: 80px; margin-right: 80px;'>");
		$formatting['divClose'] = "</div>";
		$formatting['spanOpen'] = "<span style='text-decoration:underline'>";
		$formatting['spanClose'] = "</span>";
		$formatting['br'] = "<br>";
		$formatting['pClose'] = "</p>";
		$formatting['emptyColumnSize1'] = "<div class='col-md-1'></div>";
		$formatting['commentColumnSize2'] = "<p class='col-md-2' style='padding-left: 0px; padding-right: 0px;'>";
		$formatting['commentColumnSize9'] = "<p class='col-md-9' style='padding-left: 0px;'>";
		$formatting['noCommentsNotice'] = "No comments were provided.";
		$formatting['commentNumber'] = "Comment #";
		$PRINT_EVALUATIONS = 1;
		// $paragraphSpacing = $HEADING_SPACING;
		$actionDescriptions = getActionDescriptions($pollData);
		printHeading($actionDescriptions,$formatting);
		for($x=0; $x < count($actionDescriptions); $x++) {
			$actionNum = $x + 1;
			$description = $actionDescriptions[$x];
			// Print actionn results
			printActionResults($description, $actionNum,$formatting,$pollData);
			// Print action evaluations
			printActionResults($description,$actionNum,$formatting,$pollData,$PRINT_EVALUATIONS);
		}
	}
	function getEvaluationRestrictions($poll_id,$multiActionPollType,$actionNum) {
		// Comments
		$EVALUATION = 3;
		// Variables
		$selection = null;
		// $formOptions[$key]; keys: assistant, assistantEvaluationNum, associate, full
		$formOptions =getProfessorFormOptions($poll_id);
		// print "poll_id: $poll_id multiActionPollType: $multiActionPollType actionNum: $actionNum <br>";
		// print "formOptions: "; print_r($formOptions); print "<br>";
		// If this is a multiaction poll type then the evaluation number must match the action number,
		// Otherwise evaluations may still be made for single action votes as long as the professor's form = evaluation
		if($multiActionPollType) {
			if($formOptions['assistant'] == $EVALUATION && $formOptions['assistantEvaluationNum'] == $actionNum ) {
				// print "poll_id: $poll_id multiActionPollType: $multiActionPollType actionNum: $actionNum <br>";
				// print "formOptions: "; print_r($formOptions); print "<br>";
				$selection = "(Users.title='Assistant Professor'";
			}
			if($formOptions['associate'] == $EVALUATION && $formOptions['associateEvaluationNum'] == $actionNum) {
				if(isset($selection)) {
					$selection .= " || Users.title='Associate Professor'";
				} else {
					$selection = "(Users.title='Associate Professor'";
				}
			}
			if($formOptions['full'] == $EVALUATION && $formOptions['fullEvaluationNum'] == $actionNum) {
				if(isset($selection)) {
					$selection .= " || Users.title='Full Professor'";
				} else {
					$selection = "(User.title='Full Professor'";
				}
			}
		} else { // Single action poll types
			if($formOptions['assistant'] == $EVALUATION) {
				// print "poll_id: $poll_id multiActionPollType: $multiActionPollType actionNum: $actionNum <br>";
				$selection = "(Users.title='Assistant Professor'";
			}
			if($formOptions['associate'] == $EVALUATION) {
				if(isset($selection)) {
					$selection .= " || Users.title='Associate Professor'";
				} else {
					$selection = "(Users.title='Associate Professor'";
				}
			}
			if($formOptions['full'] == $EVALUATION) {
				if(isset($selection)) {
					$selection .= " || Users.title='Full Professor'";
				} else {
					$selection = "(User.title='Full Professor'";
				}
			}
		}
		if(isset($selection)) {
			$selection .= ") AND ";
		}
		return $selection;
	}
	function checkForEvaluations($poll_id,$multiActionPollType,$actionNum) {
		// Set up variables
		global $conn;
		$eligibleCount = $actionEvaluationNum = $commentCount = 0;
		// Get evaluation restrictions
		$evaluationRestrictions = getEvaluationRestrictions($poll_id,$multiActionPollType,$actionNum);
		if($evaluationRestrictions) {
			// Select all members of the current vote that have been selected to place comments, but not to cast a vote
			$stmt = "SELECT count(Voters.user_id) as Eligible FROM Voters INNER JOIN Users on Users.user_id=Voters.user_id WHERE $evaluationRestrictions Voters.poll_id=$poll_id";
			$result = $conn->query($stmt) or die($conn->error);
			$row = $result->fetch_assoc();
			$eligibleCount = $row['Eligible'];
			// Get comment count for current action
			$stmt = "SELECT count(confidential_evals.voteCmt) AS commentCount FROM confidential_evals INNER JOIN Users on Users.user_id=confidential_evals.user_id WHERE $evaluationRestrictions confidential_evals.poll_id=$poll_id";
			$result = $conn->query($stmt) or die($conn->error);
			$row = $result->fetch_assoc();
			$commentCount = $row['commentCount'];
			// Return number of participants eligible to leave comments on the current vote/ballot/poll
			// also return the action evaluation number
			$evaluationInfo = array('eligible' => $eligibleCount,
									'commentCount' => $commentCount);
		} else {
			$evaluationInfo = array('eligible' => 0, 'commentCount' => 0);
		}

		return $evaluationInfo;
	}

	function printActionResults($description,$actionNum,$format,&$pollData,$confidentialEvals=0) {
		$VOTES_HEADING = "Votes - (to be recorded on Dept. Letter)";
		$COMMENTS_NOTICE = "(Comments are transcribed from ballots as written. Whether comments were discussed at the meeting ";
		$COMMENTS_NOTICE .= "and therefore included in the department letter is up to the chair to determine.)";
		$EVALUATIONS_HEADING = "Confidential Evaluations - (to be recorded on Dept. Letter)";
		$TOTAL_ELIGABLE_NOTICE = "Total Eligible voting members: ";
		$TRUE = 1;
		$FALSE = 0;
		$multiActionPollType = $printEvaluations = $FALSE;
		$evaluationInfo = $evaluationActionNum = $eligibleEvalsCount = $options= NULL;
		// Get voting options
		if($pollData['pollType'] == 'Other') {
			$options = getVotingOptions($pollData['pollType'],$pollData['votingOptions']);
		} else {
			$options = getVotingOptions($pollData['pollType']);
		}

		// Get vote counts
		// $results = getVotingResults($pollData['poll_id'],$pollData['pollType']);
		$results = getVoteCounts($pollData['poll_id'],$pollData['pollType']);
		// Determine if multiaction poll type
		$pollType = $pollData['pollType'];
		if($pollType === 'Merit' || $pollType == 'Promotion' || $pollType == 'Other') {
			$multiActionPollType = $TRUE;
		}
		// Check if this action has confidential evaluations to display
		if($confidentialEvals) {
			$evaluationInfo = checkForEvaluations($pollData['poll_id'],$multiActionPollType,$actionNum);
			// print "Evaluation info: "; print_r($evaluationInfo); print "<hr>";
			if($evaluationInfo['commentCount'] > 0) {
				$eligibleEvalsCount = $evaluationInfo['eligible'];
				$printEvaluations = $TRUE;
			}
		}
		// Start displaying results
		//for($actionNum = 0; $actionNum < count($descriptions); $actionNum++) {
		$romanActionNum = intToRoman($actionNum);
		if($confidentialEvals) {
			$romanActionNum .= 'a';
		}
		// Only enter the printing section if printing standard results or if there are confidential evaluations to display
		if($confidentialEvals == $FALSE || $printEvaluations == $TRUE) {
			// Open row - Start displaying action results
			echo $format['rowOpen'];
				// Display action number
				echo $format['columnSize1'];
					echo "$romanActionNum.";
				echo $format['divClose'];
				// Display heading
				echo $format['columnSize11'];
					if($confidentialEvals) {
						echo $EVALUATIONS_HEADING;
					} else {
						echo $VOTES_HEADING;
					}
				echo $format['divClose'];
				// Display action description
				echo $format['emptyColumnSize1'];
				echo $format['columnSize11'];
					echo $description;
				echo $format['divClose'];
				// Display eligible vote count
				echo $format['emptyColumnSize1'];
				echo $format['columnSize11'];
					echo $TOTAL_ELIGABLE_NOTICE;
					if($confidentialEvals) {
						echo $eligibleEvalsCount;
					} else {
						if($multiActionPollType) {
							$index = (string)$actionNum;
							echo $results[$index]['eligible'];
						} else { // Single action poll type
							echo $results['eligible'];
						}
					} // End of else
				echo $format['divClose'];
				if($confidentialEvals == $FALSE) { // confidential evaluations do not have
					// Display tally
					echo $format['emptyColumnSize1'];
					echo $format['columnSize11'];
						displayTallyOfVotes($options,$results,$actionNum,$multiActionPollType,$format);
					echo $format['divClose'];
				}
				// Display notice about comments
				echo $format['emptyColumnSize1'];
				echo $format['columnSize11'];
					echo $COMMENTS_NOTICE;
				echo $format['divClose'];
				// Display comments of the current action
				echo $format['emptyColumnSize1'];
				echo $format['columnSize11'];
					printComments($pollData['poll_id'],$pollData['pollType'],$actionNum,$format,$confidentialEvals);
				echo $format['divClose'];
			echo $format['divClose'];
			// Close row - End action results
			echo $format['rowDivider'];
		} // End of if(...)

	}

	function printComments($poll_id,$pollType,$actionNum,&$format,$confidentialEvals) {
		$comments = getComments($poll_id,$pollType,$actionNum,$confidentialEvals);
		$actionNum = (string)$actionNum;
		if($confidentialEvals) {
			//print "Comments: "; print_r($comments); print "<hr>";
			printNormalComments($comments,$format);
		} elseif($pollType == "Reappointment") {
			printNormalComments($comments,$format);
		} elseif($pollType == "Fifth Year Review") {
			printReviewComments($comments,$format);
		} elseif($pollType == "Fifth Year Appraisal") {
			printAppraisalComments($comments,$format);
		} elseif($pollType == 'Merit' || $pollType == 'Promotion' || $pollType == 'Other') {
			printNormalComments($comments[$actionNum],$format);
		}
	}

	function updatePollData(&$pollData) {
		$depts = getDepartments();
		$pollTypes = getPollTypes();
		$pollData['dept'] = $depts[$pollData['dept']];
		$pollData['pollType'] = $pollTypes[$pollData['pollType']];
	}

?>
<html>
<head>
<title>View Results</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<style></style>
</head>
<body>
<nav class="navbar navbar-default">
	<div class="container-fluid">
		<div class="navbar-header">
			<a class="navbar-brand" href="../home.php">BCOE Voting</a>
		</div>
		<ul class="nav navbar-nav">
			<li><a href="home.php">Home</a></li>
			<li><a href="vote.php">Create Poll</a></li>
			<li><a href="edit.php">Edit Poll</a></li>
			<li class="active"><a href="review.php">Review Poll</a></li>
			<li><a href="add.php">Add User</a></li>
		</ul>
	</div>
</nav>
<?php
	// This is the Main php control selection
	// From here, the rest of the page is displayed
	$pollData = $descriptions = "";
	date_default_timezone_set("America/Los_Angeles");
	// Capture post and display results
	if($_SERVER["REQUEST_METHOD"] == "POST") {
		// print_r($_POST);
		if(isset($_POST["encodedPollData"])) {
			$pollData = json_decode($_POST['encodedPollData'],true);
			updatePollData($pollData);
			echo "<div class='container well'>";
			printResults($pollData);
			echo "</div>";
		}
	}
?>
</body>
</html>
