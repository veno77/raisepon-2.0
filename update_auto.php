<?php
include("classes/customers_class.php");

//Disable ONUs with Serial Numbers in the NOT_PAID table
$izkl = new customers();
$izkl->not_paid();

//Process AUTO add customers
$auto_update_obj = new customers();
$rows = $auto_update_obj->get_Illegal_onus();
foreach ($rows as $olt => $values) {
	foreach ($values as $id => $roww) {
		$obj = new customers();
		$row_auto = $obj->check_Auto($roww['0']);
	//	print_r($row_auto);
		if ($row_auto) {
			foreach($row_auto as $row) {
				if ($row["AUTO"] == "YES") {
					$obj->setCustomer_id($row["ID"]);
					$pon_port = $obj->get_Pon_port($olt, $roww['1'],$roww['2']);
					if($pon_port) {	
						foreach ($pon_port as $pon) {						
							$pon_id = $pon['ID'];
							$obj->setPon_port($pon_id);
						}
					}
					$obj->setOlt($olt);
					$obj->get_data_customer();
					$error = $obj->edit_customer();
					//if (null !== $obj->getState_rf()) {
					//	$obj->update_rf_snmp();
					//}
				}
			}
		}
	}
}


?>
