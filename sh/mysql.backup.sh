#!/bin/sh

### MySQL Server Login Info ###
MUSER="exifer"
MPASS="<MYSQL_PASSWORD>"
MHOST="localhost"

MYSQL="$(which mysql)"
MYSQLDUMP="$(which mysqldump)"

BASE="/www/exifer/backup/mysql"
GZIP="$(which gzip)"
NOW=$(date +"%Y-%m-%d-%T")

### FTP SERVER Login info ###
#FTPU="FTP-SERVER-USER-NAME"
#FTPP="FTP-SERVER-PASSWORD"
#FTPS="FTP-SERVER-IP-ADDRESS"

#[ ! -d $BASE ] && mkdir -p $BASE

#DBS="$($MYSQL -u $MUSER -h $MHOST -p$MPASS -Bse 'show databases')"
#for db in $DBS

for db in exifer
do
 BAK="${BASE}/${db}"
 [ ! -d $BAK ] && mkdir -p $BAK
 FILE="${BAK}/${db}.${NOW}.gz"
 $MYSQLDUMP -u $MUSER -h $MHOST -p$MPASS $db | $GZIP -9 > $FILE
done

#lftp -u $FTPU,$FTPP -e "mkdir /mysql/$NOW;cd /mysql/$NOW; mput /backup/mysql/*; quit" $FTPS
