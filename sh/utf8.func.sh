#!/bin/bash

# shell params
# $0 - self script name
# $1 - filepath to replace function names
# $2 - aim: find | replace
# usage:
#
# ./utf8.func.sh utf8.func.php filepath.php

SELF=$0
FILE=$1
AIM=$2

#declare -a FUNCS;
FUNCS="
	func_name_1
	func_name_2
	func_name_3
"

for FUNC in $FUNCS; do
	echo "$FUNC"
done

exit;


if test -f $FILE ; then
	file -i $FILE | grep -i "charset" | grep -iv "charset=binary" | grep -i ".php" | grep -iv "utf8.func.php" | while read file ; do
		for FUNC in $FUNCS; do

			PATTERN="[^\$_\'\"]$FUNC\b"
			REPLACEMENT="_$FUNC"

			case $AIM in
				"find")
					grep -nH "$PATTERN" $FILE
					;;
				"replace")
					sed -i "s/$PATTERN/$REPLACEMENT/g" $FILE
					;;
			esac

		done
	done
elif test -d $FILE; then
	find $FILE -type f -name "*.php" -exec $SELF {} $AIM \;
else
	echo -e "Execute format: $SELF utf8.func.php filepath find|replace"
	echo -e "Example: $SELF utf8.func.php test.php find|replace"
fi

