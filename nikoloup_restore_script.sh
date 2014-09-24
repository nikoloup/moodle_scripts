#!/bin/bash
FILES=$1/*
FLAG=$2
mkdir $1/../quiz
for f in $FILES
do
	echo `php nikoloup_massrestore.php $f $FLAG`
done
