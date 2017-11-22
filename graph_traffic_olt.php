<?php
include ("common.php");
include ("dbconnect.php");
if ($_GET) {
        $id = $_GET['id'];
	$port_number = $_GET['port'];
        if (!preg_match('/^[0-9]*$/', $id)) {
        print "that sux";
        exit;
        } else {
try {
        $result = $db->query("SELECT INET_NTOA(OLT.IP_ADDRESS) as IP_ADDRESS, RO  from OLT where ID='$id'");
} catch (PDOException $e) {
        echo "Connection Failed:" . $e->getMessage() . "\n";
        exit;
}
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $olt_ip_address = $row["IP_ADDRESS"];
        $rrd_name = dirname(__FILE__) . "/rrd/" . $olt_ip_address . "_" . $port_number . "_traffic.rrd";

  $opts = array( "--start", "-1d", "--vertical-label=B/s", "--title=Daily Traffic",
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
  $opts2 = array( "--start", "-1w", "--vertical-label=B/s", "--title=Weekly Traffic",
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
  $opts3 = array( "--start", "-1m", "--vertical-label=B/s", "--title=Monthly Traffic",
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
  $rrd_traffic_url = $olt_ip_address . "_" . $port_number . "_traffic.gif";
  $rrd_traffic_url_week = $olt_ip_address . "_" . $port_number . "_traffic_week.gif";
  $rrd_traffic_url_month = $olt_ip_address . "_" . $port_number . "_traffic_month.gif";
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
print "<center><h2>RRD Traffic Graphs for OLT: $olt_ip_address :: PORT: $port_number</h2> ";
print "<p><img src=\"rrd/" . $rrd_traffic_url . "\"></img></p>";
print "<p><img src=\"rrd/" . $rrd_traffic_url_week . "\"></img></p>";
print "<p><img src=\"rrd/" . $rrd_traffic_url_month . "\"></img></p>";
}
}
?>


