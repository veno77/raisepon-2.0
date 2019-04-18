<?php
include ("common.php");
include ("classes/ip_pool_class.php");

if ($user_class < "9")
	exit();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$ip_pool_obj = new ip_pool();
	$ip_pool_obj->get_data_ip_pool();					
	?>
	<form id="contact_form" action="ip_pool.php" method="post">
	<?php if (null !== $ip_pool_obj->getId()) {
		print "<input type=\"hidden\" name=\"id\" value=\"". $ip_pool_obj->getId() ."\">";
	}
	?>
	<div class="row">
		<div class="col-md-4 col-md-offset-4">
			<div class="form-group">
				<label for="subnet">Subnet</label>
				<input type="text" name="subnet" class="form-control" placeholder="Subnet" aria-describedby="sizing-addon1" id="subnet" <?php if(null !== $ip_pool_obj->getSubnet()) print "value=\"" . $ip_pool_obj->getSubnet() . "\""; ?> >
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-4 col-md-offset-4">
			<div class="form-group">
				<label for="netmask">Netmask</label>
				<input type="text" name="netmask" class="form-control" placeholder="Netmask" aria-describedby="sizing-addon1" id="netmask" <?php if(null !== $ip_pool_obj->getNetmask()) print "value=\"" . $ip_pool_obj->getNetmask() . "\""; ?> >
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-4 col-md-offset-4">
			<div class="form-group">
				<label for="start_ip">Start_IP</label>
				<input type="text" name="start_ip" class="form-control" placeholder="Start_IP" aria-describedby="sizing-addon1" id="start_ip" <?php if(null !== $ip_pool_obj->getStart_ip()) print "value=\"" . $ip_pool_obj->getStart_ip() . "\""; ?> >
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-4 col-md-offset-4">
			<div class="form-group">
				<label for="end_ip">End_IP</label>
				<input type="text" name="end_ip" class="form-control" placeholder="End_IP" aria-describedby="sizing-addon1" id="end_ip" <?php if(null !== $ip_pool_obj->getEnd_ip()) print "value=\"" . $ip_pool_obj->getEnd_ip() . "\""; ?> >
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-4 col-md-offset-4">
			<div class="form-group">
				<label for="gateway">Gateway</label>
				<input type="text" name="gateway" class="form-control" placeholder="Gateway" aria-describedby="sizing-addon1" id="gateway" <?php if(null !== $ip_pool_obj->getGateway()) print "value=\"" . $ip_pool_obj->getGateway() . "\""; ?> >
			</div>
		</div>
	</div>
		<div class="row">
		<div class="col-md-4 col-md-offset-4">
			<div class="form-group">
				<label for="vlan">VLAN</label>
				<input type="text" name="vlan" class="form-control" placeholder="VLAN" aria-describedby="sizing-addon1" id="vlan" <?php if(null !== $ip_pool_obj->getVlan()) print "value=\"" . $ip_pool_obj->getVlan() . "\""; ?> >
			</div>
		</div>
	</div>
	<div class=row>
		<div class="form-group">
			<div class="col-md-4 col-md-offset-4">
				<?php
				$edit = (isset($_GET['edit']) ? $_GET['edit'] : null);
				if ($edit == "1" || null !== $ip_pool_obj->getId()) {
				?>
				<button type="submit" name="SUBMIT" class="btn btn-basic" value="EDIT">EDIT</button> 
				<button type="submit" name="SUBMIT" class="btn btn-basic" value="DELETE">DELETE</button>
				<?php }else{ ?>
				<button type="submit" name="SUBMIT" class="btn btn-basic" value="ADD">ADD</button>
				<?php } ?>
			</div>
		</div>
	</div>

		
<?php
}
?>