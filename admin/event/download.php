<?php
	
	function getFileName($title,$resultId) {
		// variables
		$fileName = explode(' ',$title);
		$fileNamePt1 = preg_replace("#[[:punct:]]#","",$fileName[0]);
		$fileNamePt2 = preg_replace("#[[:punct:]]#","",$fileName[1]);

		$fileName = $fileNamePt1 . '_' . $fileNamePt2;
		$fileName .= '_results' . $resultId . '.docx';
		return $fileName;
	}

	function getDocxCreatorPath($workingDir) {
		return $workingDir . '/../createDocx/createDocx.py';
	}

	function getResultsPath($workingDir) {
		return $workingDir . '/../createDocx/tmp/';
	}

	# capture poll_id and poll title
	if($_SERVER["REQUEST_METHOD"] == "POST") {
		// print_r($_POST);
		// constants
		$SUCCESS = 0;

		// variables
		$pollId = $_POST['poll_id'];
		$resultId = $_POST['resultId'];
		$title = $_POST['title'];
		$workingDir = getcwd();
		// print('workingDir: ' . $workingDir);
		// get current location
		$docxCreator = getDocxCreatorPath($workingDir);
		$fileName = getFileName($title,$pollId);

		// execute command store result
		$cmd = $docxCreator . ' ' . $pollId . ' ' . $fileName;

		$filePath = $output = $code = null;
		exec($cmd,$output,$code);

		if($code == $SUCCESS) {
			$filePath = getResultsPath($workingDir) . $fileName;
			# print HTML headers so browser will treat file as a download, instead of executing file (as is standard while file located in cgi-bin)
			header("Content-Type:application/x-download\n");
			header("Content-Disposition: attachment; filename=$fileName\n\n");
			readfile($filePath);
		} else {
			print('Something went wrong: '); print_r($output);
		}


	}
?>