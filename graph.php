<?php
include ("common.php");
include ("dbconnect.php");
navigation();

if ($_GET) {
	$id = $_GET['id'];
        if (!preg_match('/^[0-9]*$/', $id)) {
     		print "that sux";
        	exit;
        } else {
		try {
			$result = $db->query("SELECT CUSTOMERS.ID, CUSTOMERS.SVR_TEMPLATE, CUSTOMERS.STATE, CUSTOMERS.NAME, CUSTOMERS.ADDRESS, LPAD(HEX(CUSTOMERS.MAC_ADDRESS), 12, '0') as MAC_ADDRESS, ONU.NAME as ONU_NAME, ONU.PORTS as PORTS, ONU.RF as RF, OLT.NAME as OLT_NAME, INET_NTOA(OLT.IP_ADDRESS) as IP_ADDRESS, OLT.RO as RO, OLT_MODEL.TYPE, PON.NAME as PON_NAME, PON.PORT_ID as PORT_ID, PON.SLOT_ID as SLOT_ID, PON_ONU_ID, SVR_TEMPLATE.NAME as SVR_NAME from CUSTOMERS LEFT JOIN ONU on CUSTOMERS.ONU_MODEL=ONU.ID LEFT JOIN OLT on CUSTOMERS.OLT=OLT.ID LEFT JOIN OLT_MODEL on OLT.MODEL=OLT_MODEL.ID LEFT JOIN PON on CUSTOMERS.PON_PORT=PON.ID LEFT JOIN SVR_TEMPLATE on CUSTOMERS.SVR_TEMPLATE=SVR_TEMPLATE.ID where CUSTOMERS.ID=$id");
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
			exit;
		}

		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$ports = $row{'PORTS'};
			$rf = $row{'RF'};
			$customer_name = $row{'NAME'};
                        $olt_name = $row{'OLT_NAME'};
                        $mac_address = $row["MAC_ADDRESS"];

			if ($row{'TYPE'} == "1")
				$big_onu_id = $row{'SLOT_ID'} * 10000000 + $row{'PORT_ID'} * 100000 + $row{'PON_ONU_ID'};
			if ($row{'TYPE'} == "2")
                		$big_onu_id = type2id($row{'SLOT_ID'}, $row{'PORT_ID'}, $row{'PON_ONU_ID'});
			$olt_ip_address = $row["IP_ADDRESS"];
			$rrd_name = dirname(__FILE__) . "/rrd/" . $olt_ip_address . "_" . $big_onu_id . "_traffic.rrd";
			$rrd_power = dirname(__FILE__) . "/rrd/" . $olt_ip_address . "_" . $big_onu_id . "_power.rrd";

  			$opts = array( "--start", "-1d", "--lower-limit=0", "--vertical-label=B/s", "--title=Daily Traffic",
			 "DEF:inoctets=$rrd_name:input:AVERAGE",
			 "DEF:outoctets=$rrd_name:output:AVERAGE",
			 "AREA:inoctets#00FF00:In traffic",
			 "LINE1:outoctets#0000FF:Out traffic\\r",
			 "CDEF:inbits=inoctets",
			 "CDEF:outbits=outoctets",
			 "GPRINT:inbits:LAST:Last In\: %6.2lf %SBps",
			 "GPRINT:inbits:AVERAGE:Avg In\: %6.2lf %SBps",
			 "COMMENT:  ",
			 "GPRINT:inbits:MAX:Max In\: %6.2lf %SBps\\r",
			 "COMMENT:\\n",
			 "GPRINT:outbits:LAST:Last Out\: %6.2lf %SBps",
			 "GPRINT:outbits:AVERAGE:Avg Out\: %6.2lf %SBps",
			 "COMMENT: ",
			 "GPRINT:outbits:MAX:Max Out\: %6.2lf %SBps\\r"
		       );

			$pkts = array("unicast", "broadcast", "multicast");
        		foreach ($pkts as $tr) {
				$$tr = dirname(__FILE__) . "/rrd/" . $olt_ip_address . "_" . $big_onu_id . "_" . $tr . ".rrd";
				${$tr."_opts"} = array( "--start", "-1d", "--lower-limit=0", "--vertical-label=Pkts/s", "--title=Daily $tr",
				 "DEF:inoctets=${$tr}:input:AVERAGE",
				 "DEF:outoctets=${$tr}:output:AVERAGE",
				 "AREA:inoctets#00FF00:In",
				 "LINE1:outoctets#0000FF:Out\\r",
				 "CDEF:inbits=inoctets",
				 "CDEF:outbits=outoctets",
				 "GPRINT:inbits:LAST:Last In\: %6.0lf pkts/s",
                         	 "COMMENT:  ",
				 "GPRINT:inbits:MAX:Max In\: %6.0lf pkts/s\\r",
				 "COMMENT:\\n",
				 "GPRINT:outbits:LAST:Last Out\: %6.0lf pkts/s",
				 "COMMENT: ",
				 "GPRINT:outbits:MAX:Max Out\: %6.0lf pkts/s\\r"
			       );

			}
			
			for ($i=1; $i <= $row{'PORTS'}; $i++) {
				$octets_ethernet = dirname(__FILE__) . "/rrd/" . $olt_ip_address . "_" . $big_onu_id . "_ethernet_" . $i . ".rrd";
				${$i."_opts"} = array( "--start", "-1d", "--lower-limit=0", "--vertical-label=B/s", "--title=Daily Traffic Ethernet Port $i",
				 "DEF:inoctets=$octets_ethernet:input:AVERAGE",
				 "DEF:outoctets=$octets_ethernet:output:AVERAGE",
				 "AREA:inoctets#00FF00:In traffic",
				 "LINE1:outoctets#0000FF:Out traffic\\r",
				 "CDEF:inbits=inoctets",
				 "CDEF:outbits=outoctets",
				 "GPRINT:inbits:LAST:Last In\: %6.2lf %SBps",
				 "GPRINT:inbits:AVERAGE:Avg In\: %6.2lf %SBps",
				 "COMMENT:  ",
				 "GPRINT:inbits:MAX:Max In\: %6.2lf %SBps\\r",
				 "COMMENT:\\n",
				 "GPRINT:outbits:LAST:Last Out\: %6.2lf %SBps",
				 "GPRINT:outbits:AVERAGE:Avg Out\: %6.2lf %SBps",
				 "COMMENT: ",
				 "GPRINT:outbits:MAX:Max Out\: %6.2lf %SBps\\r"
				   );
				${$i."_url"} = $olt_ip_address . "_" . $big_onu_id . "_ethernet_" . $i . ".gif";
				${$i."_gif"} = dirname(__FILE__) . "/rrd/" . $olt_ip_address . "_" . $big_onu_id . "_ethernet_" . $i . ".gif";
				$ret = rrd_graph(${$i."_gif"}, ${$i."_opts"});
			}
			if ($rf == "1") {
				$opts4 = array( "--start", "-1d", "--vertical-label=dBm", "--title=Daily Power",
					"DEF:inoctets=$rrd_power:input:AVERAGE",
                                        "DEF:outoctets=$rrd_power:output:AVERAGE",
                                        "DEF:rx_olt=$rrd_power:rxolt:AVERAGE",
                                        "DEF:rf_in=$rrd_power:rfin:AVERAGE",
                                        "LINE2:rx_olt#D6213B:RX@OLT",
                                        "GPRINT:rx_olt:LAST:Last\: %6.2lf dBm\\r",
                                        "LINE2:outoctets#C6913B:TX@ONU",
                                        "GPRINT:outoctets:LAST:Last\: %6.2lf dBm\\r",
                                        "LINE2:inoctets#7FB37C:RX@ONU",
                                        "GPRINT:inoctets:LAST:Last\: %6.2lf dBm\\r",
                                        "LINE2:rf_in#FFD87C:RF@ONU",
                                        "GPRINT:rf_in:LAST:Last\: %6.2lf dBm\\r",
				);
			} else {
                                $opts4 = array( "--start", "-1d", "--vertical-label=dBm", "--title=Daily Power",
					"DEF:inoctets=$rrd_power:input:AVERAGE",
					"DEF:outoctets=$rrd_power:output:AVERAGE",
					"DEF:rx_olt=$rrd_power:rxolt:AVERAGE",
					"LINE2:rx_olt#D6213B:RX@OLT",
					"GPRINT:rx_olt:LAST:Last\: %6.2lf dBm\\r",
					"LINE2:outoctets#C6913B:TX@ONU",
					"GPRINT:outoctets:LAST:Last\: %6.2lf dBm\\r",
					"LINE2:inoctets#7FB37C:RX@ONU",
					"GPRINT:inoctets:LAST:Last\: %6.2lf dBm\\r",
				);
			}

			$rrd_traffic_url = $olt_ip_address . "_" . $big_onu_id . "_traffic.gif";
			$unicast_url =  $olt_ip_address . "_" . $big_onu_id . "_unicast.gif";
            $broadcast_url =  $olt_ip_address . "_" . $big_onu_id . "_broadcast.gif";
            $multicast_url =  $olt_ip_address . "_" . $big_onu_id . "_multicast.gif";
			$rrd_power_url = $olt_ip_address . "_" . $big_onu_id . "_power.gif";
			$rrd_traffic = dirname(__FILE__) . "/rrd/" . $olt_ip_address . "_" . $big_onu_id . "_traffic.gif";
			$rrd_power = dirname(__FILE__) . "/rrd/" . $olt_ip_address . "_" . $big_onu_id . "_power.gif";
			$unicast = dirname(__FILE__) . "/rrd/" . $olt_ip_address . "_" . $big_onu_id . "_unicast.gif";
			$broadcast = dirname(__FILE__) . "/rrd/" . $olt_ip_address . "_" . $big_onu_id . "_broadcast.gif";
			$multicast = dirname(__FILE__) . "/rrd/" . $olt_ip_address . "_" . $big_onu_id . "_multicast.gif";
			$ret = rrd_graph($rrd_traffic, $opts);
			$ret = rrd_graph($rrd_power, $opts4);
			$ret = rrd_graph($unicast, $unicast_opts);
            $ret = rrd_graph($broadcast, $broadcast_opts);
            $ret = rrd_graph($multicast, $multicast_opts);
			
			
			if( !is_array($ret) )
			  {
			    $err = rrd_error();
			    echo "rrd_graph() ERROR: $err\n";
			  }

		}
        print "<center><h2>RRD Graphs for <font color=blue> $customer_name </font> @ OLT::$olt_name ONU::$big_onu_id MAC::$mac_address</h2> ";
	print "<table>";
	print "<tr><td><p><a href=\"graph_traffic.php?id=" . $id . "\"><img src=\"rrd/" . $rrd_traffic_url . "\"></img></a></p></td>";
	$end = "1";
	for ($i=1; $i <= $ports; $i++) { 
		$name = ${$i."_url"};
		print "<td><p><a href=\"graph_onu_ethernet_ports.php?id=" . $id . "&port=" . $i . "\"><img src=\"rrd/" . $name . "\"></img></a></p></td>";
		$end++;
		if ($end == "2") {
			$end = "0";
			print "</tr><tr>";
		}
	}
	print "</tr>";
	print "<tr><td><p><a href=\"graph_packets.php?id=" . $id . "&type=unicast\"><img src=\"rrd/" . $unicast_url . "\"></img></a></p></td>";
	print "<td><p><a href=\"graph_packets.php?id=" . $id . "&type=broadcast\"><img src=\"rrd/" . $broadcast_url . "\"></img></a></p></td></tr>";
	print "<tr><td><p><a href=\"graph_packets.php?id=" . $id . "&type=multicast\"><img src=\"rrd/" . $multicast_url . "\"></img></a></p></td>";
	print "<td><p><a href=\"graph_power.php?id=" . $id . "\"><img src=\"rrd/" . $rrd_power_url . "\"></img></a></p></td></tr>";
	print "<table>";	
	}
}
?>


