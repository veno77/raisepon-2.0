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
			$result = $db->query("SELECT CUSTOMERS.ID, CUSTOMERS.NAME as NAME, SN, PON_ONU_ID, CUSTOMERS.SERVICE, CUSTOMERS.PON_PORT, CUSTOMERS.OLT, OLT.ID, INET_NTOA(OLT.IP_ADDRESS) as IP_ADDRESS, OLT.NAME as OLT_NAME, OLT.RO as RO, OLT.RW as RW, OLT_MODEL.TYPE, PON.ID as PON_ID, PON.PORT_ID as PORT_ID, PON.SLOT_ID as SLOT_ID, SERVICES.ID, SERVICES.LINE_PROFILE_ID, SERVICES.SERVICE_PROFILE_ID, SERVICE_PROFILE.PORTS, SERVICE_PROFILE.HGU, SERVICE_PROFILE.RF as RF from CUSTOMERS LEFT JOIN OLT on CUSTOMERS.OLT=OLT.ID LEFT JOIN OLT_MODEL on OLT.MODEL=OLT_MODEL.ID LEFT JOIN PON on CUSTOMERS.PON_PORT=PON.ID LEFT JOIN SERVICES on CUSTOMERS.SERVICE=SERVICES.ID LEFT JOIN SERVICE_PROFILE on SERVICES.SERVICE_PROFILE_ID=SERVICE_PROFILE.ID where CUSTOMERS.ID = '$id'");
		} catch (PDOException $e) {
			echo "Connection Failed:" . $e->getMessage() . "\n";
			exit;
		}

		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$customer_name = $row{'NAME'};
			$olt_name = $row{'OLT_NAME'};
			$rf = $row{'RF'};
			if ($row{'TYPE'} == "1") {
				$big_onu_id = $row{'SLOT_ID'} * 10000000 + $row{'PORT_ID'} * 100000 + $row{'PON_ONU_ID'};
				$big_onu_id_2 = $row{'SLOT_ID'} * 10000000 + $row{'PORT_ID'} * 100000 + $row{'PON_ONU_ID'};
			}
			if ($row{'TYPE'} == "2") {
				$big_onu_id = type2id($row{'SLOT_ID'}, $row{'PORT_ID'}, $row{'PON_ONU_ID'});
				$big_onu_id_2 = $row{'SLOT_ID'} * 10000000 + $row{'PORT_ID'} * 100000 + $row{'PON_ONU_ID'};
			}
			$olt_ip_address = $row["IP_ADDRESS"];
			$sn = $row["SN"];
			
			$rrd_power = dirname(__FILE__) . "/rrd/" . $sn . "_power.rrd";
			if ($rf == "Yes") {
				$opts = array( "--start", "-1d", "--vertical-label=dBm", "--title=Daily Power",
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
				$opts2 = array( "--start", "-1w", "--vertical-label=dBm", "--title=Weekly Power",
					"DEF:inoctets=$rrd_power:input:AVERAGE",
                    "DEF:outoctets=$rrd_power:output:AVERAGE",
                    "DEF:rx_olt=$rrd_power:rxolt:AVERAGE",
                    "DEF:rf_in=$rrd_power:rfin:AVERAGE",
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
					"DEF:inoctets=$rrd_power:input:AVERAGE",
					"DEF:outoctets=$rrd_power:output:AVERAGE",
					"DEF:rx_olt=$rrd_power:rxolt:AVERAGE",
					"DEF:rf_in=$rrd_power:rfin:AVERAGE",
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
				$opts2 = array( "--start", "-1w", "--vertical-label=dBm", "--title=Weekly Power",
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
				$opts3 = array( "--start", "-1m", "--vertical-label=dBm", "--title=Monthly Power",
					 "DEF:inoctets=$rrd_power:input:AVERAGE",
					 "DEF:outoctets=$rrd_power:output:AVERAGE",
					 "DEF:rx_olt=$rrd_power:rxolt:AVERAGE",
					 "LINE2:rx_olt#D6213B:RX@OLT",
					 "GPRINT:rx_olt:MAX:Max\: %6.2lf dBm\\r",
					 "LINE2:outoctets#C6913B:TX@ONU",
					 "GPRINT:outoctets:MAX:Max\: %6.2lf dBm\\r",
					 "LINE2:inoctets#7FB37C:RX@ONU",
					 "GPRINT:inoctets:MAX:Max\: %6.2lf dBm\\r",
				       );
			}
			$rrd_power_url = $sn . "_power.gif";
			$rrd_power_url_week = $sn . "_power_week.gif";
			$rrd_power_url_month = $sn . "_power_month.gif";
			$rrd_power = dirname(__FILE__) . "/rrd/" . $sn . "_power.gif";
			$rrd_power_week = dirname(__FILE__) . "/rrd/" . $sn . "_power_week.gif";
			$rrd_power_month = dirname(__FILE__) . "/rrd/" . $sn . "_power_month.gif";

			$ret = rrd_graph($rrd_power, $opts);
			$ret = rrd_graph($rrd_power_week, $opts2);
			$ret = rrd_graph($rrd_power_month, $opts3);

			if( !is_array($ret) ) {
				$err = rrd_error();
				echo "rrd_graph() ERROR: $err\n";
			}
		}
		print "<center><h3>RRD Power Graphs for <font color=blue> $customer_name </font> @ OLT::$olt_name  ONU_SN::$sn</h3> ";
		print "<p><img src=\"rrd/" . $rrd_power_url . "\"></img></p>";
		print "<p><img src=\"rrd/" . $rrd_power_url_week . "\"></img></p>";
		print "<p><img src=\"rrd/" . $rrd_power_url_month . "\"></img></p>";
	}
}
?>


