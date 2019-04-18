$(function() {
        $("#select-olt").change(function() {
                $("#select-pon").load("get.php?choice=" + $("#select-olt").val());
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

function getPage(customer_id, type) {
	$('#output').html('<center><img src="pic/loading.gif" /></center>');
	jQuery.ajax({
		url: "onu_info.php",
		data: {customer_id: customer_id, type: type},
		type: "POST"
	}).done(function(data) {
		$('#output').html(data);
		$('.dropdown-toggle').dropdown();
	});
	
}

function getOltPage(olt_id, type) {
	$('#output').html('<center><img src="pic/loading.gif" /></center>');
	jQuery.ajax({
		url: "olt_info.php",
		data: {olt_id: olt_id, type: type},
		type: "POST"
	}).done(function(data) {
		$('#output').html(data);
		$('.dropdown-toggle').dropdown();
	});
	
}

function getPageRF(customer_id, type) {
	var selected = $('#rf_menu option:selected');
	$('#output').html('<center><img src="pic/loading.gif" /></center>');
	jQuery.ajax({
		url: "onu_info.php",
		data: {customer_id: customer_id, type: type, rf_val: selected.val()},
		type: "POST"
	}).done(function(data) {
		$('#output').html(data);
		$('.dropdown-toggle').dropdown();
	});
}

function setUniPortStatus(customer_id, port_num, type) {
	var boza = port_num
	var selected = $('#uni_num_' + port_num + ' option:selected');
	$('#output').html('<center><img src="pic/loading.gif" /></center>');
	jQuery.ajax({
		url: "onu_info.php",
		data: {customer_id: customer_id, port_num: port_num, type: type, uni_val: selected.val()},
		type: "POST"
	}).done(function(data) {
		$('#output').html(data);
		$('.dropdown-toggle').dropdown();
	});
}

function getService(service_id) {
	jQuery.ajax({
		url: "service_modal.php",
		data: {service_id: service_id},
		type: "POST"
	}).done(function(data){
		$('#modalbody').html(data);
		$('#myModal').modal('show'); 
		$('#xpon').dropdown();
		$('#profiles').dropdown();
		$('#tools').dropdown();
	});
}
function getAccount(account_id) {
	jQuery.ajax({
		url: "accounts_modal.php",
		data: {account_id: account_id},
		type: "POST"
	}).done(function(data){
		$('#modalbody').html(data);
		$('#myModal').modal('show'); 
		$('#xpon').dropdown();
		$('#profiles').dropdown();
		$('#tools').dropdown();
	});
}
function getService_Profile(id) {
	jQuery.ajax({
		url: "service_profile_modal.php",
		data: {id: id},
		type: "POST"
	}).done(function(data){
		$('#modalbody').html(data);
		$('#myModal').modal('show'); 
		$('#xpon').dropdown();
		$('#profiles').dropdown();
		$('#tools').dropdown();
	});
}
function get_ip_pool(id) {
	jQuery.ajax({
		url: "ip_pool_modal.php",
		data: {id: id},
		type: "POST"
	}).done(function(data){
		$('#modalbody').html(data);
		$('#myModal').modal('show'); 
		$('#xpon').dropdown();
		$('#profiles').dropdown();
		$('#tools').dropdown();
	});
}
function olt_ip_pool(binding_id) {
	jQuery.ajax({
		url: "olt_ip_pool_modal.php",
		data: {binding_id: binding_id},
		type: "POST"
	}).done(function(data){
		$('#modalbody_binding').html(data);
		$('#Modal_Binding').modal('show'); 
		$('#xpon').dropdown();
		$('#profiles').dropdown();
		$('#tools').dropdown();
	});
}
function getLine_profile(id) {
	jQuery.ajax({
		url: "line_profile_modal.php",
		data: {id: id},
		type: "POST"
	}).done(function(data){
		$('#modalbody').html(data);
		$('#myModal').modal('show'); 
		$('#xpon').dropdown();
		$('#profiles').dropdown();
		$('#tools').dropdown();
	});
}

function getOlt(olt_id) {
	jQuery.ajax({
		url: "olt_modal.php",
		data: {olt_id: olt_id},
		type: "POST"
	}).done(function(data){
		$('#modalbody').html(data);
		$('#myModal').modal('show'); 
		$('#xpon').dropdown();
		$('#profiles').dropdown();
		$('#tools').dropdown();
	});
}

function getPon(pon_id, olt) {
	jQuery.ajax({
		url: "pon_modal.php",
		data: {pon_id: pon_id, olt: olt},
		type: "POST"
	}).done(function(data){
		$('#modalbody').html(data);
		$('#myModal').modal('show'); 
		$('#xpon').dropdown();
		$('#profiles').dropdown();
		$('#tools').dropdown();
	});
}

function addPon(olt) {
	jQuery.ajax({
		url: "pon_modal.php",
		data: {olt: olt},
		type: "POST"
	}).done(function(data){
		$('#modalbody').html(data);
		$('#myModal').modal('show'); 
		$('#xpon').dropdown();
		$('#profiles').dropdown();
		$('#tools').dropdown();
	});
}
function getCustomer(customers_id) {
	jQuery.ajax({
		url: "customers_modal.php",
		data: {customers_id: customers_id},
		type: "POST"
	}).done(function(data){
		$('#modalbody').html(data);
		$('#myModal').modal('show'); 
		$('#xpon').dropdown();
		$('#profiles').dropdown();
		$('#tools').dropdown();
	});
}
function editCustomer(olt, pon_port, customers_id) {
	jQuery.ajax({
		url: "customers_modal.php",
		data: {olt: olt, pon_port: pon_port, customers_id: customers_id},
		type: "POST"
	}).done(function(data){
		$('#modalbody').html(data);
		$('#myModal').modal('show'); 
		$('#xpon').dropdown();
		$('#profiles').dropdown();
		$('#tools').dropdown();
	});
}

function addCustomer(olt, pon_port, sn) {
	jQuery.ajax({
		url: "customers_modal.php",
		data: {old_olt: olt, old_pon_port: pon_port, sn: sn},
		type: "POST"
	}).done(function(data){
		$('#modalbody').html(data);
		$('#myModal').modal('show'); 
		$('#xpon').dropdown();
		$('#profiles').dropdown();
		$('#tools').dropdown();
	});
}

function graph(id, type) {
	$('#output').html('<img src="pic/loading.gif" />');
	jQuery.ajax({
		url: "graph.php",
		data: {id: id, type: type},
		type: "GET",
		success:function(data){$('#output').html(data);}
	});
}
function graph_onu(id, type) {
	$('#output').html('<img src="pic/loading.gif" />');
	jQuery.ajax({
		url: "graph.php",
		data: {id: id, type: type},
		type: "GET",
		success:function(data){$('#output').html(data);}
	});
}
function graph_olt(ip_address, index, ifDescr) {
	$('#output').html('<img src="pic/loading.gif" />');
	jQuery.ajax({
		url: "graph_olt.php",
		data: {ip_address: ip_address, index: index, ifDescr: ifDescr},
		type: "GET",
		success:function(data){$('#output').html(data);}
	});
}

function graph_pon(pon_id, type) {
	$('#output').html('<img src="pic/loading.gif" />');
	jQuery.ajax({
		url: "graph_pon.php",
		data: {id: pon_id, type: type},
		type: "GET",
		success:function(data){$('#output').html(data);}
	});
}

function LoadGraphs() {
	$('#output').html('<img src="pic/loading.gif" />');
	jQuery.ajax({
		url: "graphs.php",
		data: $( "#graphs" ).serialize(),
		type: "POST",
		success:function(data){$('#output').html(data);}
	});
}
function LoadIndex() {
	$('#output').html('<img src="pic/loading.gif" />');
	jQuery.ajax({
		url: "index.php",
		data: $( "#load" ).serialize(),
		type: "POST",
		success:function(data){$('#output').html(data);}
	}).done(function(data) {
		$('#output').html(data);
		$('.dropdown-toggle').dropdown();
	});
}
function SearchIndex() {
	$('#output').html('<img src="pic/loading.gif" />');
	jQuery.ajax({
		url: "index.php",
		data: $( "#search" ).serialize(),
		type: "POST",
		success:function(data){$('#output').html(data);}
	}).done(function(data) {
		$('#output').html(data);
		$('.dropdown-toggle').dropdown();
	});
}
function UnassignedIndex() {
	$('#output').html('<img src="pic/loading.gif" />');
	jQuery.ajax({
		url: "index.php",
		data: $( "#unassigned" ).serialize(),
		type: "POST",
		success:function(data){$('#output').html(data);}
	}).done(function(data) {
		$('#output').html(data);
		$('.dropdown-toggle').dropdown();
	});
}
function ShowSamePon(olt_id, pon_id, onu_id) {
	$('#output').html('<img src="pic/loading.gif" />');
	jQuery.ajax({
		url: "index.php",
		data: {olt_id: olt_id, pon_id: pon_id, onu_id: onu_id, SUBMIT: "LOAD"},
		type: "POST",
		success:function(data){$('#output').html(data);}
	}).done(function(data) {
		$('#output').html(data);
		$('.dropdown-toggle').dropdown();
	});
}
$(document).ready(function(){
	$(document).on('click', '#navbar2 .nav li a', function () {
		 $('.active').removeClass('active');
		 $(this).parent().addClass('active');
	});
});

