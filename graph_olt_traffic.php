<?php
include ("common.php");
include ("dbconnect.php");
if ($_GET) {
	$ip_address = $_GET['ip_address'];
	$index = $_GET['index'];
	$ifDescr = $_GET['ifDescr'];
	if (!preg_match('/^[0-9.]*$/', $ip_address)) {
		print "that sux";
		exit;
	}

	if (!preg_match('/^[0-9]*$/', $index)) {
		print "that sux";
		exit;
	} else {
		
		$rrd_name = dirname(__FILE__) . "/rrd/" . $ip_address . "_" . $index . "_traffic.rrd";

  $opts = array( "--start", "-1d", "--lower-limit=0", "--vertical-label=B/s", "--title=Daily Traffic $index",
                 "DEF:inoctets=$rrd_name:input:AVERAGE",
                 "DEF:outoctets=$rrd_name:output:AVERAGE",
                 "AREA:inoctets#00FF00:In traffic",
                 "LINE1:outoctets#0000FF:Out traffic\\r",
                 "CDEF:inbits=inoctets",
                 "CDEF:outbits=outoctets",
                 "GPRINT:inbits:LAST:Last In\: %6.2lf %sBps",
                 "GPRINT:inbits:AVERAGE:Avg In\: %6.2lf %sBps",
                 "COMMENT:  ",
                 "GPRINT:inbits:MAX:Max In\: %6.2lf %sBps\\r",
                 "COMMENT:\\n",
                 "GPRINT:outbits:LAST:Last Out\: %6.2lf %sBps",
                 "GPRINT:outbits:AVERAGE:Avg Out\: %6.2lf %sBps",
                 "COMMENT: ",
                 "GPRINT:outbits:MAX:Max Out\: %6.2lf %sBps\\r"
               );
  $opts2 = array( "--start", "-1w", "--lower-limit=0", "--vertical-label=B/s", "--title=Weekly Traffic $index",
                 "DEF:inoctets=$rrd_name:input:AVERAGE",
                 "DEF:outoctets=$rrd_name:output:AVERAGE",
                 "AREA:inoctets#00FF00:In traffic",
                 "LINE1:outoctets#0000FF:Out traffic\\r",
                 "CDEF:inbits=inoctets",
                 "CDEF:outbits=outoctets",
                 "GPRINT:inbits:LAST:Last In\: %6.2lf %sBps",
                 "GPRINT:inbits:AVERAGE:Avg In\: %6.2lf %sBps",
                 "COMMENT:  ",
                 "GPRINT:inbits:MAX:Max In\: %6.2lf %sBps\\r",
                 "COMMENT:\\n",
                 "GPRINT:outbits:LAST:Last Out\: %6.2lf %sBps",
                 "GPRINT:outbits:AVERAGE:Avg Out\: %6.2lf %sBps",
                 "COMMENT: ",
                 "GPRINT:outbits:MAX:Max Out\: %6.2lf %sBps\\r"
               );
  $opts3 = array( "--start", "-1m", "--lower-limit=0", "--vertical-label=B/s", "--title=Monthly Traffic $index",
                 "DEF:inoctets=$rrd_name:input:AVERAGE",
                 "DEF:outoctets=$rrd_name:output:AVERAGE",
                 "AREA:inoctets#00FF00:In traffic",
                 "LINE1:outoctets#0000FF:Out traffic\\r",
                 "CDEF:inbits=inoctets",
                 "CDEF:outbits=outoctets",
                 "GPRINT:inbits:LAST:Last In\: %6.2lf %sBps",
                 "GPRINT:inbits:AVERAGE:Avg In\: %6.2lf %sBps",
                 "COMMENT:  ",
                 "GPRINT:inbits:MAX:Max In\: %6.2lf %sBps\\r",
                 "COMMENT:\\n",
                 "GPRINT:outbits:LAST:Last Out\: %6.2lf %sBps",
                 "GPRINT:outbits:AVERAGE:Avg Out\: %6.2lf %sBps",
                 "COMMENT: ",
                 "GPRINT:outbits:MAX:Max Out\: %6.2lf %sBps\\r"
               );
  $rrd_traffic_url = $ip_address . "_" . $index . "_traffic.gif"; 
  $rrd_traffic_url_week = $ip_address . "_" . $index . "_traffic_week.gif";
  $rrd_traffic_url_month = $ip_address . "_" . $index . "_traffic_month.gif";
  $rrd_traffic = dirname(__FILE__) . "/rrd/" . $rrd_traffic_url;
  $rrd_traffic_week = dirname(__FILE__) . "/rrd/" . $rrd_traffic_url_week;
  $rrd_traffic_month = dirname(__FILE__) . "/rrd/" . $rrd_traffic_url_month;

  $ret = rrd_graph($rrd_traffic, $opts);
  $ret = rrd_graph($rrd_traffic_week, $opts2);
  $ret = rrd_graph($rrd_traffic_month, $opts3);

  if( !is_array($ret) )
  {
    $err = rrd_error();
    echo "rrd_graph() ERROR: $err\n";
  }
}
print "<center><h3>RRD Traffic Graphs for $ip_address Port: $index $ifDescr</h3> ";
print "<p><img src=\"rrd/" . $rrd_traffic_url . "\"></img></p>";
print "<p><img src=\"rrd/" . $rrd_traffic_url_week . "\"></img></p>";
print "<p><img src=\"rrd/" . $rrd_traffic_url_month . "\"></img></p>";
}

?>


