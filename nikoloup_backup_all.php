<?php

define('CLI_SCRIPT', 1);
require_once('config.php');
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');

$starting_id = intval($argv[1]);
$ending_id = intval($argv[2]);
$destination = $argv[3];

$link = new mysqli($CFG->dbhost, $CFG->dbuser, $CFG->dbpass, $CFG->dbname);
if ($link->connect_errno) {
        echo "Failed to connect to MySQL: (" . $link->connect_errno . ") " . $link->connect_error;
}

for($i=$starting_id; $i<=$ending_id; $i++)
{ 
            $query = "select id,shortname from mdl_course where id=".$i;

            $res = $link->query($query);        

            while ($row = $res->fetch_assoc()) {
                $shortname = $row['shortname'];
            }
            
            $bc = new backup_controller(backup::TYPE_1COURSE, $i, backup::FORMAT_MOODLE, backup::INTERACTIVE_NO, backup::MODE_GENERAL, 2);

            $bc->execute_plan();

            $result = $bc->get_results();
            $file = $result['backup_destination'];
            $file->copy_content_to($destination."/ExportFile_".$shortname."_".$i.".zip");
}

?>
