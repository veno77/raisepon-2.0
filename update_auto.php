<?php

include("classes/customers_class.php");



$auto_update_obj = new customers();

$rows = $auto_update_obj->get_Illegal_onus();

foreach ($rows as $olt => $values) {
	foreach ($values as $roww) {
		$row_auto = $auto_update_obj->check_Auto($roww['0']);
		if ($row_auto) {
			foreach($row_auto as $row) {
				if ($row["AUTO"] == "YES") {
					$auto_update_obj->setCustomer_id($row["ID"]);
					$pon_port = $auto_update_obj->get_Pon_port($olt, $roww{'1'},$roww{'2'});
					foreach ($pon_port as $pon) {
						$pon_id = $pon{'ID'};
					}
					$auto_update_obj->setPon_port($pon_id);
					$auto_update_obj->setOlt($olt);
					$auto_update_obj->get_data_customer();
					$auto_update_obj->edit_customer();
					
				}
			}
		}
	}
}

?>
