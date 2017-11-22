<?php
include ("common.php");
include ("dbconnect.php");
if ($_GET) {
        $id = $_GET['id'];
        if (!preg_match('/^[0-9]*$/', $id)) {
        print "that sux";
        exit;
        } else {
 try {
        $result = $db->query("SELECT NAME, INET_NTOA(OLT.IP_ADDRESS) as IP_ADDRESS, RO from OLT where ID='$id'");
        } catch (PDOException $e) {
        echo "Connection Failed:" . $e->getMessage() . "\n";
        exit;
        }

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {

        $ip_address = $row['IP_ADDRESS'];
	$olt_name = $row['NAME'];
        print "<center><h2>RRD Graphs for OLT: $olt_name</h2> ";

        foreach (range(1, 18) as $port_number) {
        $rrd_name = dirname(__FILE__) . "/rrd/" . $ip_address . "_" . $port_number . "_traffic.rrd";

  	$opts = array( "--start", "-1d", "--lower-limit=0", "--vertical-label=B/s", "--title=Daily Traffic for Port " . $port_number,
                 "DEF:inoctets=$rrd_name:input:AVERAGE",
                 "DEF:outoctets=$rrd_name:output:AVERAGE",
    		 "CDEF:inbits=inoctets",
                 "CDEF:outbits=outoctets",
                 "AREA:inbits#00FF00:In traffic",
                 "LINE1:outbits#0000FF:Out traffic\\r",
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
  $rrd_traffic_url = $ip_address . "_" . $port_number . "_traffic.gif";
  $rrd_traffic = dirname(__FILE__) . "/rrd/" . $ip_address . "_" . $port_number . "_traffic.gif";
  $ret = rrd_graph($rrd_traffic, $opts);

  if( !is_array($ret) )
  {
    $err = rrd_error();
    echo "rrd_graph() ERROR: $err\n";
  }
  print "<p><a href=\"graph_traffic_olt.php?id=" . $id . "&port=" . $port_number . "\"><img src=\"rrd/" . $rrd_traffic_url . "\"></img></a></p>";
  }

 }
}
}
?>


