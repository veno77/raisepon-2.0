<?php



class snmp_oid {	
	private $gpon_oid = array(
		"onu_device_type_oid" => "1.3.6.1.4.1.8886.18.3.6.1.1.1.10",
		"onu_hw_revision_oid" => "1.3.6.1.4.1.8886.18.3.6.1.1.1.3",
		"onu_match_state_oid" => "1.3.6.1.4.1.8886.18.3.6.1.1.1.34",
		"onu_sysuptime_oid" => "1.3.6.1.4.1.8886.18.3.6.1.1.1.18",
		"onu_pon_temp_oid" => "1.3.6.1.4.1.8886.18.3.6.3.1.1.18",
		"onu_reboot_oid" => "1.3.6.1.4.1.8886.18.3.6.1.1.1.23",
		"onu_tx_power_oid" => "1.3.6.1.4.1.8886.18.3.6.3.1.1.17",
		"onu_rx_power_oid" => "1.3.6.1.4.1.8886.18.3.6.3.1.1.16",
		"onu_status_oid" => "1.3.6.1.4.1.8886.18.3.1.3.1.1.17",
		"onu_last_online_oid" => "1.3.6.1.4.1.8886.18.3.1.3.1.1.14",
		"onu_offline_reason_oid" => "1.3.6.1.4.1.8886.18.3.1.3.1.1.15",
		"onu_sn_oid" => "1.3.6.1.4.1.8886.18.3.1.3.1.1.2",
		"onu_rf_status_oid" => "1.3.6.1.4.1.8886.18.3.6.10.1.1.2",
		"onu_rf_rx_power_oid" => "1.3.6.1.4.1.8886.18.3.6.10.1.1.8",
		"line_profile_oid" => "1.3.6.1.4.1.8886.18.3.1.3.1.1.6",
		"line_profile_name_oid" => "1.3.6.1.4.1.8886.18.3.1.3.1.1.7",
		"service_profile_oid" => "1.3.6.1.4.1.8886.18.3.1.3.1.1.8",
		"service_profile_name_oid" => "1.3.6.1.4.1.8886.18.3.1.3.1.1.9",
		"row_status_oid" => "1.3.6.1.4.1.8886.18.3.1.3.1.1.19",
		"description_oid" => "1.3.6.1.4.1.8886.18.3.1.3.1.1.20",
		"dtype_oid" => "1.3.6.1.4.1.8886.18.2.1.3.1.1.3",
		"onu_active_state_oid" => "1.3.6.1.4.1.8886.18.3.1.3.1.1.18",
		"uni_port_link_oid" => "1.3.6.1.4.1.8886.18.3.6.5.1.1.8",
		"uni_port_admin_oid" => "1.3.6.1.4.1.8886.18.3.6.5.1.1.7",
		"uni_port_admin_set_oid" => "1.3.6.1.4.1.8886.18.3.6.5.1.1.7",
		"uni_port_autong_oid" => "1.3.6.1.4.1.8886.18.3.6.5.1.1.15",
		"uni_port_flowctrl_oid" => "1.3.6.1.4.1.8886.18.3.6.5.1.1.17",
		"uni_port_nativevlan_oid" => "1.3.6.1.4.1.8886.18.3.6.5.1.1.20",
		"uni_port_speed_duplex_oid" => "1.3.6.1.4.1.8886.18.3.6.5.1.1.6",
		"onu_recv_power_oid" => "1.3.6.1.4.1.8886.18.3.6.3.1.1.16",
		"onu_send_power_oid" => "1.3.6.1.4.1.8886.18.3.6.3.1.1.17",
		"olt_rx_power_oid" => "1.3.6.1.4.1.8886.18.3.1.3.3.1.1",
		"uni_octets_in_ethernet_oid" => "1.3.6.1.4.1.8886.18.3.6.5.2.1.2",
		"uni_octets_out_ethernet_oid" => "1.3.6.1.4.1.8886.18.3.6.5.2.1.15",
		"illegal_onu_sn_oid" => "1.3.6.1.4.1.8886.18.3.1.2.2.1.1",
		"illegal_onu_login_time_oid" => "1.3.6.1.4.1.8886.18.3.1.2.2.1.5",
		"illegal_onu_row_status_oid" => "1.3.6.1.4.1.8886.18.3.1.2.2.1.6",
		"onu_register_distance_oid" => "1.3.6.1.4.1.8886.18.3.1.3.1.1.16",
		"rcGponPONPortIndex" => "1.3.6.1.4.1.8886.18.3.1.2.1.1.1",
		"rcGponPONPortAllocIdLeft" => "1.3.6.1.4.1.8886.18.3.1.2.1.1.11",
		"rcGponPONPortOperStatus" => "1.3.6.1.4.1.8886.18.3.1.2.1.1.2",
		"rcGponPONPortRegisteredONUNumber" => "1.3.6.1.4.1.8886.18.3.1.2.1.1.3",
		"rcGponPONPortSFPOperStatus" => "1.3.6.1.4.1.8886.18.3.1.2.1.1.13",
		"rcGponPONOnuRegisterDistanceMin" => "1.3.6.1.4.1.8886.18.3.1.2.1.1.5",
		"rcGponPONOnuRegisterDistanceMax" => "1.3.6.1.4.1.8886.18.3.1.2.1.1.6",
		"rcGponOnuNetCurrentIpAddr" => "1.3.6.1.4.1.8886.18.3.6.6.1.1.13", 
		"rcGponOnuNetIpAddr" => "1.3.6.1.4.1.8886.18.3.6.6.1.1.2",
		"rcGponOnuNetIpMask" => "1.3.6.1.4.1.8886.18.3.6.6.1.1.3",
		"rcGponOnuNetDefaultGateway" => "1.3.6.1.4.1.8886.18.3.6.6.1.1.4",
		"rcGponOnuNetVlan" => "1.3.6.1.4.1.8886.18.3.6.6.1.1.7",
		"rcGponOnuEthPortTrunkAllowedVlan" => "1.3.6.1.4.1.8886.18.3.6.5.1.1.25",
		"rcGponPONPortMinOnuIndex" => "1.3.6.1.4.1.8886.18.3.1.2.1.1.21",
		"mac_address_perport_oid" => "1.3.6.1.4.1.8886.18.3.6.12.1.1.1",
		"mac_address_perport_number_oid" => "1.3.6.1.4.1.8886.18.3.6.12.1.1.2",
		"raisecomSWFileVersion_oid" => "1.3.6.1.4.1.8886.1.26.3.1.1.2",
		"raisecomSWFileCommit_oid" => "1.3.6.1.4.1.8886.1.26.3.1.1.3",
		"raisecomSWFileActivate_oid" => "1.3.6.1.4.1.8886.1.26.3.1.1.4"		
	);
	
	private $epon_oid = array(
			"onu_device_type_oid" => "1.3.6.1.4.1.8886.18.2.6.1.1.1.12",
			"onu_hw_revision_oid" => "1.3.6.1.4.1.8886.18.2.6.1.1.1.5",
			"onu_match_state_oid" => "1.3.6.1.4.1.8886.18.2.6.1.7.1.2",
			"onu_sysuptime_oid" => "1.3.6.1.4.1.8886.18.3.6.1.1.1.18",
			"onu_pon_temp_oid" => "1.3.6.1.4.1.8886.18.2.8.1.2.1.2.1",
			"onu_reboot_oid" => "1.3.6.1.4.1.8886.18.2.6.1.3.1.1",
			"onu_rf_status_oid" => "1.3.6.1.4.1.8886.18.2.6.21.3.1.2",
			"onu_rf_rx_power_oid" => "1.3.6.1.4.1.8886.18.2.6.21.2.1.2",
			"onu_tx_power_oid" => "1.3.6.1.4.1.8886.18.2.8.1.2.1.2.4",
			"onu_rx_power_oid" => "1.3.6.1.4.1.8886.18.2.8.1.2.1.2.5",
			"onu_status_oid" => "1.3.6.1.4.1.8886.18.2.1.3.1.1.8",
			"onu_sn_oid" => "1.3.6.1.4.1.8886.18.2.1.3.1.1.2",
			"row_status_oid" => "1.3.6.1.4.1.8886.18.2.1.3.1.1.9",	
			"line_profile_oid" => "1.3.6.1.4.1.8886.18.2.1.3.1.1.19",
			"line_profile_name_oid" => "1.3.6.1.4.1.8886.18.2.1.3.1.1.20",
			"service_profile_oid" => "1.3.6.1.4.1.8886.18.2.1.3.1.1.21",
			"service_profile_name_oid" => "1.3.6.1.4.1.8886.18.2.1.3.1.1.22",
			"description_oid" => "1.3.6.1.4.1.8886.18.2.1.3.1.1.15",
			"dtype_oid" => "1.3.6.1.4.1.8886.18.2.1.3.1.1.3",
			"onu_active_state_oid" => "1.3.6.1.4.1.8886.18.2.1.3.1.1.10",
			"onu_last_online_oid" => "1.3.6.1.4.1.8886.18.2.1.3.1.1.7",
			"onu_offline_reason_oid" => "1.3.6.1.4.1.8886.18.2.1.3.1.1.17",
			"uni_port_link_oid" => "1.3.6.1.4.1.8886.18.2.6.3.1.1.2",
			"uni_port_admin_oid" => "1.3.6.1.4.1.8886.18.2.6.3.1.1.3",
			"uni_port_admin_set_oid" => "1.3.6.1.4.1.8886.18.2.6.3.1.1.4",
			"uni_port_autong_oid" => "1.3.6.1.4.1.8886.18.2.6.3.1.1.5",
			"uni_port_flowctrl_oid" => "1.3.6.1.4.1.8886.18.2.6.3.1.1.10",
			"uni_port_speed_duplex_oid" => "1.3.6.1.4.1.8886.18.2.6.3.2.1.2",
			"illegal_onu_mac_address_oid" => "1.3.6.1.4.1.8886.18.2.1.5.1.1.1",
			"illegal_onu_login_time_oid" => "1.3.6.1.4.1.8886.18.2.1.5.1.1.2",
			"illegal_onu_row_status_oid" => "1.3.6.1.4.1.8886.18.2.1.5.1.1.3",
			"onu_recv_power_oid" => "1.3.6.1.4.1.8886.18.2.8.1.2.1.2.5",
			"onu_send_power_oid" => "1.3.6.1.4.1.8886.18.2.8.1.2.1.2.4",
			"olt_rx_power_oid" => "1.3.6.1.2.1.155.1.4.1.5.1.2",
			"uni_octets_in_ethernet_oid" => "1.3.6.1.4.1.8886.18.2.6.3.3.1.6",
			"uni_octets_out_ethernet_oid" => "1.3.6.1.4.1.8886.18.2.6.3.3.1.23",
			"rcEponPONPortIndex" => "1.3.6.1.4.1.8886.18.2.1.2.1.1.1",
			"rcEponPONPortAdmin" => "1.3.6.1.4.1.8886.18.2.1.2.1.1.2",
			"rcEponPONPortOperStatus" => "1.3.6.1.4.1.8886.18.2.1.2.1.1.3",
			"rcEponPONPortRegisteredONUNumber" => "1.3.6.1.4.1.8886.18.2.1.2.1.1.4",
			"rcEponPONPortDescription" => "1.3.6.1.4.1.8886.18.2.1.2.1.1.23",
			"rcEponPONPortSFPOperStatus" => "1.3.6.1.4.1.8886.18.2.1.2.1.1.19",
			"rcEponPONPortCreateONUNumber" => "1.3.6.1.4.1.8886.18.2.1.2.1.1.27",
			"rcEponPONPortMinOnuIndex" => "1.3.6.1.4.1.8886.18.2.1.2.1.1.34"
	);
	
	private $xgspon_oid = array(
		"onu_device_type_oid" => "1.3.6.1.4.1.8886.18.3.6.1.1.1.10",
		"onu_hw_revision_oid" => "1.3.6.1.4.1.8886.18.3.6.1.1.1.3",
		"onu_match_state_oid" => "1.3.6.1.4.1.8886.18.3.6.1.1.1.34",
		"onu_sysuptime_oid" => "1.3.6.1.4.1.8886.18.3.6.1.1.1.18",
		"onu_pon_temp_oid" => "1.3.6.1.4.1.8886.18.3.6.3.1.1.18",
		"onu_reboot_oid" => "1.3.6.1.4.1.8886.2.3233.3.6.1.2.1.23",
		"onu_tx_power_oid" => "1.3.6.1.4.1.8886.2.3233.3.6.2.2.1.17",
		"onu_rx_power_oid" => "1.3.6.1.4.1.8886.2.3233.3.6.2.2.1.16",
		"onu_status_oid" => "1.3.6.1.4.1.8886.2.3233.3.3.2.1.17",
		"onu_last_online_oid" => "1.3.6.1.4.1.8886.2.3233.3.3.2.1.14",
		"onu_offline_reason_oid" => "1.3.6.1.4.1.8886.2.3233.3.3.2.1.15",
		"onu_sn_oid" => "1.3.6.1.4.1.8886.2.3233.3.3.2.1.2",
		"onu_rf_status_oid" => "1.3.6.1.4.1.8886.2.3233.3.6.7.2.1.2",
		"onu_rf_rx_power_oid" => "1.3.6.1.4.1.8886.2.3233.3.6.7.2.1.8",
		"line_profile_oid" => "1.3.6.1.4.1.8886.2.3233.3.3.2.1.6",
		"line_profile_name_oid" => "1.3.6.1.4.1.8886.2.3233.3.3.2.1.7",
		"service_profile_oid" => "1.3.6.1.4.1.8886.2.3233.3.3.2.1.8",
		"service_profile_name_oid" => "1.3.6.1.4.1.8886.2.3233.3.3.2.1.9",
		"gponONUCreateMode" => "1.3.6.1.4.1.8886.2.3233.3.3.2.1.10",		
		"row_status_oid" => "1.3.6.1.4.1.8886.2.3233.3.3.2.1.19",
		"description_oid" => "1.3.6.1.4.1.8886.2.3233.3.3.2.1.20",
		"dtype_oid" => "1.3.6.1.4.1.8886.18.2.1.3.1.1.3",
		"onu_active_state_oid" => "1.3.6.1.4.1.8886.18.3.1.3.1.1.18",
		"uni_port_link_oid" => "1.3.6.1.4.1.8886.2.3233.3.6.3.2.1.8",
		"uni_port_admin_oid" => "1.3.6.1.4.1.8886.2.3233.3.6.3.2.1.7",
		"uni_port_admin_set_oid" => "1.3.6.1.4.1.8886.2.3233.3.6.3.2.1.7",
		"uni_port_autong_oid" => "1.3.6.1.4.1.8886.2.3233.3.6.3.2.1.15",
		"uni_port_flowctrl_oid" => "1.3.6.1.4.1.8886.2.3233.3.6.3.2.1.17",
		"uni_port_nativevlan_oid" => "1.3.6.1.4.1.8886.2.3233.3.6.3.2.1.20",
		"uni_port_speed_duplex_oid" => "1.3.6.1.4.1.8886.2.3233.3.6.3.2.1.6",
		"onu_recv_power_oid" => "1.3.6.1.4.1.8886.2.3233.3.6.2.2.1.16",
		"onu_send_power_oid" => "1.3.6.1.4.1.8886.2.3233.3.6.2.2.1.17",
		"olt_rx_power_oid" => "1.3.6.1.4.1.8886.2.3233.3.3.4.1.1",
		"uni_octets_in_ethernet_oid" => "1.3.6.1.4.1.8886.18.3.6.5.2.1.2",
		"uni_octets_out_ethernet_oid" => "1.3.6.1.4.1.8886.18.3.6.5.2.1.15",
		"illegal_onu_sn_oid" => "1.3.6.1.4.1.8886.2.3233.3.2.3.1.1",
		"illegal_onu_login_time_oid" => "1.3.6.1.4.1.8886.2.3233.3.2.3.1.5",
		"illegal_onu_row_status_oid" => "1.3.6.1.4.1.8886.2.3233.3.2.3.1.6",
		"onu_register_distance_oid" => "1.3.6.1.4.1.8886.2.3233.3.3.2.1.16",
		"rcGponPONPortIndex" => "1.3.6.1.4.1.8886.2.3233.3.2.2.1.1",
		"rcGponPONPortAllocIdLeft" => "1.3.6.1.4.1.8886.2.3233.3.2.2.1.22",
		"rcGponPONPortOperStatus" => "1.3.6.1.4.1.8886.2.3233.3.2.2.1.2",
		"rcGponPONPortRegisteredONUNumber" => "1.3.6.1.4.1.8886.2.3233.3.2.2.1.3",
		"rcGponPONPortSFPOperStatus" => "1.3.6.1.4.1.8886.18.3.1.2.1.1.13",
		"rcGponPONOnuRegisterDistanceMin" => "1.3.6.1.4.1.8886.2.3233.3.2.2.1.5",
		"rcGponPONOnuRegisterDistanceMax" => "1.3.6.1.4.1.8886.2.3233.3.2.2.1.6",
		"rcGponOnuNetCurrentIpAddr" => "1.3.6.1.4.1.8886.18.3.6.6.1.1.13", 
		"rcGponOnuNetIpAddr" => "1.3.6.1.4.1.8886.18.3.6.6.1.1.2",
		"rcGponOnuNetIpMask" => "1.3.6.1.4.1.8886.18.3.6.6.1.1.3",
		"rcGponOnuNetDefaultGateway" => "1.3.6.1.4.1.8886.18.3.6.6.1.1.4",
		"rcGponOnuNetVlan" => "1.3.6.1.4.1.8886.18.3.6.6.1.1.7",
		"rcGponOnuEthPortTrunkAllowedVlan" => "1.3.6.1.4.1.8886.18.3.6.5.1.1.25",
		"gponPortMinOnuIndex" => "1.3.6.1.4.1.8886.2.3233.3.2.2.1.21",
		"gponRmtOnuDevTxOctV" => "1.3.6.1.4.1.8886.2.3233.3.6.1.4.1.1",
		"gponRmtOnuDevRxOctV" => "1.3.6.1.4.1.8886.2.3233.3.6.1.4.1.2",
		"mac_address_perport_oid" => "1.3.6.1.4.1.8886.2.3233.3.6.18.1.1.1",
		"mac_address_perport_number_oid" => "1.3.6.1.4.1.8886.2.3233.3.6.18.1.1.2",
		"raisecomSWFileVersion_oid" => "1.3.6.1.4.1.8886.2.3233.1.5.3.1.2",
		"raisecomSWFileCommit_oid" => "1.3.6.1.4.1.8886.2.3233.1.5.3.1.3",
		"raisecomSWFileActivate_oid" => "1.3.6.1.4.1.8886.2.3233.1.5.3.1.4"	
	);
	
	private $olt_oid = array (
		"olt_status_oid" => "1.3.6.1.2.1.1.3.0",
		"olt_temp_oid" => '1.3.6.1.4.1.8886.1.27.2.1.1.10.0',
		"olt_cpu_oid" => '1.3.6.1.4.1.8886.18.1.7.1.1.1.4',
		"sys_uptime_oid" => '1.3.6.1.2.1.1.3.0',
		"olt_serial_number_oid" => '1.3.6.1.4.1.8886.1.27.2.1.1.3.0',
		"olt_hw_version_oid" => '1.3.6.1.4.1.8886.1.27.2.1.1.4.0',
		"olt_model_oid" => '1.3.6.1.4.1.8886.1.27.2.1.1.6.0',
		"olt_slot_num_oid" => '1.3.6.1.4.1.8886.1.27.2.1.1.11.0',
		"olt_mac_address_oid" => '1.3.6.1.4.1.8886.1.27.2.1.1.15.0',
		"olt_reboot_oid" => '1.3.6.1.4.1.8886.1.27.3.1.1.12.1',
		"ifDescr" => "1.3.6.1.2.1.2.2.1.2",
		"ifAdminStatus" => "1.3.6.1.2.1.2.2.1.7",
		"dot3StatsIndex" => "1.3.6.1.2.1.10.7.2.1.1",
		"dot3StatsDuplexStatus" => "1.3.6.1.2.1.10.7.2.1.19",
		"ifHighSpeed" => "1.3.6.1.2.1.31.1.1.1.15",
		"ifOperStatus" => "1.3.6.1.2.1.2.2.1.8",
		"ifHCInOctets" => "1.3.6.1.2.1.31.1.1.1.6",
		"ifHCOutOctets" => "1.3.6.1.2.1.31.1.1.1.10",
		"ifHCInUcastPkts" => "1.3.6.1.2.1.31.1.1.1.7",
		"ifHCOutUcastPkts" => "1.3.6.1.2.1.31.1.1.1.11",
		"ifHCInMulticastPkts" => "1.3.6.1.2.1.31.1.1.1.8",
		"ifHCOutMulticastPkts" => "1.3.6.1.2.1.31.1.1.1.12",
		"ifHCInBroadcastPkts" => "1.3.6.1.2.1.31.1.1.1.9",
		"ifHCOutBroadcastPkts" => "1.3.6.1.2.1.31.1.1.1.13",
		"dot3MpcpRoundTripTime" => "1.3.6.1.2.1.155.1.1.1.1.10",
		"raisecomOnlineUpgradeV2Protocol" => "1.3.6.1.4.1.8886.1.26.1.1.1.1.1.3",
		"raisecomOnlineUpgradeV2OprType" => "1.3.6.1.4.1.8886.1.26.1.1.1.1.1.4",
		"raisecomOnlineUpgradeV2FileType" => "1.3.6.1.4.1.8886.1.26.1.1.1.1.1.5",
		"raisecomOnlineUpgradeV2ServerAddress" => "1.3.6.1.4.1.8886.1.26.1.1.1.1.1.6",
		"raisecomOnlineUpgradeV2FileName" => "1.3.6.1.4.1.8886.1.26.1.1.1.1.1.7",
		"raisecomOnlineUpgradeV2UserName" => "1.3.6.1.4.1.8886.1.26.1.1.1.1.1.8",
		"raisecomOnlineUpgradeV2UserPassword" => "1.3.6.1.4.1.8886.1.26.1.1.1.1.1.9",
		"raisecomOnlineUpgradeV2NotificationOnCompletion" => "1.3.6.1.4.1.8886.1.26.1.1.1.1.1.10",
		"raisecomOnlineUpgradeV2FailCause" => "1.3.6.1.4.1.8886.1.26.1.1.1.1.1.14",
		"raisecomOnlineUpgradeV2EntryRowStatus" => "1.3.6.1.4.1.8886.1.26.1.1.1.1.1.15"
	);
	private $olt_xgspon_oid = array (
		"olt_status_oid" => "1.3.6.1.4.1.8886.2.1.1.21.0",
		"olt_temp_oid" => '1.3.6.1.4.1.8886.2.11.15.2.1.3.1080197121',
		"olt_cpu_oid" => '1.3.6.1.4.1.8886.2.11.3.2.1.2',
		"sys_uptime_oid" => '1.3.6.1.4.1.8886.2.1.1.21.0',
		"olt_serial_number_oid" => '1.3.6.1.4.1.8886.2.1.1.11.0',
		"olt_hw_version_oid" => '1.3.6.1.4.1.8886.2.1.1.13.0',
		"olt_model_oid" => '1.3.6.1.4.1.8886.2.1.1.1.0',
		"olt_slot_num_oid" => '1.3.6.1.4.1.8886.1.27.2.1.1.11.0',
		"olt_mac_address_oid" => '1.3.6.1.4.1.8886.2.1.1.7.0',
		"olt_reboot_oid" => '1.3.6.1.4.1.8886.2.1.1.23.0',
		"ifDescr" => "1.3.6.1.2.1.2.2.1.2",
		"ifAdminStatus" => "1.3.6.1.2.1.2.2.1.7",
		"dot3StatsIndex" => "1.3.6.1.2.1.10.7.2.1.1",
		"dot3StatsDuplexStatus" => "1.3.6.1.2.1.10.7.2.1.19",
		"ifHighSpeed" => "1.3.6.1.2.1.31.1.1.1.15",
		"ifOperStatus" => "1.3.6.1.2.1.2.2.1.8",
		"ifHCInOctets" => "1.3.6.1.2.1.31.1.1.1.6",
		"ifHCOutOctets" => "1.3.6.1.2.1.31.1.1.1.10",
		"ifHCInUcastPkts" => "1.3.6.1.2.1.31.1.1.1.7",
		"ifHCOutUcastPkts" => "1.3.6.1.2.1.31.1.1.1.11",
		"ifHCInMulticastPkts" => "1.3.6.1.2.1.31.1.1.1.8",
		"ifHCOutMulticastPkts" => "1.3.6.1.2.1.31.1.1.1.12",
		"ifHCInBroadcastPkts" => "1.3.6.1.2.1.31.1.1.1.9",
		"ifHCOutBroadcastPkts" => "1.3.6.1.2.1.31.1.1.1.13",
		"dot3MpcpRoundTripTime" => "1.3.6.1.2.1.155.1.1.1.1.10",
		"raisecomOnlineUpgradeV2Protocol" => "1.3.6.1.4.1.8886.1.26.1.1.1.1.1.3",
		"raisecomOnlineUpgradeV2OprType" => "1.3.6.1.4.1.8886.1.26.1.1.1.1.1.4",
		"raisecomOnlineUpgradeV2FileType" => "1.3.6.1.4.1.8886.1.26.1.1.1.1.1.5",
		"raisecomOnlineUpgradeV2ServerAddress" => "1.3.6.1.4.1.8886.1.26.1.1.1.1.1.6",
		"raisecomOnlineUpgradeV2FileName" => "1.3.6.1.4.1.8886.1.26.1.1.1.1.1.7",
		"raisecomOnlineUpgradeV2UserName" => "1.3.6.1.4.1.8886.1.26.1.1.1.1.1.8",
		"raisecomOnlineUpgradeV2UserPassword" => "1.3.6.1.4.1.8886.1.26.1.1.1.1.1.9",
		"raisecomOnlineUpgradeV2NotificationOnCompletion" => "1.3.6.1.4.1.8886.1.26.1.1.1.1.1.10",
		"raisecomOnlineUpgradeV2FailCause" => "1.3.6.1.4.1.8886.1.26.1.1.1.1.1.14",
		"raisecomOnlineUpgradeV2EntryRowStatus" => "1.3.6.1.4.1.8886.1.26.1.1.1.1.1.15"
	);	
	 
	
	function get_pon_oid($key, $type) {
		if ($type == "GPON") {
			$oid = $this->gpon_oid[$key];
			return $oid;
		}
		if ($type == "EPON") {
			$oid = $this->epon_oid[$key];
			return $oid;
		}
		if ($type == "OLT") {
			$oid = $this->olt_oid[$key];
			return $oid;
		}
		if ($type == "XGSPON") {
			$oid = $this->xgspon_oid[$key];
			return $oid;
		}
		if ($type == "XGSPON_OLT") {
			$oid = $this->olt_xgspon_oid[$key];
			return $oid;
		}
	}
		
	function type2ponid ($slot, $pon_port) {
        $slot = decbin($slot);
        $pon_port = str_pad(decbin($pon_port), 6, "0", STR_PAD_LEFT);
        $pon_id = bindec($slot . $pon_port);
        return $pon_id;
	}
	function type3ponid ($slot, $pon_port) {
		$interface_type_id = "1010";
		$system_id = "00";
        $slot = str_pad(decbin($slot), 5, "0", STR_PAD_LEFT);
        $pon_port = str_pad(decbin($pon_port), 6, "0", STR_PAD_LEFT);
        $tail = "000000000000000";
        $pon_id =bindec($interface_type_id . $system_id . $slot . $pon_port . $tail) + 1;       
        return $pon_id;
	}	
	function type2id($slot, $pon_port, $onu_id) {
        $vif = "0001";
        $slot = str_pad(decbin($slot),5, "0", STR_PAD_LEFT);
        $pon_port = str_pad(decbin($pon_port), 6, "0", STR_PAD_LEFT);
        $onu_id = str_pad(decbin($onu_id), 16, "0", STR_PAD_LEFT);
        $big_onu_id = bindec($vif . $slot . "0" . $pon_port . $onu_id);
        return $big_onu_id;
	}
	function RmtOnuIntId($slot_id, $port_id, $pon_onu_id, $onu_port_id) {
		$interface_pon = "010";
		$slot = str_pad(decbin($slot_id),5, "0", STR_PAD_LEFT);
		$pon_port = str_pad(decbin($port_id), 6, "0", STR_PAD_LEFT);
		$onu_id = str_pad(decbin($pon_onu_id), 10, "0", STR_PAD_LEFT);
		$onu_port = str_pad(decbin($onu_port_id), 8, "0", STR_PAD_LEFT);
		$RmtOnuIntId = bindec($interface_pon . $slot . $pon_port . $onu_id . $onu_port);
        return $RmtOnuIntId;
	}
	function trunk_vlans($hex_string){
		$output = str_replace('Hex-STRING: ', '', $hex_string);
		$output = trim(preg_replace('/\s\s+/', ' ', $output));
		$output = explode(' ', $output);
		foreach ($output as $key => $value ){
			if ($value != '00') {
				$value = strrev(base_convert($value, 16, 2));
				$value = str_split($value);
				$vlans = array();
				foreach ($value as $bit_key => $bitnumber){
					if ($bitnumber != '0'){
						$vlan = $key*8 + 8 - $bit_key;
						$vlans[] = $vlan;
					}
				}
				$vlans = array_reverse($vlans);
				$all_vlans[] = $vlans;
			}
		}
		if (!empty($all_vlans)){
			$vlans_merged = array();
			foreach ($all_vlans as $vlans){
				$vlans_merged = array_merge($vlans_merged, $vlans);
			}
			return $vlans_merged;	
		}
	}
	
}



?>

