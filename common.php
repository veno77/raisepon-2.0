<?php
session_start();
if (!isset($_SESSION["id"]) && false == strpos($_SERVER['REQUEST_URI'], 'login.php')) {
header("Location: login.php");
}
//header('Content-Type: text/html; charset=utf-8');
print "<link rel=\"stylesheet\" href=\"./css/bootstrap.min.css\">";
print "<script src=\"./js/jquery-3.1.1.min.js\"></script>";
print "<script src=\"./js/bootstrap.min.js\"></script>";
 
?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>
<script type="text/javascript" language="javascript">
$(function() {
        $("#select-olt").change(function() {
                $("#select-pon").load("get.php?choice=" + $("#select-olt").val());
                $("#line-profile").load("get_line_profile.php?choice=" + $("#select-olt").val());
        });
});

$(function() {
        $("#select-olt-2").change(function() {
                $("#select-pon-2").load("get.php?choice=" + $("#select-olt-2").val());
        });
});



$(function() {
        $("#select-onu").change(function() {
                $("#service-profile").load("get_service_profile.php?choice=" + $("#select-onu").val() + "&olt=" + $("#select-olt").val());
        });
});

$(function() {
$("#selectall").click(function () {
var checkAll = $("#selectall").prop('checked');
    if (checkAll) {
        $(".case").prop("checked", true);
    } else {
        $(".case").prop("checked", false);
    }
});
});



var getPage;

function getPage(customer_id, type) {
	$('#output').html('<center><img src="pic/loading.gif" /></center>');
	jQuery.ajax({
		url: "onu_info.php",
		data: {customer_id: customer_id, type: type},
		type: "POST",
		success:function(data){$('#output').html(data);}
	});
	$('#xpon').dropdown();
	$('#tools').dropdown();
}

function getOnu(onu_id) {
	jQuery.ajax({
		url: "onu_modal.php",
		data: {onu_id: onu_id},
		type: "POST",
		success:function(data){$('#modalbody').html(data);}
	});
	$('#myModal').modal('show'); 
}

function getService(service_id) {
	jQuery.ajax({
		url: "service_modal.php",
		data: {service_id: service_id},
		type: "POST",
		success:function(data){$('#modalbody').html(data);}
	});
	$('#myModal').modal('show'); 
	$('#xpon').dropdown();
	$('#tools').dropdown();
}

function getService_Profile(id) {
	jQuery.ajax({
		url: "service_profile_modal.php",
		data: {id: id},
		type: "POST",
		success:function(data){$('#modalbody').html(data);}
	});
	$('#myModal').modal('show'); 
	$('#xpon').dropdown();
	$('#tools').dropdown();
}

function getLine_profile(id) {
	jQuery.ajax({
		url: "line_profile_modal.php",
		data: {id: id},
		type: "POST",
		success:function(data){$('#modalbody').html(data);}
	});
	$('#myModal').modal('show');
	$('#xpon').dropdown();
	$('#tools').dropdown();	
}

function getOlt(olt_id) {
	jQuery.ajax({
		url: "olt_modal.php",
		data: {olt_id: olt_id},
		type: "POST",
		success:function(data){$('#modalbody').html(data);}
	});
	$('#myModal').modal('show'); 
	$('#xpon').dropdown();
	$('#tools').dropdown();
}

function getPon(pon_id, olt) {
	jQuery.ajax({
		url: "pon_modal.php",
		data: {pon_id: pon_id, olt: olt},
		type: "POST",
		success:function(data){$('#modalbody').html(data);}
	});
	$('#myModal').modal('show'); 
	$('#xpon').dropdown();
	$('#tools').dropdown();
}

function addPon(olt) {
	jQuery.ajax({
		url: "pon_modal.php",
		data: {olt: olt},
		type: "POST",
		success:function(data){$('#modalbody').html(data);}
	});
	$('#myModal').modal('show'); 
	$('#xpon').dropdown();
	$('#tools').dropdown();

}
function getCustomer(customers_id) {
	jQuery.ajax({
		url: "customers_modal.php",
		data: {customers_id: customers_id},
		type: "POST",
		success:function(data){$('#modalbody').html(data);}
	});
	$('#myModal').modal('show'); 
	$('#xpon').dropdown();
	$('#tools').dropdown();
}

function addCustomer(olt, pon_port, sn) {
	jQuery.ajax({
		url: "customers_modal.php",
		data: {old_olt: olt, old_pon_port: pon_port, sn: sn},
		type: "POST",
		success:function(data){$('#modalbody').html(data);}
	});
	$('#myModal').modal('show'); 
	$('#xpon').dropdown();
	$('#tools').dropdown();
}

function get_graph_power(id) {
        $('#output').html('<img src="pic/loading.gif" />');
        jQuery.ajax({
                url: "graph_power.php",
                data: {id: id},
                type: "GET",
                success:function(data){$('#output').html(data);}
        });
	
}

function get_graph_traffic(id) {
        $('#output').html('<img src="pic/loading.gif" />');
        jQuery.ajax({
                url: "graph_traffic.php",
                data: {id: id},
                type: "GET",
                success:function(data){$('#output').html(data);}
        });
}


function get_graph_packets(customer_id, type) {
        $('#output').html('<img src="pic/loading.gif" />');
        jQuery.ajax({
                url: "graph_packets.php",
                data: {id: customer_id, type: type},
                type: "GET",
                success:function(data){$('#output').html(data);}
        });
}

function graph_onu_ethernet_ports(id, port) {
        $('#output').html('<img src="pic/loading.gif" />');
        jQuery.ajax({
                url: "graph_onu_ethernet_ports.php",
                data: {id: id, port: port},
                type: "GET",
                success:function(data){$('#output').html(data);}
        });
}








</script>

<?php

$user_class = isset($_SESSION["type"]);
$cur_user_id = isset($_SESSION["id"]);

$pon_dropdown = array();

ini_set('display_errors','off');



function test_input($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}

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

function echoActiveClassIfRequestMatches($requestUri)
{
    $current_file_name = basename($_SERVER['REQUEST_URI'], ".php");

    if ($current_file_name == $requestUri)
        echo 'class="active"';
}

?>
