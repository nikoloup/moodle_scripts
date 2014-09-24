<?php

// to be able to run this file as command line script
define('CLI_SCRIPT', true);

require_once('config.php');
require_once($CFG->dirroot . '/course/lib.php');

$stay_courses = array();
$stay_categories = array();

$dry_run = false;

if(!isset($argv[1]))
{
	echo "\nPlease provide file with courses to not be removed or enter \"none\" (argument 1)\n";
	exit;
}
if(!isset($argv[2]))
{
	echo "\nPlease provide file with categories to not be removed or enter \"none\" (argument 2)\n";
	exit;
}
if(isset($argv[3]))
{
	if($argv[3]=="-dry")
	{
		$dry_run = true;
		echo "/*DRY RUN*/\n\n";
	}
	else
	{
		echo "\nPlease enter \"-dry\" for dry run (argument 3)\n";
		exit;
	}
}

if($argv[1]!="none")
{
	$handle = fopen($argv[1],"r");
	if($handle)
	{
		while (($line = fgets($handle)) !== false) 
		{
        		$stay_courses[] = trim($line);
    		}
	} 
	else 
	{
		echo "\nError opening file 1\n";
		exit;
	}
} 
fclose($handle);

if($argv[2]!="none")
{
	$handle = fopen($argv[2],"r");
	if($handle)
	{
        	while (($line = fgets($handle)) !== false) 
        	{
        	        $stay_categories[] = trim($line);
        	}
	} 
	else 
	{
		echo "\nError opening file 2\n";
		exit;
	}
}
fclose($handle);

$courses = get_courses();

//print_r("Courses count: " . count($courses) . "\n");

echo "Courses Skipped (untouched):\n";

if(count($courses) > 1) { // there is one default course of moodle                                                                                                                                   
	foreach ($courses as &$course) {
		if(in_array($course->category,$stay_categories))
		{
			echo "$course->shortname\n";
			continue;
		}
		if($key = array_search($course->shortname,$stay_courses))
		{
			unset($stay_courses[$key]);
			echo "$course->shortname\n";
			continue;
		}
		if(!$dry_run)
		{
			delete_course($course);
		}	
	    	fix_course_sortorder(); // important!
	}
}
else { 
	print_r("\nNo course in the system!\n");                                                                                                            
}

echo "\nCourses to remain that were not found:\n";
print_r($stay_courses);

?>
