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
		"CDEF:inbits=inoctets,8,*",
		"CDEF:outbits=outoctets,8,*",
		"AREA:inbits#00FF00:In traffic",
		"LINE1:outbits#0000FF:Out traffic\\r",
		"GPRINT:inbits:MAX:IN Max\: %6.2lf%Sbps",
		"COMMENT:  ",
		"GPRINT:inbits:AVERAGE:Avg\: %6.2lf%Sbps",
		"COMMENT:  ",
		"GPRINT:inbits:LAST:Last\: %6.2lf%Sbps\\r",
		"COMMENT:\\n",
		"GPRINT:outbits:MAX:OUT Max\: %6.2lf%Sbps",
		"COMMENT:  ",
		"GPRINT:outbits:AVERAGE:Avg\: %6.2lf%Sbps",
		"COMMENT:  ",
		"GPRINT:outbits:LAST:Last\: %6.2lf%Sbps\\r"
		);
		$opts2 = array( "--start", "-1w", "--lower-limit=0", "--vertical-label=B/s", "--title=Weekly Traffic $index",
		"DEF:inoctets=$rrd_name:input:AVERAGE",
		"DEF:outoctets=$rrd_name:output:AVERAGE",
		"CDEF:inbits=inoctets,8,*",
		"CDEF:outbits=outoctets,8,*",
		"AREA:inbits#00FF00:In traffic",
		"LINE1:outbits#0000FF:Out traffic\\r",
		"GPRINT:inbits:MAX:IN Max\: %6.2lf%Sbps",
		"COMMENT:  ",
		"GPRINT:inbits:AVERAGE:Avg\: %6.2lf%Sbps",
		"COMMENT:  ",
		"GPRINT:inbits:LAST:Last\: %6.2lf%Sbps\\r",
		"COMMENT:\\n",
		"GPRINT:outbits:MAX:OUT Max\: %6.2lf%Sbps",
		"COMMENT:  ",
		"GPRINT:outbits:AVERAGE:Avg\: %6.2lf%Sbps",
		"COMMENT:  ",
		"GPRINT:outbits:LAST:Last\: %6.2lf%Sbps\\r"
		);
		$opts3 = array( "--start", "-1m", "--lower-limit=0", "--vertical-label=B/s", "--title=Monthly Traffic $index",
		"DEF:inoctets=$rrd_name:input:AVERAGE",
		"DEF:outoctets=$rrd_name:output:AVERAGE",
		"CDEF:inbits=inoctets,8,*",
		"CDEF:outbits=outoctets,8,*",
		"AREA:inbits#00FF00:In traffic",
		"LINE1:outbits#0000FF:Out traffic\\r",
		"GPRINT:inbits:MAX:IN Max\: %6.2lf%Sbps",
		"COMMENT:  ",
		"GPRINT:inbits:AVERAGE:Avg\: %6.2lf%Sbps",
		"COMMENT:  ",
		"GPRINT:inbits:LAST:Last\: %6.2lf%Sbps\\r",
		"COMMENT:\\n",
		"GPRINT:outbits:MAX:OUT Max\: %6.2lf%Sbps",
		"COMMENT:  ",
		"GPRINT:outbits:AVERAGE:Avg\: %6.2lf%Sbps",
		"COMMENT:  ",
		"GPRINT:outbits:LAST:Last\: %6.2lf%Sbps\\r"
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


