<?php

$name = "Osvobojdenie Trakiya";
$big_onu_id = "276889602";

                $sn_oid = "iso.3.6.1.4.1.8886.18.3.1.3.1.1.2." . $big_onu_id;
                $line_profile_id = "iso.3.6.1.4.1.8886.18.3.1.3.1.1.6." . $big_onu_id;
                $svr_profile_id = "iso.3.6.1.4.1.8886.18.3.1.3.1.1.8." . $big_onu_id;
                $row_status = "iso.3.6.1.4.1.8886.18.3.1.3.1.1.19." . $big_onu_id;
                $description = "iso.3.6.1.4.1.8886.18.3.1.3.1.1.20." . $big_onu_id;
                //EXECUTE SNMPSET TO ADD ONU
$session = new SNMP(SNMP::VERSION_2C, '192.168.102.97', 'private');

                $session->set(array($sn_oid, $line_profile_id, $svr_profile_id, $row_status), array('s', 'i', 'i', 'i'), array('52434D47185800EF', '1', '2', '4'));

$description = "iso.3.6.1.4.1.8886.18.3.1.3.1.1.20." . $big_onu_id;
$session->set($description, 's', $name);

