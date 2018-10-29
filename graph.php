<?php
include_once("common.php");
include_once("dbconnect.php");

if ($_GET) {
	$type = $_GET['type'];
	$id = $_GET['id'];
	if (!preg_match('/^[0-9]*$/', $id)) {
		print "that sux";
		exit;
	} else {
		try {
			$result = $db->query("SELECT CUSTOMERS.ID, CUSTOMERS.NAME as NAME, SN, PON_ONU_ID, CUSTOMERS.SERVICE, CUSTOMERS.PON_PORT, CUSTOMERS.OLT, OLT.ID, INET_NTOA(OLT.IP_ADDRESS) as IP_ADDRESS, OLT.NAME as OLT_NAME, OLT.RO as RO, OLT.RW as RW, OLT_MODEL.TYPE, PON.ID as PON_ID, PON.PORT_ID as PORT_ID, PON.SLOT_ID as SLOT_ID, SERVICES.ID, SERVICES.LINE_PROFILE_ID, SERVICES.SERVICE_PROFILE_ID, SERVICE_PROFILE.PORTS, SERVICE_PROFILE.HGU, SERVICE_PROFILE.RF as RF from CUSTOMERS LEFT JOIN OLT on CUSTOMERS.OLT=OLT.ID LEFT JOIN OLT_MODEL on OLT.MODEL=OLT_MODEL.ID LEFT JOIN PON on CUSTOMERS.PON_PORT=PON.ID LEFT JOIN SERVICES on CUSTOMERS.SERVICE=SERVICES.ID LEFT JOIN SERVICE_PROFILE on SERVICES.SERVICE_PROFILE_ID=SERVICE_PROFILE.ID where CUSTOMERS.ID = '$id'");
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
			exit;
		}

		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$olt_ip_address = $row["IP_ADDRESS"];
			$sn = $row["SN"];
			$pon_onu_id = $row["PON_ONU_ID"];
			$olt_name = $row["OLT_NAME"];
			$customer_name = $row["NAME"];
			$rf = $row{'RF'};
			$rrd_name = dirname(__FILE__) . "/rrd/" . $sn . "_" . $type . ".rrd";
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
				$opts2 = array( "--start", "-1w", "--lower-limit=0", "--vertical-label=Pkts/s", "--title=Weekly $type",
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
				$opts3 = array( "--start", "-1m", "--lower-limit=0", "--vertical-label=Pkts/s", "--title=Monthly $type",
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
			
			if ($type == "traffic" || preg_match('/^[1-9][0-9]*$/', $type)) {
				if (preg_match('/^[1-9][0-9]*$/', $type) == "1")
					$type = "Traffic UNI " . $type;
				$opts = array( "--start", "-1d", "--lower-limit=0", "--vertical-label=B/s", "--title=Daily $type",
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
				$opts2 = array( "--start", "-1w", "--lower-limit=0", "--vertical-label=B/s", "--title=Weekly $type",
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
				$opts3 = array( "--start", "-1m", "--lower-limit=0", "--vertical-label=B/s", "--title=Monthly $type",
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
			
			if ($type == "power") {	
				if ($rf == "Yes") {
					$opts = array( "--start", "-1d", "--vertical-label=dBm", "--title=Daily Power",
					"DEF:inoctets=$rrd_name:input:AVERAGE",
					"DEF:outoctets=$rrd_name:output:AVERAGE",
					"DEF:rx_olt=$rrd_name:rxolt:AVERAGE",
					"DEF:rf_in=$rrd_name:rfin:AVERAGE",
					"LINE2:rx_olt#D6213B:RX@OLT",
					"GPRINT:rx_olt:LAST:Last\: %6.2lf dBm\\r",
					"LINE2:outoctets#C6913B:TX@ONU",
					"GPRINT:outoctets:LAST:Last\: %6.2lf dBm\\r",
					"LINE2:inoctets#7FB37C:RX@ONU",
					"GPRINT:inoctets:LAST:Last\: %6.2lf dBm\\r",
					"LINE2:rf_in#FFD87C:RF@ONU",
					"GPRINT:rf_in:LAST:Last\: %6.2lf dBm\\r",
					);
					$opts2 = array( "--start", "-1w", "--vertical-label=dBm", "--title=Weekly Power",
					"DEF:inoctets=$rrd_name:input:AVERAGE",
					"DEF:outoctets=$rrd_name:output:AVERAGE",
					"DEF:rx_olt=$rrd_name:rxolt:AVERAGE",
					"DEF:rf_in=$rrd_name:rfin:AVERAGE",
					"LINE2:rx_olt#D6213B:RX@OLT",
					"GPRINT:rx_olt:MIN:Min\: %6.2lf dBm\\r",
					"LINE2:outoctets#C6913B:TX@ONU",
					"GPRINT:outoctets:MIN:Min\: %6.2lf dBm\\r",
					"LINE2:inoctets#7FB37C:RX@ONU",
					"GPRINT:inoctets:MIN:Min\: %6.2lf dBm\\r",
					"LINE2:rf_in#FFD87C:RF@ONU",
					"GPRINT:rf_in:MIN:Min\: %6.2lf dBm\\r",
					);
					$opts3 = array( "--start", "-1m", "--vertical-label=dBm", "--title=Monthly Power",
					"DEF:inoctets=$rrd_name:input:AVERAGE",
					"DEF:outoctets=$rrd_name:output:AVERAGE",
					"DEF:rx_olt=$rrd_name:rxolt:AVERAGE",
					"DEF:rf_in=$rrd_name:rfin:AVERAGE",
					"LINE2:rx_olt#D6213B:RX@OLT",
					"GPRINT:rx_olt:MIN:Min\: %6.2lf dBm\\r",
					"LINE2:outoctets#C6913B:TX@ONU",
					"GPRINT:outoctets:MIN:Min\: %6.2lf dBm\\r",
					"LINE2:inoctets#7FB37C:RX@ONU",
					"GPRINT:inoctets:MIN:Min\: %6.2lf dBm\\r",
					"LINE2:rf_in#FFD87C:RF@ONU",
					"GPRINT:rf_in:MIN:Min\: %6.2lf dBm\\r",
					);
				} else {
					$opts = array( "--start", "-1d", "--vertical-label=dBm", "--title=Daily Power",
					"DEF:inoctets=$rrd_name:input:AVERAGE",
					"DEF:outoctets=$rrd_name:output:AVERAGE",
					"DEF:rx_olt=$rrd_name:rxolt:AVERAGE",
					"LINE2:rx_olt#D6213B:RX@OLT",
					"GPRINT:rx_olt:LAST:Last\: %6.2lf dBm\\r",
					"LINE2:outoctets#C6913B:TX@ONU",
					"GPRINT:outoctets:LAST:Last\: %6.2lf dBm\\r",
					"LINE2:inoctets#7FB37C:RX@ONU",
					"GPRINT:inoctets:LAST:Last\: %6.2lf dBm\\r",
					);
					$opts2 = array( "--start", "-1w", "--vertical-label=dBm", "--title=Weekly Power",
					"DEF:inoctets=$rrd_name:input:AVERAGE",
					"DEF:outoctets=$rrd_name:output:AVERAGE",
					"DEF:rx_olt=$rrd_name:rxolt:AVERAGE",
					"LINE2:rx_olt#D6213B:RX@OLT",
					"GPRINT:rx_olt:LAST:Last\: %6.2lf dBm\\r",
					"LINE2:outoctets#C6913B:TX@ONU",
					"GPRINT:outoctets:LAST:Last\: %6.2lf dBm\\r",
					"LINE2:inoctets#7FB37C:RX@ONU",
					"GPRINT:inoctets:LAST:Last\: %6.2lf dBm\\r",
					);
					$opts3 = array( "--start", "-1m", "--vertical-label=dBm", "--title=Monthly Power",
					 "DEF:inoctets=$rrd_name:input:AVERAGE",
					 "DEF:outoctets=$rrd_name:output:AVERAGE",
					 "DEF:rx_olt=$rrd_name:rxolt:AVERAGE",
					 "LINE2:rx_olt#D6213B:RX@OLT",
					 "GPRINT:rx_olt:MAX:Max\: %6.2lf dBm\\r",
					 "LINE2:outoctets#C6913B:TX@ONU",
					 "GPRINT:outoctets:MAX:Max\: %6.2lf dBm\\r",
					 "LINE2:inoctets#7FB37C:RX@ONU",
					 "GPRINT:inoctets:MAX:Max\: %6.2lf dBm\\r",
					);
				}
			
			}
			$rrd_traffic_url = $sn . "_" . $type . ".gif";
			$rrd_traffic_url_week = $sn . "_" . $type . "_week.gif";
			$rrd_traffic_url_month = $sn . "_" . $type . "_month.gif";
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
	print "<center><h3>RRD $type Graphs ONU:" . $pon_onu_id ."</h3> ";
	print "<p><img src=\"rrd/" . $rrd_traffic_url . "\"></img></p>";
	print "<p><img src=\"rrd/" . $rrd_traffic_url_week . "\"></img></p>";
	print "<p><img src=\"rrd/" . $rrd_traffic_url_month . "\"></img></p>";
	print "<center>";
	}
}
?>


