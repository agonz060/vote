<?php
	require_once '../includes/connDB.php';
	// capture post data here
	if($_SERVER["REQUEST_METHOD"] == "POST") {
		$action = array();
		$option = array();
		$addOption = false;
		$addOptionNotice = false;

		if(isset($_POST['action'])) {
			$query = '';
			$action = $_POST['action'];
			$tableInfo = getTableInfo($action['optionType']);

			if($action['actionType'] == 'add') {
				// add flag for retreiving option info after insert
				$addOption = true;
				if($action['optionType'] == 'notice') {
					// extract option
					$option = $action['option'];
					// clean user input before insert
					$option['noticeName'] = mysqli_real_escape_string($conn,$option['noticeName']);
					$option['notice'] = mysqli_real_escape_string($conn,$option['notice']);
					// create insert query
					$query = 'INSERT INTO notices(type,notice) ';
					$query .= 'VALUES("' . $option['noticeName'] . '","' . $option['notice'] . '")';
				} else {
					// clean user input
					//var_dump($action['option']);
					$action['option'] = mysqli_real_escape_string($conn,$action['option']);
					// create insert query
					$query = 'INSERT INTO ' . $tableInfo['table'] . '(' . $tableInfo['field'] . ') ';
					$query .= 'VALUES("' . $action['option'] . '")';
				}
			} else if($action['actionType'] == 'remove') {
				$query = 'DELETE FROM ' . $tableInfo['table'] . ' WHERE ' . $tableInfo['id'];
				$query .= '=' . $action['option'];
				// echo 'Delete query: ' . $deleteQuery;
			}
			// execute query to either add or remove
			if($result = mysqli_query($conn,$query)) {
					$newOptionInfo['status'] = "success";
					if($addOption) {
						$selectNewOptionQuery = "SELECT {$tableInfo['id']} FROM {$tableInfo['table']} ";
						$selectNewOptionQuery .= "ORDER BY {$tableInfo['id']} DESC LIMIT 1";
						if($result = mysqli_query($conn,$selectNewOptionQuery)) {
							$row = $result->fetch_assoc();
							$newOptionInfo['id'] = $row[$tableInfo['id']];
						}
					}
					echo json_encode($newOptionInfo);
			} else {
				$error['status'] = 'error';
				$error['msg'] = mysqli_error($conn);
				echo json_encode($error);
			}
		}// else if(isset($_POST['update'])) {
		// 	$update = $_POST['update'];
		// 	if($update['optionType'] == 'notice') {
		// 		updateNoticesSelectOptions($update['part'],$conn);
		// 	} else {
		// 		updateSelectOptions($update['optionType'],$conn);
		// 	}
		// }
	}

	// // update the select options for notices
	// // notice update happens in 2 parts:
	// // part = 1 -> update notice names (type)
	// // part = 2 -> update notice text
	// function updateNoticesSelectOptions($part,$conn) {
	// 	$tableInfo = getTableInfo('notice');
	// 	$update = '';
	// 	if($part == 1) {
	// 		$selectQuery = "SELECT n_id,type FROM notices ORDER BY n_id ASC";
	// 		if($result = mysqli_query($conn,$selectQuery)) {
	// 			while($row = $result->fetch_assoc()) {
	// 				$update .= "<option id=\"{$row['n_id']}\">{$row['type']}</option>";
	// 			}
	// 		} else {
	// 			echo "<option>".mysqli_error($conn)."</option>";
	// 		}
	// 	} else {
	// 		$selectQuery = "SELECT n_id,notice FROM notices ORDER BY n_id ASC";
	// 		if($result = mysqli_query($conn,$selectQuery)) {
	// 			while($row = $result->fetch_assoc()) {
	// 				$update .= "<option id=\"{$row['n_id']}\">{$row['notice']}</option>";
	// 			}
	// 			echo $update;
	// 		} else {
	// 			echo "<option>".mysqli_error($conn)."</option>";
	// 		}
	// 	}
	// }

	// function updateSelectOptions($optionType) {
	// 	$tableInfo = getTableInfo($optionType);
	// 	$selectQuery = "SELECT {$tableInfo['id']},{$tableInfo['fields']} FROM {$tableInfo['table']} ORDER BY ";
	// 	$selectQuery .= "{$tableInfo['id']} ASC";
	// 	$update = '';

	// 	if($result = mysqli_query($conn,$selectQuery)) {
	// 		$id = $tableInfo['id'];
	// 		while($row = $result->fetch_assoc()) {
	// 			$update .= "<option value=\"{$row[$id]}\">{$row['table_fields']}</option>";
	// 		}
	// 		echo $update;
	// 	} else {
	// 		echo "<option>".mysqli_error($conn)."</option>";
	// 	}
	// }

	function getTableInfo($optionType) {
		$info = array();
		switch($optionType) {
			case 'title':
				$info['table'] = 'titles';
				$info['field'] = 'title';
				$info['id'] = 't_id';
				break;
			case 'dept':
				$info['table'] = 'departments';
				$info['field'] = 'department';
				$info['id'] = 'd_id';
				break;
			case 'poll_type':
				$info['table'] = 'poll_types';
				$info['field'] = 'poll_type';
				$info['id'] = 'p_id';
				break;
			case 'notice':
				$info['table'] = 'notices';
				$info['field'] = 'type,notice';
				$info['id'] = 'n_id';
				break;
			case 'voting_options':
				$info['table'] = 'voting_options';
				$info['field'] = 'options';
				$info['id'] = 'v_id';
				break;
		}
		return $info;
	}