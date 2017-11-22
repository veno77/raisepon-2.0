<?php

function type2id($slot, $pon_port, $onu_id) { 
	$vif = "0001";
	$slot = str_pad(decbin($slot),5, "0", STR_PAD_LEFT);
	$pon_port = str_pad(decbin($pon_port), 6, "0", STR_PAD_LEFT);
	$onu_id = str_pad(decbin($onu_id), 16, "0", STR_PAD_LEFT);
	$big_onu_id = bindec($vif . $slot . "0" . $pon_port . $onu_id);
	return $big_onu_id;
}

function type2ponid ($slot, $pon_port) {
	$slot = decbin($slot);
	$pon_port = str_pad(decbin($pon_port), 6, "0", STR_PAD_LEFT);	
	$pon_id = bindec($slot . $pon_port);
	return $pon_id;
}

$big_onu_id = type2ponid("20","4");
print $big_onu_id . "\n";
?>
