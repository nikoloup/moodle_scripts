<?php
 
define('CLI_SCRIPT', true);


//Includes 
require_once('config.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');

//Get directory with exports from argument
if(!isset($argv[1]))
{
	echo 'ERROR : Please provide directory with export files (full path)';
	echo "\n";
}

//Arrays with matches of category ids and school ids
//idindexu -> unergraduate, idindexp -> postgraduate
//Column 1 : Export (school) id
//Column 2 : Moodle Category id
$idindexu = array(
	70 => 4,
	15 => 7,
	62 => 9,
	71 => 12,
	20 => 13,
	21 => 16,
	22 => 19,
	23 => 22,
	16 => 25,
	18 => 28,
	17 => 32,
	19 => 34,
	72 => 36,
	3 => 39,
	2 => 44,
	4 => 41,
	5 => 47,
	6 => 50,
	1 => 54,
	73 => 55,
	31 => 57,
	54 => 61,
	28 => 64,
	7 => 69,
	29 => 71,
	35 => 78,
	36 => 79,
	37 => 80,
	30 => 81,
	9 => 94,
	11 => 95,
	14 => 96,
	8 => 97,
	13 => 98,
	12 => 99,
	81 => 100,
	24 => 112,
	26 => 113,
	25 => 114,
	61 => 115,
	41 => 123,
	34 => 124,
	32 => 129,
	27 => 134,
	40 => 135
);


$idindexp = array(
	70 => 4,
	15 => 8,
	61 => 10,
	71 => 12,
	20 => 14,
	21 => 17,
	22 => 20,
	23 => 23,
	16 => 26,
	18 => 29,
	17 => 31,
	19 => 35,
	72 => 36,
	3 => 38,
	2 => 45,
	4 => 42,
	5 => 48,
	6 => 51,
	1 => 53,
	73 => 55,
	31 => 58,
	54 => 60,
	28 => 65,
	7 => 68,
	29 => 72,
	35 => 82,
	36 => 83,
	37 => 84,
	30 => 85,
	9 => 101,
	11 => 102,
	14 => 103,
	8 => 104,
	13 => 105,
	12 => 138,
	81 => 106,
	24 => 116,
	26 => 117,
	25 => 118,
	61 => 119,
	41 => 125,
	34 => 126,
	32 => 130,
	27 => 137,
	40 => 136
);

//Get list of files and folders in dir

$fullpath = $argv[1];
$tmp = explode('/',$fullpath);
$filename = end($tmp);

//Convert flag
//If cflag = 1, conversion is needed (Reteach courses)
//If cflag = 0, conversion is not needed (Moodle 2.7 backup courses - usually quizzes)
$cflag = intval($argv[2]);

//Course settings

$folder             = 99; // as found in: $CFG->dataroot . '/temp/backup/'
$categoryid         = 1; // e.g. 1 == Miscellaneous
$userdoingrestore   = 2; // e.g. 2 == admin

$matches = array();
$pattern = '/ExportFile_([0-9][0-9])([UPF]).*/';

preg_match($pattern, $filename, $matches);

echo "Importing $filename ...";
//Copy to folder
shell_exec("cp $fullpath $CFG->dataroot/temp/backup/");
//Unzip
shell_exec("unzip -d $CFG->dataroot/temp/backup/99 -o $CFG->dataroot/temp/backup/$filename");
//Delete the zip file
shell_exec("rm $CFG->dataroot/temp/backup/$filename");
	
//Do stuff to determine category

if($matches[2]=="U")
{
	if(array_key_exists(intval($matches[1]),$idindexu))
	{
		$categoryid = $idindexu[intval($matches[1])];
	}
	else
	{
		$categoryid = 1;
		echo "\nWARNING : Category not found, placed in Miscellaneous  ...";
	}
}
else if($matches[2]=="P")
{
	if(array_key_exists(intval($matches[1]),$idindexp))
	{
		$categoryid = $idindexp[intval($matches[1])];
	}
	else
	{
		$categoryid = 1;
		echo "\nWARNING : Category not found, placed in Miscellaneous  ...";
	}
}
else
{

	$categoryid = 1;
}

//Begin import

try{

	// Transaction.
	$transaction = $DB->start_delegated_transaction();
 
	// Create new course.
	$courseid = restore_dbops::create_new_course('', '', $categoryid);
		 
	// Restore backup into course.
	$controller = new restore_controller($folder, $courseid, 
	        backup::INTERACTIVE_NO, backup::MODE_GENERAL, $userdoingrestore,
	        backup::TARGET_NEW_COURSE);
	//Check if conversion is needed
	if($cflag==1)
	{
		$controller->convert();
	}
	$controller->execute_precheck();
	$controller->execute_plan();
	 
	// Commit.
	$transaction->allow_commit();

	echo "OK\n";

	$DB->dispose();

}
catch(Exception $e)
{
	echo $e;
	
	$matches_exc = array();
	$pattern_exc = "/'error\/([^']*)'/";
	preg_match($pattern_exc,$e,$matches_exc);	

	shell_exec("rm -r $CFG->dataroot/temp/backup/99");
	remove_dir($CFG->dataroot.'/cache', true);
	$DB->dispose();
	file_put_contents("failed.txt",$filename,FILE_APPEND);
	if(isset($matches_exc[1]))
	{
		file_put_contents("failed.txt"," ",FILE_APPEND);
		file_put_contents("failed.txt",$matches_exc[1],FILE_APPEND);

		//Move to quiz folder
		$tmp = array();
		$tmp = explode("/",$argv[1],-1);
		$tmp = implode($tmp,"/");
		$tmp = $tmp."../quiz/";
		if(!strcmp($matches_exc[1],'missing_common_question_field'))
		{
			shell_exec("mv $argv[1] $tmp");
		}
	}
	file_put_contents("failed.txt","\n",FILE_APPEND);
}
		
?>
