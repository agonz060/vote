<?php
	require_once '../../includes/functions.php';

	$options = array();

	$options['pollTypes'] = getPollTypes();
	$options['titles'] = getTitles();
	$options['depts'] = getDepartments();
	$options['notices'] = getNotices();

	$options = json_encode($options);

	echo $options;