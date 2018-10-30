<?php

include_once("header.php");
include_once("common.php");
include_once("dbconnect.php");
include_once("navigation.php"); 

$OLT_ID = $PON_ID = $pon_port = $traffic = $power = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	
		$OLT_ID = $_POST["olt_port"];
		$PON_ID = $_POST["pon_port"];
		$graph = $_POST["graph"];
	

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

}else{
	print "<form id=\"graphs\">";
	?>
	<div class="container">
		<div class="row text-center">
			<div class="col-md-2 col-md-offset-3">
				<div class="form-group">
					<label for="select-olt">OLT:</label>
					<select class="form-control" id="select-olt" name="olt_port">
						<option value="" class="rhth">OLT</option>
						<?php 
						try {
								$result = $db->query("SELECT * from OLT");
							} catch (PDOException $e) {
								echo "Connection Failed:" . $e->getMessage() . "\n";
								exit;
							}

							while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
								print "<option value=\"" . $row{'ID'} ."\">" . $row{'NAME'} . "</option>";
							}
						?>
					</select>
				</div>
			</div>
			<div class="col-md-2">
				<div class="form-group">
					<label for="select-pon">PON:</label>
					<select class="form-control" id="select-pon" name="pon_port">
						<option value="" class="rhth">PON PORT</option>
					</select>
				</div>
			</div>
			<div class="col-md-2">
				<div class="form-group">
					<label for="select-graph">GRAPH:</label>
					<select class="form-control" id="select-graph" name="graph">
						<option value="traffic">Traffic</option>
						<option value="unicast">Unicast</option>
						<option value="broadcast">Broadcast</option>
						<option value="multicast">Multicast</option>
						<option value="power">Power</option>
					</select>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="text-center">
				<div class="form-group">
					<button id="load" type="button" class="btn btn-info" onClick="LoadGraphs();">LOAD</button>
				</div>
			</div>
		</div>		
	</div>
	<div class="container">
		<div class="text-center">
	<?php
	print "</form>";
}
print "<div id=\"output\">";
if ($PON_ID) {
	$where = "PON.ID='" . $PON_ID ."' and OLT.ID='" . $OLT_ID . "'";

	try {
		$result = $db->query("SELECT CUSTOMERS.ID as ID, CUSTOMERS.NAME, CUSTOMERS.ADDRESS, SN, OLT.NAME as OLT_NAME, INET_NTOA(OLT.IP_ADDRESS) as IP_ADDRESS, OLT.RO as RO, OLT_MODEL.TYPE, PON.NAME as PON_NAME, PON.PORT_ID as PORT_ID, PON.SLOT_ID as SLOT_ID, PON_ONU_ID from CUSTOMERS LEFT JOIN OLT on CUSTOMERS.OLT=OLT.ID LEFT JOIN OLT_MODEL on OLT.MODEL=OLT_MODEL.ID LEFT JOIN PON on CUSTOMERS.PON_PORT=PON.ID WHERE " . $where ." order by PON_ONU_ID");
	} catch (PDOException $e) {
		echo "Connection Failed:" . $e->getMessage() . "\n";
		exit;
	} 
	print "<div class=\"text-center\">";
	print "<h1>OLT: " . $OLT_NAME . "</h1><h2>PON: " . $PON_NAME . "   (" . $SLOT_ID . "/" . $PORT_ID . ")</h2></div>"  ;
	$i= 0;
	print "<div class=\"row justify-content-md-center\"><div class=\"table-responsive \"><table class=\"table text-center \"><tr>";
	$end = "0";
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
		$i++;	
		$big_onu_id = type2id($row{'SLOT_ID'}, $row{'PORT_ID'}, $row{'PON_ONU_ID'});	
		$pon_snmp_id = type2ponid($row{'SLOT_ID'},$row{'PORT_ID'});	
		$olt_ip_address = $row["IP_ADDRESS"];
		$sn = $row["SN"];
		
		
		if ($graph == "traffic") {
        	$rrd_name = dirname(__FILE__) . "/rrd/" . $sn . "_traffic.rrd";
			$rrd_pon =  dirname(__FILE__) . "/rrd/" . $olt_ip_address . "_" . $pon_snmp_id . "_traffic.rrd";

			$opts = array( "--start", "-1d", "--lower-limit=0", "--vertical-label=b/s", "--title=Daily Traffic",
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
			$opts_pon = array( "--start", "-1d", "--lower-limit=0", "--vertical-label=b/s", "--title=Daily Traffic",
			"DEF:inoctets=$rrd_pon:input:AVERAGE",
			"DEF:outoctets=$rrd_pon:output:AVERAGE",
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

			$rrd_traffic_url = $sn . "_traffic.gif";
			$rrd_traffic = dirname(__FILE__) . "/rrd/" . $sn . "_traffic.gif";
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
				print "<td colspan=\"2\">PON:" . $row{'SLOT_ID'} . "/" . $row{'PORT_ID'} . "</p>" ;
				print "<p><p onClick=\"graph_pon('". $PON_ID . "', 'traffic');\"><img src=\"rrd/" . $pon_traffic_url . "\"></img></p></td></tr><tr>";
			}
			print "<td><p> ONU:" . $row{'PON_ONU_ID'} . "</p>" ;
			print "<p onClick=\"graph_onu('". $row{'ID'} . "', 'traffic');\"><img src=\"rrd/" . $rrd_traffic_url . "\"></img></p></td>";
		}
		
		
		if ($graph == "unicast" || $graph == "broadcast" || $graph == "multicast") {
			$rrd_name = dirname(__FILE__) . "/rrd/" . $sn . "_" . $graph . ".rrd";
			$rrd_pon =  dirname(__FILE__) . "/rrd/" . $olt_ip_address . "_" . $pon_snmp_id . "_" . $graph . ".rrd";
			$opts = array( "--start", "-1d", "--lower-limit=0", "--vertical-label=pkts/s", "--title=Daily $graph",
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
			$opts_pon = array( "--start", "-1d", "--lower-limit=0", "--vertical-label=pkts/s", "--title=Daily $graph",
			"DEF:inoctets=$rrd_pon:input:AVERAGE",
			"DEF:outoctets=$rrd_pon:output:AVERAGE",
			"AREA:inoctets#00FF00:In",
			"LINE1:outoctets#0000FF:Out\\r",
			"GPRINT:inoctets:MAX:IN Max\: %6.0lf pkts/s",
			"GPRINT:inoctets:AVERAGE:Avg\: %6.0lf pkts/s",
			"GPRINT:inoctets:LAST:Last\: %6.0lf pkts/s\\r",
			"GPRINT:outoctets:MAX:OUT Max\: %6.0lf pkts/s",
			"GPRINT:outoctets:AVERAGE:Avg\: %6.0lf pkts/s",
			"GPRINT:outoctets:LAST:Last\: %6.0lf pkts/s\\r"
			);

			$rrd_traffic_url = $sn . "_" . $graph . ".gif";
			$rrd_traffic = dirname(__FILE__) . "/rrd/" . $sn . "_" . $graph . ".gif";
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
					print "<td colspan=\"2\">PON:" . $row{'SLOT_ID'} . "/" . $row{'PORT_ID'} ;
					print "<p onClick=\"graph_pon('". $PON_ID . "', '" . $graph . "');\"><img src=\"rrd/" . $pon_traffic_url . "\"></img></p></td></tr><tr>";
			}
			print "<td>ONU:" . $row{'PON_ONU_ID'};
			print "<p onClick=\"graph_onu('". $row{'ID'} . "', '" . $graph . "');\"><img src=\"rrd/" . $rrd_traffic_url . "\"></img></p></td>";
		}


		if ($graph == "power") {
			$rrd_power = dirname(__FILE__) . "/rrd/" . $sn . "_power.rrd";
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
			$rrd_power_url = $sn . "_power.gif";
			$rrd_power = dirname(__FILE__) . "/rrd/" . $sn . "_power.gif";
			$ret = rrd_graph($rrd_power, $opts);

			if( !is_array($ret) )
			{
				$err = rrd_error();
				echo "rrd_graph() ERROR: $err\n";
  			}
			print "<td>ONU:" . $row{'PON_ONU_ID'};
			print "<p onClick=\"graph_onu('". $row{'ID'} . "', 'power');\"><img src=\"rrd/" . $rrd_power_url . "\"></img></p></td>";
		}
		
		$end++;
		if ($end == "2") {
			$end = "0";
			print "</tr><tr>";
		}
	}
	print "</tr></table></div></div></div></div></div>";
}
?>

