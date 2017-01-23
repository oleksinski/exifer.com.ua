#!/bin/bash

# shell params
#$0 - self script name
#$1 - CP1251
#$2 - UTF-8
#$3 - filepath


if [[ -n "$1" && -n "$2" && -n "$3" ]] ; then
	if test -f "$3" ; then
		file -i "$3" | grep -i "charset" | grep -iv "charset=$2" | grep -iv "charset=binary" | while read file_output ;
		do
			TMP_FILE="$3"-iconv-tmp
			echo -e "$3"

			/usr/bin/iconv -f "$1" -t "$2" "$3" -o "$TMP_FILE";

			if [ $? -eq 0 ] ; then
				mv "$TMP_FILE" "$3"
			else
				rm "$TMP_FILE"
			fi
		done
	elif test -d "$3"; then
		find "$3" -type f \( -name "*" ! -name "fconv.sh" \) -exec $0 $1 $2 {} \;
	else
		echo "filepath '$3' not exists"
	fi
else
	echo -e "Execute format: $0 from_encoding to_encoding filepath"
	echo -e "Example: $0 CP1251 UTF-8 filepath"
fi


#
# CHECK FILES FOR ENCODING
# 
# find . -type f -name "*" -exec file -i {} \; | grep -v utf-8
#
