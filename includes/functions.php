<?php

	function getActionCount($pollId) {
        global $conn;
        $actionCount = 0;
        $query = "SELECT count(action_num) FROM Poll_Actions WHERE poll_id=?";
        $stmt = mysqli_prepare($conn,$query) or die(mysqli_error($conn));
        mysqli_stmt_bind_param($stmt, "i", $pollId) or die(mysqli_error($conn));
        mysqli_stmt_execute($stmt) or die(mysqli_error($conn));
        mysqli_stmt_bind_result($stmt, $actionCount) or die(mysqli_error($conn));
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
        return $actionCount;
    }

    function getActionInfo($pollId) {
        global $conn;
        $actionInfoArray = array();
        $fromTitle = $fromStep = $toTitle = $toStep = $accelerated = "";

        $query = "SELECT fromTitle,fromStep,toTitle,toStep,accelerated FROM Poll_Actions WHERE poll_id=?";
        $stmt = mysqli_prepare($conn,$query) or die(mysqli_error($conn));
        mysqli_stmt_bind_param($stmt, "i", $pollId) or die(mysqli_error($conn));
        mysqli_stmt_execute($stmt) or die(mysqli_error($conn));
        mysqli_stmt_bind_result($stmt,$fromTitle,$fromStep,$toTitle,$toStep,$accelerated) or die(mysqli_error($conn));

        $actionNum = 1;
        while(mysqli_stmt_fetch($stmt)) {
            $actionInfo = array( "fromTitle" => $fromTitle,
                            "fromStep" => $fromStep,
                            "toTitle" => $toTitle,
                            "toStep" => $toStep,
                            "accelerated" => $accelerated );
            $actionInfoArray[$actionNum] = $actionInfo;
            $actionNum++;
        }
        mysqli_stmt_close($stmt);
        return $actionInfoArray;
    }

	function getNotices () {
        global $conn;
        $notices = array();

        $getNotices = "SELECT n_id,notice FROM notices";
        $result = mysqli_query($conn,$getNotices);
        if($result) {
            while($row = $result->fetch_assoc()) {
                $notices[$row['n_id']] = $row['notice'];
            }
        }
        return $notices;
    }

    function getPollTypes() {
        global $conn;
        $pollTypes = array();

        if(!empty($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
        } else {
            $msg = 'edit.php: error - user_id not set. Redirecting to log in..';
            // alertMsg($msg);
            signOut();
        }

        $getTypes = "SELECT p_id,poll_type FROM poll_types ORDER BY p_id ASC";
        $result = mysqli_query($conn,$getTypes);
        if($result) {
            while($row = $result->fetch_assoc()) {
                $pollTypes[$row['p_id']] = $row['poll_type'];
            }
        }
        return $pollTypes;
    }

    function getDepartments() {
    	global $conn;
    	$depts = array();
    	$getDeptQuery = "SELECT d_id,department from departments";
    	$result = mysqli_query($conn,$getDeptQuery);
    	if($result = mysqli_query($conn,$getDeptQuery)) {
    		while($row = $result->fetch_assoc()) {
    			$depts[$row['d_id']] = $row['department'];
    		}
    	}
    	return $depts;
    }

    function getNewPollIDs() {
        global $conn;
        $ids = array();
        $user_id = "";

        if(!empty($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
        } else {
            $msg = 'edit.php: error - user_id not set. Redirecting to log in..';
            alertMsg($msg);
            signOut();
        }

        $SELECTCMD = "SELECT poll_id FROM Voters WHERE user_id=$user_id ";
        $SELECTCMD .= "AND voteFlag=0";
        $result = mysqli_query($conn,$SELECTCMD);
        if($result) {
            while($row = $result->fetch_assoc()) {
                $ids[] = $row['poll_id'];
            }
            return $ids;
        } else { // Error executing select command
            return -1;
        }
    }

     function getOldPollIDs() {
        global $conn;
        $ids = array();
        $user_id = "";

        if(!empty($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
        } else {
            $msg = 'review.php: error - user_id not set. Redirecting to log in...';
            alertMsg($msg);
            signOut();
            return -1;
        }

        // Get all polls the user has voted in or polls the user was included in
        // but did not participate in
        $SELECTCMD = "SELECT poll_id FROM Voters WHERE (user_id=$user_id ";
        $SELECTCMD .= "AND voteFlag=1)";
        $result = mysqli_query($conn,$SELECTCMD);
        if($result) {
            while($row = $result->fetch_assoc()) {
                $ids[] = $row['poll_id'];
            }
            //echo "ids: "; print_r($ids);
            return $ids;
        } else { // Error executing select command
            return -1;
        }
    }

    function getDataTable($pollType) {
        // Replace spaces for indexing purposes
        $pollType = str_replace(' ','_',$pollType);
        // Create array of database tables with the pollType as an index
        $tables = array('Fifth_Year_Review' => 'fifth_year_reviews',
                        'Fifth_Year_Appraisal' => 'fifth_year_appraisals',
                        'Merit' => 'merits',
                        'Promotion' => 'promotions',
                        'Reappointment' => 'reappointments',
                        'Other' => 'other_polls');
        // Return table
        return $tables[$pollType];
    }
