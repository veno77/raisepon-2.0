<?php
include ("header.php");
include ("common.php");
include ("dbconnect.php");
include ("navigation.php");

$OLT_ID = $PON_ID = $pon_port = $traffic = $power = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if ($_POST["SUBMIT"]) {
		$submit = test_input($_POST["SUBMIT"]);
		}
	if ($submit == "LOAD") {
		$OLT_ID = $_POST["olt_port"];
		$PON_ID = $_POST["pon_port"];
		$graph = $_POST["graph"];
	}

	if ($OLT_ID) {
		try {
			$rst = $db->query("SELECT NAME from OLT WHERE ID=" . $OLT_ID);
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
			exit;
		}

		while ($row = $rst->fetch(PDO::FETCH_ASSOC)) {
			$OLT_NAME = $row{'NAME'};
		}

	}

	if ($PON_ID) {
		try {
			$rst = $db->query("SELECT * from PON WHERE ID=" . $PON_ID);
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
			exit;
		}

		while ($row = $rst->fetch(PDO::FETCH_ASSOC)) {
			$PON_NAME = $row{'NAME'};
			$SLOT_ID = $row{'SLOT_ID'};
			$PORT_ID = $row{'PORT_ID'};
		}

	}

}

print "<form action=\"graphs.php\" method=\"post\">";
print "<center>OLT:<select id=\"select-olt\" name=\"olt_port\">";
print "<option value=\"\" class=\"rhth\">Select OLT</option>";
try {
	$result = $db->query("SELECT * from OLT");
} catch (PDOException $e) {
        echo "Connection Failed:" . $e->getMessage() . "\n";
        exit;
}

while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	print "<option value=\"" . $row{'ID'} ."\">" . $row{'NAME'} . "</option>";
}


print "</select>";
print "&nbsp;";
print "<select id=\"select-pon\" name=\"pon_port\">";
print "<option value=\"\">PON PORT</option>";
print "</select>";
print "&nbsp;";
print "<select id=\"select-graph\" name=\"graph\">";
print "<option value=\"traffic\">Traffic</option>";
print "<option value=\"unicast\">Unicast</option>";
print "<option value=\"broadcast\">Broadcast</option>";
print "<option value=\"multicast\">Multicast</option>";
print "<option value=\"power\">Power</option>";
print "</select>";

print "<input type=\"submit\" name=\"SUBMIT\" value=\"LOAD\">";
print "</form>";

if ($PON_ID) {
	$where = "PON.ID='" . $PON_ID ."' and OLT.ID='" . $OLT_ID . "'";

	try {
		$result = $db->query("SELECT CUSTOMERS.ID as ID, CUSTOMERS.NAME, CUSTOMERS.ADDRESS, SN, OLT.NAME as OLT_NAME, INET_NTOA(OLT.IP_ADDRESS) as IP_ADDRESS, OLT.RO as RO, OLT_MODEL.TYPE, PON.NAME as PON_NAME, PON.PORT_ID as PORT_ID, PON.SLOT_ID as SLOT_ID, PON_ONU_ID from CUSTOMERS LEFT JOIN OLT on CUSTOMERS.OLT=OLT.ID LEFT JOIN OLT_MODEL on OLT.MODEL=OLT_MODEL.ID LEFT JOIN PON on CUSTOMERS.PON_PORT=PON.ID WHERE " . $where ." order by PON_ONU_ID");
	} catch (PDOException $e) {
		echo "Connection Failed:" . $e->getMessage() . "\n";
		exit;
	} 
	print "<p><center>";
	print "<h1>OLT: " . $OLT_NAME . "</h1><h2>PON: " . $PON_NAME . "   (" . $SLOT_ID . "/" . $PORT_ID . ")</h2><br><br>"  ;
	$i= 0;
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
		$i++;
		if($row{'TYPE'} == "1")
			$big_onu_id = $row{'SLOT_ID'} * 10000000 + $row{'PORT_ID'} * 100000 + $row{'PON_ONU_ID'};
		if($row{'TYPE'} == "2")
			$big_onu_id = type2id($row{'SLOT_ID'}, $row{'PORT_ID'}, $row{'PON_ONU_ID'});	
		$pon_snmp_id = $row{'SLOT_ID'} . "000000" . $row{'PORT_ID'} ;
		$olt_ip_address = $row["IP_ADDRESS"];
		if ($graph == "traffic") {
        		$rrd_name = dirname(__FILE__) . "/rrd/" . $olt_ip_address . "_" . $big_onu_id . "_traffic.rrd";
			$rrd_pon =  dirname(__FILE__) . "/rrd/" . $olt_ip_address . "_" . $pon_snmp_id . "_traffic.rrd";

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
			$opts_pon = array( "--start", "-1d", "--lower-limit=0", "--vertical-label=B/s", "--title=Daily Traffic",
				 "DEF:inoctets=$rrd_pon:input:AVERAGE",
				 "DEF:outoctets=$rrd_pon:output:AVERAGE",
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

			$rrd_traffic_url = $olt_ip_address . "_" . $big_onu_id . "_traffic.gif";
			$rrd_traffic = dirname(__FILE__) . "/rrd/" . $olt_ip_address . "_" . $big_onu_id . "_traffic.gif";
			$pon_traffic_url = $olt_ip_address . "_" . $pon_snmp_id . "_traffic.gif";
			$pon_traffic = dirname(__FILE__) . "/rrd/" . $olt_ip_address . "_" . $pon_snmp_id . "_traffic.gif";

			$ret = rrd_graph($rrd_traffic, $opts);
			if($i == '1')
				$ret = rrd_graph($pon_traffic, $opts_pon);
			if( !is_array($ret) )
  			{
				$err = rrd_error();
				echo "rrd_graph() ERROR: $err\n";
			}
			if($i == '1') {
				print "<a href=\"graph_traffic_pon.php?id=" . $PON_ID . "\">PON:" . $row{'SLOT_ID'} . "/" . $row{'PORT_ID'} . "</a>";
				print "<p><a href=\"graph_traffic_pon.php?id=" . $PON_ID . "\"><img src=\"rrd/" . $pon_traffic_url . "\"></img></a></p>";
			}
			print "<a href=\"graph_traffic.php?id=" . $row{'ID'} . "\">ONU:" . $row{'PON_ONU_ID'} . "</a>";
			print "<p><a href=\"graph_traffic.php?id=" . $row{'ID'} . "\"><img src=\"rrd/" . $rrd_traffic_url . "\"></img></a></p>";
		}
     		if ($graph == "unicast" || $graph == "broadcast" || $graph == "multicast") {
                        $rrd_name = dirname(__FILE__) . "/rrd/" . $olt_ip_address . "_" . $big_onu_id . "_" . $graph . ".rrd";
                        $rrd_pon =  dirname(__FILE__) . "/rrd/" . $olt_ip_address . "_" . $pon_snmp_id . "_" . $graph . ".rrd";
                        $opts = array( "--start", "-1d", "--lower-limit=0", "--vertical-label=Pkts/s", "--title=Daily $graph",
                                 "DEF:inoctets=$rrd_name:input:AVERAGE",
                                 "DEF:outoctets=$rrd_name:output:AVERAGE",
                                 "AREA:inoctets#00FF00:In",
                                 "LINE1:outoctets#0000FF:Out\\r",
                                 "CDEF:inbits=inoctets",
                                 "CDEF:outbits=outoctets",
                                 "GPRINT:inbits:LAST:Last In\: %6.0lf Pkts/s",
                                 "GPRINT:inbits:AVERAGE:Avg In\: %6.0lf Pkts/s",
                                 "COMMENT:  ",
                                 "GPRINT:inbits:MAX:Max In\: %6.0lf Pkts/s\\r",
                                 "COMMENT:\\n",
                                 "GPRINT:outbits:LAST:Last Out\: %6.0lf Pkts/s",
                                 "GPRINT:outbits:AVERAGE:Avg Out\: %6.0lf Pkts/s",
                                 "COMMENT: ",
                                 "GPRINT:outbits:MAX:Max Out\: %6.0lf Pkts/s\\r"
                        );
                        $opts_pon = array( "--start", "-1d", "--lower-limit=0", "--vertical-label=Pkts/s", "--title=Daily $graph",
                                 "DEF:inoctets=$rrd_pon:input:AVERAGE",
                                 "DEF:outoctets=$rrd_pon:output:AVERAGE",
                                 "AREA:inoctets#00FF00:In",
                                 "LINE1:outoctets#0000FF:Out \\r",
                                 "CDEF:inbits=inoctets",
                                 "CDEF:outbits=outoctets",
                                 "GPRINT:inbits:LAST:Last In\: %6.0lf Pkts/s",
                                 "GPRINT:inbits:AVERAGE:Avg In\: %6.0lf Pkts/s",
                                 "COMMENT:  ",
                                 "GPRINT:inbits:MAX:Max In\: %6.0lf Pkts/s\\r",
                                 "COMMENT:\\n",
                                 "GPRINT:outbits:LAST:Last Out\: %6.0lf Pkts/s",
                                 "GPRINT:outbits:AVERAGE:Avg Out\: %6.0lf Pkts/s",
                                 "COMMENT: ",
                                 "GPRINT:outbits:MAX:Max Out\: %6.0lf Pkts/s\\r"
                        );

                        $rrd_traffic_url = $olt_ip_address . "_" . $big_onu_id . "_" . $graph . ".gif";
                        $rrd_traffic = dirname(__FILE__) . "/rrd/" . $olt_ip_address . "_" . $big_onu_id . "_" . $graph . ".gif";
                        $pon_traffic_url = $olt_ip_address . "_" . $pon_snmp_id . "_" . $graph . ".gif";
                        $pon_traffic = dirname(__FILE__) . "/rrd/" . $olt_ip_address . "_" . $pon_snmp_id . "_" . $graph . ".gif";
                        $ret = rrd_graph($rrd_traffic, $opts);
                        if($i == '1')
                                $ret = rrd_graph($pon_traffic, $opts_pon);
                        if( !is_array($ret) )
                        {
                                $err = rrd_error();
                                echo "rrd_graph() ERROR: $err\n";
                        }
                        if($i == '1') {
                                print "<a href=\"graph_packets_pon.php?id=" . $PON_ID . "&type=" . $graph . "\">PON:" . $row{'SLOT_ID'} . "/" . $row{'PORT_ID'} . "</a>";
                                print "<p><a href=\"graph_packets_pon.php?id=" . $PON_ID . "&type=" . $graph . "\"><img src=\"rrd/" . $pon_traffic_url . "\"></img></a></p>";
                        }
                        print "<a href=\"graph_packets.php?id=" . $row{'ID'} . "&type=" . $graph . "\">ONU:" . $row{'PON_ONU_ID'} . "</a>";
                        print "<p><a href=\"graph_packets.php?id=" . $row{'ID'} . "&type=" . $graph . "\"><img src=\"rrd/" . $rrd_traffic_url . "\"></img></a></p>";
                }


		if ($graph == "power") {
			$rrd_power = dirname(__FILE__) . "/rrd/" . $olt_ip_address . "_" . $big_onu_id . "_power.rrd";
			$opts = array( "--start", "-1d", "--vertical-label=dBm", "--title=Daily Power",
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
			$rrd_power_url = $olt_ip_address . "_" . $big_onu_id . "_power.gif";
			$rrd_power = dirname(__FILE__) . "/rrd/" . $olt_ip_address . "_" . $big_onu_id . "_power.gif";
			$ret = rrd_graph($rrd_power, $opts);

			if( !is_array($ret) )
			{
				$err = rrd_error();
				echo "rrd_graph() ERROR: $err\n";
  			}
			print "<a href=\"graph_power.php?id=" . $row{'ID'} . "\">ONU:" . $row{'PON_ONU_ID'} . "</a>";
			print "<p><a href=\"graph_power.php?id=" . $row{'ID'} . "\"><img src=\"rrd/" . $rrd_power_url . "\"></img></a></p>";
		}
		
	}
}
?>

