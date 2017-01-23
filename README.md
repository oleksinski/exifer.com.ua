# Photo site Exifer.com.ua

## Domain Info

### Main properties:
- Imana.UA domain ID: 356460
- TTL: 1 hour
- Negative TTL: 1 hour
- Refresh: 1800
- Expire: 2419200
- Retry: 600

### Subdomains:
1. @.exifer.com.ua MX 10
2. @.exifer.com.ua A "{IP_ADDRESS}"
3. *.exifer.com.ua A "{IP_ADDRESS}"
4. @.exifer.com.ua TXT "v=spf1 ip4:{IP_ADDRESS} a mx ~all"

## Server configuration

### Server setup
````
**********************************************************

Aptitude Install:
=============
apt-get install XXX

Aptitude Remove
=============
apt-get remove XXX

Aptitude Search
=============
apt-cache search XXX

Aptitude Update Repo
=============
apt-get update

Aptitude Autoremove [Force]
=============
apt-get [-f] autoremove

Aptitude Clean Fetched Modules
=============
apt-get clean [autoclean]

DPKG Total Remove
=============
dpkg --purge XXX

Compile sources
=============
./configure [--help]
make test|install|uninstall

**********************************************************
http://howtoforge.com/perfect-server-ubuntu8.04-lts-p6
http://svdev.ru/blog/setup-nginx-apache-under-debian/
http://gediminasm.org/article/build-php-5-3-0-php-5-3-4-dev-on-ubuntu-server
http://habrahabr.ru/blogs/server_side_optimization/67557/ (Nginx + PHP-cgi + eAccelerator)

* ln -sf /bin/bash /bin/sh
=============

* apt-get install ntp ntpdate
=============

* apt-get install mc
=============

Add new locales
=============
nano /var/lib/locales/supported.d/local:
locale-gen

/etc/hosts
=============

/etc/hostname
=============
pro

/etc/init.d/hostname start
hostname
hostname -f

network address modifications
=============
nano /etc/network/interfaces
/etc/init.d/networking restart

OpenSSH
=============
apt-get install ssh openssh-server
nano /etc/ssh/ssh_config
nano /etc/ssh/sshd_config

[disable apparmor]
=============
/etc/init.d/apparmor stop
update-rc.d -f apparmor remove
[apt-get remove apparmor apparmor-utils]

mysql
=============
apt-get install mysql-server mysql-client libmysqlclient15-dev mysql-client-5.1 mysql-common mysql-server-5.1 mysql-server-core-5.1

mysql -uroot -p

CUREATE DATABASE `exifer`;
GRANT ALL PRIVILEGES ON exifer.* TO 'exifer'@'localhost' IDENTIFIED BY 'password'; # WITH GRANT OPTION;
GRANT ALTER, CREATE, CREATE TEMPORARY TABLES, CREATE VIEW, DELETE, DROP, INDEX, INSERT, LOCK TABLES, SELECT, UPDATE ON exifer.* TO 'exifer'@'localhost' IDENTIFIED BY 'password';

CREATE DATABASE `etc`;
GRANT ALL PRIVILEGES ON etc.* TO 'exifer'@'localhost' IDENTIFIED BY 'password'; # WITH GRANT OPTION;
GRANT ALTER, CREATE, CREATE TEMPORARY TABLES, CREATE VIEW, DELETE, DROP, INDEX, INSERT, LOCK TABLES, SELECT, UPDATE ON etc.* TO 'exifer'@'localhost' IDENTIFIED BY 'password';

nano /etc/mysql/my.cfg
/etc/init.d/mysql restart
netstat -tap | grep mysql

apache2
=============
apt-get install apache2 apache2-doc apache2.2-common apache2-mpm-prefork apache2-utils libexpat1 ssl-cert libapache2-mod-rpaf
nano /etc/apache2/apache2.conf
ServerName 127.0.0.1

nano /etc/apache2/mods-available/dir.conf

Modules:
a2enmod ssl
a2enmod rewrite
a2enmod suexec
a2enmod include
a2enmod rpaf

update-rc.d -f apache2 remove # remove from init.d
update-rc.d apache2 defaults   # add to init.d

PCRE 8.10 CONFIGURE & MAKE & MAKE INSTALL
=============
download the latest version (8.10) from http://pcre.org
tar -xvf pcre_8.10.tar.gz
./configure --enable-utf8 --enable-unicode-properties
./make
cp ./.libs/libpcre.a ./libpcre.a

php modules [for install from sources PHP 5.3.3]
=============
apt-get install libtidy-dev curl libcurl3 libcurl3-dev libcurl3-gnutls libcurl4-openssl-dev zlib1g zlib1g-dev libxslt1-dev
libzip-dev libzip1 libxml2 libxml2-dev libxml2-utils libsnmp-base libsnmp15 libsnmp-dev libjpeg62 libjpeg62-dev libpng12-0 libpng12-dev
zlib1g zlib1g-dev libfreetype6 libfreetype6-dev libbz2-dev libxaw7-dev libmcrypt-dev libmcrypt4 g++ libjpeg-progs libjpeg8 libjpeg8-dev libpng3
libt1-5 libt1-dev libgmp3-dev libgmp3c2 firebird2.5-dev libsasl2-2 libsasl2-dev mcrypt libmcrypt-dev

apt-get install bzip2 libbz2-dev libbz2-1.0 [for x86]
apt-get install bzip2 lib64bz2-dev lib64bz2-1.0 [for x64]

mhash (maybe need to compile php with mhash support)
=============
download from http://sourceforge.net/projects/mhash/
./configure | make | make test | make install

openssl
=============
apt-get install openssl-devel ?

php from debian packages:
=============
apt-get install php-auth libapache2-mod-php5 php5 php5-common php5-curl php5-dev php5-gd php5-idn php-pear
php5-imagick php5-imap php5-mcrypt php5-memcache php5-mhash php5-ming php5-mysql php5-pspell php5-recode
php5-snmp php5-sqlite php5-tidy php5-xmlrpc php5-xsl php5-cgi php5-cli spawn-fcgi mcrypt

edit php.ini
=============
nano php5/conf-d/* [replace "#" with ";"]
mkdir /etc/php5/common
cp /etc/php5/apache2/php.ini /etc/php5/common/php.ini
ln -sf /etc/php5/common/php.ini /etc/php5/apache2/php.ini
ln -sf /etc/php5/common/php.ini /etc/php5/cli/php.ini
ln -sf /etc/php5/common/php.ini /etc/php5/cgi/php.ini
nano /etc/php5/common/php.ini
post_max_size = 8M
magic_quotes_gpc = Off
magic_quotes_sybase = Off
upload_max_filesize = 10M
mbstring.internal_encoding = UTF-8
mbstring.http_output = UTF-8
mbstring.func_overload = 4
allow_call_time_pass_reference = On

nginx
=============
apt-get install nginx
http://articles.slicehost.com/2008/5/13/ubuntu-hardy-adding-an-nginx-init-script
http://habrahabr.ru/blogs/nginx/66764/
https://github.com/JasonGiedymin/nginx-init-ubuntu

php-fastcgi
=============
http://svdev.ru/blog/nginx-php-fastcgi-remove-apache/
http://howtoforge.com/drupal-6-hosting-with-nginx-and-php-fastcgi-on-ubuntu-9.10

sudo apt-get install php5-cgi spawn-fcgi
use php-cgi instead of spawn-cgi ?

/usr/bin/php5-cgi # system
/usr/bin/php-fastcgi # manual
/etc/init.d/init-fastcgi # manual

sudo chmod 755 /usr/bin/php-fastcgi
sudo chmod 755 /etc/init.d/init-fastcgi
sudo update-rc.d init-fastcgi defaults

install git
=============
apt-get install git-core gitosis

http://www.mail-archive.com/github@googlegroups.com/msg00196.html
http://progit.org/book/
http://toroid.org/ams/git-website-howto
http://danielmiessler.com/blog/using-git-to-maintain-your-website

postfix
=============
http://howtoforge.com/perfect-server-ubuntu8.04-lts-p5

apt-get install postfix libsasl2-2 sasl2-bin libsasl2-modules procmail
dpkg-reconfigure postfix

mv /etc/postfix/main.cf /etc/postfix/main.cf.orig
cp /www/exifer/www/env/postfix/main.cf /etc/postfix/main.cf

postconf -e 'smtpd_sasl_local_domain ='
postconf -e 'smtpd_sasl_auth_enable = yes'
postconf -e 'smtpd_sasl_security_options = noanonymous'
postconf -e 'broken_sasl_auth_clients = yes'
postconf -e 'smtpd_sasl_authenticated_header = yes'
postconf -e 'smtpd_recipient_restrictions = permit_sasl_authenticated,permit_mynetworks,reject_unauth_destination'
postconf -e 'inet_interfaces = all'

echo 'pwcheck_method: saslauthd' >> /etc/postfix/sasl/smtpd.conf
echo 'mech_list: plain login' >> /etc/postfix/sasl/smtpd.conf

mkdir /etc/postfix/ssl
cd /etc/postfix/ssl/
openssl genrsa -des3 -rand /etc/hosts -out smtpd.key 1024
chmod 600 smtpd.key
openssl req -new -key smtpd.key -out smtpd.csr
openssl x509 -req -days 3650 -in smtpd.csr -signkey smtpd.key -out smtpd.crt
openssl rsa -in smtpd.key -out smtpd.key.unencrypted
mv -f smtpd.key.unencrypted smtpd.key
openssl req -new -x509 -extensions v3_ca -keyout cakey.pem -out cacert.pem -days 3650

postconf -e 'myhostname = exifer.com.ua'
postconf -e 'smtpd_tls_auth_only = no'
postconf -e 'smtp_use_tls = yes'
postconf -e 'smtpd_use_tls = yes'
postconf -e 'smtp_tls_note_starttls_offer = yes'
postconf -e 'smtpd_tls_key_file = /etc/postfix/ssl/smtpd.key'
postconf -e 'smtpd_tls_cert_file = /etc/postfix/ssl/smtpd.crt'
postconf -e 'smtpd_tls_CAfile = /etc/postfix/ssl/cacert.pem'
postconf -e 'smtpd_tls_loglevel = 1'
postconf -e 'smtpd_tls_received_header = yes'
postconf -e 'smtpd_tls_session_cache_timeout = 3600s'
postconf -e 'tls_random_source = dev:/dev/urandom'

mkdir -p /var/spool/postfix/var/run/saslauthd
Set START to yes and change the line OPTIONS="-c -m /var/run/saslauthd" to OPTIONS="-c -m /var/spool/postfix/var/run/saslauthd -r":

/etc/init.d/postfix restart
/etc/init.d/saslauthd start

nano /etc/php5/common/php.ini
sendmail_path = /usr/sbin/sendmail -t -i

eAccelerator
============
cd /tmp
wget httр://bart.eaccelerator.net/source/0.9.5.3/eaccelerator-0.9.5.3.tar.bz2
tar xvfj eaccelerator-0.9.5.3.tar.bz2
cd eaccelerator-0.9.5.3
phpize
./configure
make
make install

nano /etc/php5/conf.d/eaccelerator.ini
extension="eaccelerator.so"
eaccelerator.shm_size="64"
eaccelerator.cache_dir="/var/cache/eaccelerator"
eaccelerator.enable="1"
eaccelerator.optimizer="1"
eaccelerator.check_mtime="1"
eaccelerator.debug="0"
eaccelerator.filter=""
eaccelerator.shm_max="0"
eaccelerator.shm_ttl="3600"
eaccelerator.shm_prune_period="1800"
eaccelerator.shm_only="0"
eaccelerator.compress="1"
eaccelerator.compress_level="9"

mkdir -p /var/cache/eaccelerator
chmod 0777 /var/cache/eaccelerator

memcached
=========
apt-get install memcached

modify cron
=============
nano /etc/cron.d/php5
nano /etc/cron.daily/popularity-contest (exit 0)

apt-get install phpmyadmin
=============

Recursive chmod:
=============
find www -type d -exec chmod 755 {} \;
find www -type f -exec chmod 644 {} \;

Starting/stoping services on Ubuntu
=============
https://help.ubuntu.com/community/UbuntuBootupHowto

# Traditional:
/etc/init.d/myservice start

# Upstart
service myservice start

Mp3 convert
=============
find . -type f -name "*.mp3" -exec ffmpeg -i input/{} -acodec
libmp3lame -ab 128k -ac 2 -ar 44100 output/{} -map_meta_data
output/{}:input/{} \;

````

### Crontab jobs
```
# usage: crontab [-u user] file
#        crontab [ -u user ] [ -i ] { -e | -l | -r }
#               (default operation is replace, per 1003.2)
#        -e     (edit user's crontab)
#        -l     (list user's crontab)
#        -r     (delete user's crontab)
#        -i     (prompt before deleting user's crontab)
#
# 1) sudo su
# 2) crontab {this-file-path}
# 3) crontab -l
#
#
#* 1: Minute (0-59)
#* 2: Hours (0-23)
#* 3: Day (0-31)
#* 4: Month (0-12 [12 == December])
#* 5: Day of the week(0-7 [7 or 0 == sunday])
#* /path/to/command - Script or command name to schedule

#* * * * * command to be executed
#- - - - -
#| | | | |
#| | | | ----- Day of week (0 - 7) (Sunday=0 or 7)
#| | | ------- Month (1 - 12)
#| | --------- Day of month (1 - 31)
#| ----------- Hour (0 - 23)
#------------- Minute (0 - 59)

# 1 2 3 4 5 USERNAME /path/to/command arg1 arg2

################################################

# mysql backup (at 00:13)
13 0 * * * sudo -u www-data /www/exifer/www/sh/mysql.backup.sh > /dev/null 2>&1

# clear expired filecache data (at 00:20)
20 0 * * * sudo -u www-data /usr/bin/php /www/exifer/www/cron/storage_clear_expired.exec.php

# update user hit info profile & delete expired data (every 30 minute)
0,30 * * * * sudo -u www-data /usr/bin/php /www/exifer/www/cron/user_online.exec.php

# sitemaps generation (at 06:30)
30 6 * * * sudo -u www-data /usr/bin/php /www/exifer/www/cron/sitemap.gen.php
```

### Server Locale

/var/lib/locales/supported.d/local:
````
en_US.UTF-8 UTF-8
ru_RU.UTF-8 UTF-8
ru_UA.UTF-8 UTF-8
uk_UA.UTF-8 UTF-8
ru_RU.CP1251 CP1251
ru_RU.KOI8-R KOI8-R
````

### PHP-FastCGI configuration

/etc/init.d/php-fastcgi

````
#! /bin/sh
### BEGIN INIT INFO
# Provides:          php-fastcgi
# Required-Start:    $all
# Required-Stop:     $all
# Default-Start:     2 3 4 5
# Default-Stop:      0 1 6
# Short-Description: Start and stop php-cgi in external FASTCGI mode
# Description:       Start and stop php-cgi in external FASTCGI mode
### END INIT INFO

# Author: Kurt Zankl <[EMAIL PROTECTED]>

# Do NOT "set -e"

PATH=/sbin:/usr/sbin:/bin:/usr/bin
DESC="php-cgi in external FASTCGI mode"
NAME=php-fastcgi
DAEMON=/usr/bin/php-cgi
PIDFILE=/var/run/$NAME.pid
SCRIPTNAME=/etc/init.d/$NAME
PHP_CONFIG_FILE=/etc/php5/cgi/php.ini
START=yes
EXEC_AS_USER=www-data
FCGI_HOST=127.0.0.1
FCGI_PORT=9000
PHP_FCGI_CHILDREN=4
PHP_FCGI_MAX_REQUESTS=1000

# Exit if the package is not installed
[ -x "$DAEMON" ] || exit 0

# Load the VERBOSE setting and other rcS variables
. /lib/init/vars.sh

# Define LSB log_* functions.
# Depend on lsb-base (>= 3.0-6) to ensure that this file is present.
. /lib/lsb/init-functions

# Process configuration
export PHP_FCGI_CHILDREN PHP_FCGI_MAX_REQUESTS
DAEMON_ARGS="-q -b $FCGI_HOST:$FCGI_PORT -c $PHP_CONFIG_FILE"

do_start()
{
        # Return
        #   0 if daemon has been started
        #   1 if daemon was already running
        #   2 if daemon could not be started
        start-stop-daemon --start --quiet --pidfile $PIDFILE --exec $DAEMON --test > /dev/null \
                || return 1
        start-stop-daemon --start --quiet --pidfile $PIDFILE --exec $DAEMON \
                --background --make-pidfile --chuid $EXEC_AS_USER --startas $DAEMON -- \
                $DAEMON_ARGS \
                || return 2
}

do_stop()
{
        # Return
        #   0 if daemon has been stopped
        #   1 if daemon was already stopped
        #   2 if daemon could not be stopped
        #   other if a failure occurred
        start-stop-daemon --stop --quiet --retry=TERM/30/KILL/5 --pidfile $PIDFILE > /dev/null # --name $DAEMON
        RETVAL="$?"
        [ "$RETVAL" = 2 ] && return 2
        # Wait for children to finish too if this is a daemon that forks
        # and if the daemon is only ever run from this initscript.
        # If the above conditions are not satisfied then add some other code
        # that waits for the process to drop all resources that could be
        # needed by services started subsequently.  A last resort is to
        # sleep for some time.
        start-stop-daemon --stop --quiet --oknodo --retry=0/30/KILL/5 --exec $DAEMON
        [ "$?" = 2 ] && return 2
        # Many daemons don't delete their pidfiles when they exit.
        rm -f $PIDFILE
        return "$RETVAL"
}
case "$1" in
  start)
        [ "$VERBOSE" != no ] && log_daemon_msg "Starting $DESC" "$NAME"
        do_start
        case "$?" in
                0|1) [ "$VERBOSE" != no ] && log_end_msg 0 ;;
                2) [ "$VERBOSE" != no ] && log_end_msg 1 ;;
        esac
        ;;
  stop)
        [ "$VERBOSE" != no ] && log_daemon_msg "Stopping $DESC" "$NAME"
        do_stop
        case "$?" in
                0|1) [ "$VERBOSE" != no ] && log_end_msg 0 ;;
                2) [ "$VERBOSE" != no ] && log_end_msg 1 ;;
        esac
        ;;
  restart|force-reload)
        log_daemon_msg "Restarting $DESC" "$NAME"
        do_stop
        case "$?" in
          0|1)
                do_start
                case "$?" in
                        0) log_end_msg 0 ;;
                        1) log_end_msg 1 ;; # Old process is still running
                        *) log_end_msg 1 ;; # Failed to start
                esac
                ;;
          *)
                # Failed to stop
                log_end_msg 1
                ;;
        esac
        ;;
  *)
        echo "Usage: $SCRIPTNAME {start|stop|restart|force-reload}" >&2
        exit 3
        ;;
esac
````

### MySQL configuration

/etc/mysql/conf.d/character_cp1251.cnf
````
# SET CP1251(WINDOWS-1251) CHARACTER SET

[client]
default-character-set=cp1251

[mysqld]
default-character-set=cp1251
default-collation=cp1251_general_ci
init-connect = "SET NAMES cp1251"
collation-server=cp1251_general_ci
skip-character-set-client-handshake

[mysql]
default-character-set=cp1251

[mysqldump]
default-character-set=cp1251
````

/etc/mysql/conf.d/character_utf8.cnf
````
# SET UTF-8 CHARACTER SET

[client]
default-character-set=utf8

[mysqld]
default-character-set=utf8
default-collation=utf8_general_ci
init-connect = "SET NAMES utf8"
collation-server=utf8_general_ci
skip-character-set-client-handshake

[mysql]
default-character-set=utf8

[mysqldump]
default-character-set=utf8
````

### Nginx configuration

/etc/nginx/conf.d/proxy.conf

````
proxy_redirect off;
proxy_set_header Host $host;
proxy_set_header X-Real-IP $remote_addr;
proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
proxy_connect_timeout 3s;
proxy_read_timeout 5s;
proxy_send_timeout 7s;
proxy_buffer_size 4k;
proxy_buffers 8 64k;
proxy_busy_buffers_size 64k;
proxy_temp_file_write_size 64k;

client_max_body_size 10m;
client_body_buffer_size 128k;
````

/etc/nginx/fastcgi_params
````
fastcgi_param  QUERY_STRING       $query_string;
fastcgi_param  REQUEST_METHOD     $request_method;
fastcgi_param  CONTENT_TYPE       $content_type;
fastcgi_param  CONTENT_LENGTH     $content_length;

fastcgi_param  SCRIPT_NAME        $fastcgi_script_name;
fastcgi_param  REQUEST_URI        $request_uri;
fastcgi_param  DOCUMENT_URI       $document_uri;
fastcgi_param  DOCUMENT_ROOT      $document_root;
fastcgi_param  SERVER_PROTOCOL    $server_protocol;

fastcgi_param  GATEWAY_INTERFACE  CGI/1.1;
fastcgi_param  SERVER_SOFTWARE    nginx/$nginx_version;

fastcgi_param  REMOTE_ADDR        $remote_addr;
fastcgi_param  REMOTE_PORT        $remote_port;
fastcgi_param  SERVER_ADDR        $server_addr;
fastcgi_param  SERVER_PORT        $server_port;
fastcgi_param  SERVER_NAME        $server_name;

fastcgi_param HTTP_X_FORWARDED_FOR $proxy_add_x_forwarded_for;

# PHP only, required if PHP was built with --enable-force-cgi-redirect
fastcgi_param  REDIRECT_STATUS    200;
````

### Nginx server configuration

/etc/nginx/sites-available/exifer.pro

````
server {
	listen exifer.com.ua;
	server_name exifer.com.ua www.exifer.com.ua;
	root /www/exifer/www;
	set $d_root $document_root;
	access_log /var/log/nginx/exifer.access.log;
	error_log /var/log/nginx/exifer.error.log;
	charset utf-8;
	location / {
		rewrite ^/.* /index.php break;
		fastcgi_pass 127.0.0.1:9000;
		include /etc/nginx/fastcgi_params;
		fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
		fastcgi_param SCRIPT_NAME /index.php;
		fastcgi_index index.php;
		fastcgi_intercept_errors on;
	}
	location /favicon.ico {
		root $d_root/s/img/logo; break;
	}
	location /robots.txt {
		root $d_root; break;
	}
	location /crossdomain.xml {
		root $d_root; break;
	}
}

server {
	listen s.exifer.com.ua;
	server_name s.exifer.com.ua;
	root /www/exifer/s;
	set $s_root $document_root;
	autoindex off;
	charset utf-8;
	access_log  /var/log/nginx/exifer.access.s.log;
	error_log  /var/log/nginx/exifer.error.s.log;
	location /favicon.ico {
		root $s_root/img/logo; break;
	}
	location /spacer.gif {
		empty_gif; expires max; break;
	}
	location /robots.txt {
		empty_gif; expires max; break;
	}
	location /js_min {
		deny all;
	}
	location /js_raw {
		deny all;
	}
	location /css_raw {
		deny all;
	}
	location /css_min {
		deny all;
	}
}

server {
	listen i.exifer.com.ua;
	server_name i.exifer.com.ua;
	root /www/exifer/i;
	set $d_root $document_root/..;
	autoindex off;
	charset utf-8;
	access_log  /var/log/nginx/exifer.access.i.log;
	error_log  /var/log/nginx/exifer.error.i.log;
	location /favicon.ico {
		root $d_root/s/img/logo; break;
	}
	location /spacer.gif {
		empty_gif; expires max; break;
	}
	location /robots.txt {
		empty_gif; expires max; break;
	}
}

````

### How to use rsync
````
rsync -avz -e "ssh -p PORT" USERNAME@SERVERNAME:/source/path /destination/path
````

### Using Git on the project
````
# SERVER & DEV

$ git config --global user.name "USERNAME"
$ git config --global user.email "USERNAME@email.com"

# SERVER

$ sudo -i
$ cd /

$ mkdir -p /gitrepo/exifer
$ chown USERNAME:USERNAME /gitrepo/exifer

$ mkdir -p /www/exifer/i
$ mkdir -p /www/exifer/static
$ mkdir -p /www/exifer/static/smarty/template_cache
$ mkdir -p /www/exifer/static/smarty/template_compile
$ mkdir -p /www/exifer/static/smarty/template_config
$ chown USERNAME:USERNAME /www/exifer
$ chown -R www-data:www-data /www/exifer/i
$ chown -R www-data:www-data /www/exifer/static

# DEV

$ cd /www/exifer/
$ tar -cf exifer_www.tar www
$ scp exifer_www.tar USERNAME@pro:/gitrepo/exifer_www.tar

# SERVER

$ sudo -i USERNAME

$ cd /gitrepo/exifer
$ tar -xvf exifer_www.tar
$ rm exifer_ww.tar

$ cd www
$ git init
$ git add .
$ git commit -a "Initial exifer import" .

$ cd ../

$ git clone --bare www www.git

# DEV

$ sudo -i USERNAME
$ cd /www/exifer
$ git clone ssh://exifer.com.ua/gitrepo/exifer/www.git /www/exifer/www

# SERVER

$ sudo -i USERNAME
$ git clone /gitrepo/exifer/www.git /www/exifer/www

# SERVER & DEV
$ sudo -i USERNAME
$ ln -s /www/exifer/www/s /www/exifer/s
$ ln -s /var/log/nginx /www/exifer/log

======================================

Branching:
^^^^^^^^^

View branch info:
----------------
$ git branch

View last commits on branches:
------------------------------
$ git branch -v

Show remote info
----------------
$ git remote show origin

Create local branch:
---------------
$ git branch branch_name
$ git checkout branch_name

OR

$ git checkout -b branch_name

Back to master branch:
---------------------
$ git checkout master

Merging branch into master trunk:
---------------------------------

$ git checkout -b testing
$ git commit -a -m "test message" .
$ git checkout master
$ git merge testing

Delete branch:
--------------
$ git branch -d hotfix

View branches which were merged with trunk (master branch)
-----------------------------------------------------------
$ git branch --merged

View branches which were not merged yet with master branch
-----------------------------------------------------------
$ git branch --no-merged

Commit and push local branch to remote server
---------------------------------------------
$ git push origin serverfix

Checkout remote branch and track all changes
--------------------------------------------
$ git checkout -b serverfix origin/serverfix

Synchronize local branch history with server's one
--------------------------------------------------
$ git fetch origin btanch_name

Create local branch from server's branch
---------------------------------------
$ git checkout -b serverfix origin/serverfix, where serverfix - branch name

Delete branch on remote server
------------------------------
$ git push origin :serverfix


View Tags
--------
$ git tag

View tag info
------------
$ git show v1.4

Check if tag is checkouted
--------------------------
$ git name-rev --name-only --tags HEAD
$ git describe --exact-match --tags HEAD

Create annotated tag
--------------------
$ git tag -a v1.4 -m 'my version 1.4'

Create light tag
-----------------
$ git tag v1.4

Fetch tag
---------------
$ git fetch origin tag X

Checkout tag
-------------
$ git checkout tag_name

Delete tag
----------
$ git tag -d X

Push tags to remote server
--------------------------
$ git push origin v1.5

````