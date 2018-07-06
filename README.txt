RAISEPON 2.0

Raisepon is Opensource php/mysql software written to manage Subscriber base on RAISECOM's GPON/GEPON OLTs ISCOM5508(B), ISCOM5508-GP, ISCOM6860, ISCOM6800. Supported are most of RAISECOM ONUs.

Installation:

I suggest using latest FreeBSD or Debian Stable Release.
You need:

Apache 2.4 or later
PHP 5.6 or later 
FreeBSD Ports Colection PHP 5.6 Extensions + PHP Mysql PDO
Mysql 5.6 or later 
net-snmp + php-snmp
FreeBSD ports rrdtool + pecl-rrd
On Debian/Ubuntu type linux you will need probably librrd-dev and php-rrd packets. 

Copy the files to your web folder.
Create database "gpon" and load in it the supplied gpon.sql file. 
Grant permissions to user for databse "gpon". Modify classes/db_connect_class.php and dbconnect.php to match the mysql user,pass.


Add the following to your crontab:

*/5 * * * *     www     /usr/local/bin/php -f /path/to/your/webcontent/update_rrd.php > /dev/null 2>&1

Add also this if you want to use AUTO ONU registering based on Illegal ONUs found on device:


*/5 * * * *     www     /usr/local/bin/php -f /path/to/your/webcontent/update_auto.php > /dev/null 2>&1

Configure your OLTs to send logs to your syslogd server. 
Edit your sylogd.conf:

local7.*                                        /var/log/gpon.log

Create rrd/ directory under the raisepon root tree.

Default username/password admin/admin123.

Usage:
1. You need to add at least One OLT and one pon port to be able to add customers.
2. If you are provisioning GPON/GEPON, you CAN create also line-profile, service-profile and match them to Services ID in the web-interface. You need to pre-create the same profiles with same ids on the OLTs you are going to provision.
3. Clicking on INFO in index.html when you load the customers on selected OLT and PON will give you more information.
4. If you use AUTO assign of ONUs on certain OLT/PON port you can find created ONUs in UNASSIGNED before being automaticaly assigned by update_auto.php - executed every 5 minutes via crontab.


The software is provided under the MIT License. Read below:

The MIT License (MIT)

Copyright (c) 2017 Ventsislav Velkov a.k.a. Veno

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.



