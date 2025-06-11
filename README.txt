RAISEPON 2.0 Docker Build

Raisepon is Opensource php/mysql software written to manage Subscriber base on RAISECOM's GPON/GEPON OLTs ISCOM5508(B), ISCOM5508-GP, ISCOM6820-EP, ISCOM6820-GP, 
ISCOM6860, ISCOM6800. Supported are most of RAISECOM ONUs.

Web interface is implemented with Bootstrap(https://getbootstrap.com/) and jQuery(https://jquery.com/). API relies on PHP-JWT(https://github.com/firebase/php-jwt)

Docker Installation:

Composer file is in Dockerfiles/

Download or copy content of docker-compose.yaml on your docker host and run "docker compose up -d"
There two docker containers created one for the database and one for the web application.
Web application by default is listening on port 8088. There is rsyslogd configured and listening on UDP port 1514 if you want to collect logs from the OLTs, you have to point them with the appropriate commands to send syslog to your host IP.
There are three volumes created by default, one for the database, one for the rrd/ files and one for the log files.

Web interface is at: http://your-host-ip:8088/

Default username/password for web interface admin/admin123.

Usage:
1. You need to add at least One OLT and one pon port to be able to add customers.
2. You can create also line-profile, service-profile and match them to Services ID in the web-interface. You need to pre-create the same profiles with same ids on the OLTs you are going to provision.
3. Clicking on INFO in index.html when you load the customers on selected OLT and PON will give you more information.
4. If you use AUTO assign of ONUs on certain OLT/PON port you can find created ONUs in UNASSIGNED before being automaticaly assigned by update_auto.php - executed every 5 minutes via crontab.
5. If you want to have backup of the startup-config and raisepon database. Configure xPON->Backup. Add FTP details for upload of the startup-config files from the OLTs and email-address for raisepon's db backup.

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



