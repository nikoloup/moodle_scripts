<?php

// to be able to run this file as command line script
define('CLI_SCRIPT', true);

require_once('config.php');
require_once($CFG->dirroot . '/course/lib.php');

$handle = fopen('empty_courses.txt','r');

while(!feof ($handle))
{
	$line = fgets($handle);
	$id = intval($line);
	$course = get_course($id);
	print $id."\n\n";
	delete_course($course);
}

fclose($handle);

?>
