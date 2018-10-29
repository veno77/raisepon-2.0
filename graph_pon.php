<?php
include ("common.php");
include ("dbconnect.php");

if ($_GET) {
	$type = $_GET['type'];
	$id = $_GET['id'];
	if (!preg_match('/^[0-9]*$/', $id)) {
		print "that sux";
		exit;
	} else {
		try {
			 $result = $db->query("SELECT PON.ID, PON.SLOT_ID, PON.PORT_ID, INET_NTOA(OLT.IP_ADDRESS) as IP_ADDRESS from PON LEFT JOIN OLT on PON.OLT=OLT.ID where PON.ID = '$id'");
		} catch (PDOException $e) {
				echo "Connection Failed:" . $e->getMessage() . "\n";
				exit;
		}

		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$pon_id = type2ponid($row{'SLOT_ID'},$row{'PORT_ID'});	
			$olt_ip_address = $row["IP_ADDRESS"];
			$rrd_name = dirname(__FILE__) . "/rrd/" . $olt_ip_address . "_" . $pon_id . "_" . $type . ".rrd";
			if ($type == "unicast" || $type == "broadcast" || $type == "multicast") {	
				$opts = array( "--start", "-1d", "--lower-limit=0", "--vertical-label=pkts/s", "--title=Daily $type",
				"DEF:inoctets=$rrd_name:input:AVERAGE",
				"DEF:outoctets=$rrd_name:output:AVERAGE",
				"AREA:inoctets#00FF00:In",
				"LINE1:outoctets#0000FF:Out\\r",
				"GPRINT:inoctets:MAX:IN Max\: %6.0lf pkts/s",
				"GPRINT:inoctets:AVERAGE:Avg\: %6.0lf pkts/s",
				"GPRINT:inoctets:LAST:Last\: %6.0lf pkts/s\\r",
				"GPRINT:outoctets:MAX:OUT Max\: %6.0lf pkts/s",
				"GPRINT:outoctets:AVERAGE:Avg\: %6.0lf pkts/s",
				"GPRINT:outoctets:LAST:Last\: %6.0lf pkts/s\\r"
				);
				$opts2 = array( "--start", "-1w", "--lower-limit=0", "--vertical-label=pkts/s", "--title=Weekly $type",
				"DEF:inoctets=$rrd_name:input:AVERAGE",
				"DEF:outoctets=$rrd_name:output:AVERAGE",
				"AREA:inoctets#00FF00:In",
				"LINE1:outoctets#0000FF:Out\\r",
				"GPRINT:inoctets:MAX:IN Max\: %6.0lf pkts/s",
				"GPRINT:inoctets:AVERAGE:Avg\: %6.0lf pkts/s",
				"GPRINT:inoctets:LAST:Last\: %6.0lf pkts/s\\r",
				"GPRINT:outoctets:MAX:OUT Max\: %6.0lf pkts/s",
				"GPRINT:outoctets:AVERAGE:Avg\: %6.0lf pkts/s",
				"GPRINT:outoctets:LAST:Last\: %6.0lf pkts/s\\r"
				);
				$opts3 = array( "--start", "-1m", "--lower-limit=0", "--vertical-label=pkts/s", "--title=Monthly $type",
				"DEF:inoctets=$rrd_name:input:AVERAGE",
				"DEF:outoctets=$rrd_name:output:AVERAGE",
				"AREA:inoctets#00FF00:In",
				"LINE1:outoctets#0000FF:Out\\r",
				"GPRINT:inoctets:MAX:IN Max\: %6.0lf pkts/s",
				"GPRINT:inoctets:AVERAGE:Avg\: %6.0lf pkts/s",
				"GPRINT:inoctets:LAST:Last\: %6.0lf pkts/s\\r",
				"GPRINT:outoctets:MAX:OUT Max\: %6.0lf pkts/s",
				"GPRINT:outoctets:AVERAGE:Avg\: %6.0lf pkts/s",
				"GPRINT:outoctets:LAST:Last\: %6.0lf pkts/s\\r"
				);
			}
			
			if ($type == "traffic") {
				$opts = array( "--start", "-1d", "--lower-limit=0", "--vertical-label=b/s", "--title=Daily $type",
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
				$opts2 = array( "--start", "-1w", "--lower-limit=0", "--vertical-label=b/s", "--title=Weekly $type",
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
				$opts3 = array( "--start", "-1m", "--lower-limit=0", "--vertical-label=b/s", "--title=Monthly $type",
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
			}
			
			$rrd_traffic_url = $olt_ip_address . "_" . $pon_id . "_" . $type . ".gif";
			$rrd_traffic_url_week = $olt_ip_address . "_" . $pon_id . "_" . $type . "_week.gif";
			$rrd_traffic_url_month = $olt_ip_address . "_" . $pon_id . "_" . $type . "_month.gif";
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
		print "<center><h2>RRD $type Graphs for PON: $olt_ip_address :: $pon_id</h2> ";
		print "<p><img src=\"rrd/" . $rrd_traffic_url . "\"></img></p>";
		print "<p><img src=\"rrd/" . $rrd_traffic_url_week . "\"></img></p>";
		print "<p><img src=\"rrd/" . $rrd_traffic_url_month . "\"></img></p>";
	}
}
?>


