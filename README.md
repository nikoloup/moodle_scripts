moodle_scripts
==============

Various Moodle 1.9 and 2.7 scripts for mass exporting, importing and deletion of courses

All php scripts must be run from within a Moodle installation

### nikoloup_backup_all.php
Backup all 2.7 courses between two ids.

Usage: 
```
php nikoloup_backup_all.php $starting_id $ending_id $export_destination_full_path
```

### nikoloup_delete_all_courses.php
Delete all 2.7 courses except the specific course shortnames and categories

Usage:
```
- Delete all courses
php nikoloup_delete_all_courses.php none none
- Delete all courses except specific shortnames
php nikoloup_delete_all_courses.php $txt_file_with_shortnames_one_line_each none
- Delete all courses except specific categories
php nikoloup_delete_all_courses.php none $txt_file_with_category_ids_one_line_each
- Delete all courses except shortnames and categories
php nikoloup_delete_all_courses.php $txt_file_with_shortnames_one_line_each $txt_file_with_category_ids_one_line_each
- Do any of the above as a dry run
php nikoloup_delete_all_courses.php $arg1 $arg2 -dry
```

### nikoloup_delete_selected_courses.php
Delete all 2.7 courses with IDs in 'empty_courses.txt'

Usage:
```
php nikoloup_delete_selected_courses.php
```

### nikoloup_restore_script.sh & nikoloup_massrestore.php
Import all 1.9 or 2.7 courses exports that are in a specific folder to a 2.7 Moodle installation

Usage:
```
sh nikoloup_restore_script.sh $course_exports_location $flag
```
where $flag = 1 if the courses are in Moodle 1 format or $flag = 0 if in Moodle 2 format
You will have to change the matching rules in nikoloup_massrestore.php to place the courses in the appropriate categories if you wish.
Courses failing import with the error 'error/missing_common_question_field' will be placed in folder 'quiz'.
