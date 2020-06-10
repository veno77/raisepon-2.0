<?php
include ("common.php");
include ("classes/backup_class.php");

if ($user_class < "9")
	exit();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$backup_obj = new backup();
	$backup_obj->get_data();					
	
?>
	<form id="contact_form" action="backup.php" method="post">
	<?php if (null !== $backup_obj->getId()) {
		print "<input type=\"hidden\" name=\"id\" value=\"". $backup_obj->getId() ."\">";
	}
	?>
	<div class="row">
		<div class="col-md-4 col-md-offset-4">
			<div class="form-group">
				<label for="name">Name*</label>
				<input type="text" name="name" class="form-control" placeholder="Name" aria-describedby="sizing-addon1" id="name" <?php if(!empty($backup_obj->getName())) print "value=\"" . $backup_obj->getName() . "\""; ?> >
			</div>
		</div>
	</div>
	<div class=row>
		<div class="col-md-4 col-md-offset-4">
			<div class="form-group">
				<label for="ip_address">IP Address*</label>
				<input type="text" name="ip_address" class="form-control" placeholder="IP Address" aria-describedby="sizing-addon1" id="ip_address" <?php if(!empty($backup_obj->getIp_address())) print "value=\"" . $backup_obj->getIp_address() . "\""; ?> >
			</div>
		</div>
	</div>	
	<div class=row>
		<div class="col-md-4 col-md-offset-4">
			<div class="form-group">
				<label for="username">Username*</label>
				<input type="text" name="username" class="form-control" placeholder="Username" aria-describedby="sizing-addon1" id="username" <?php if(!empty($backup_obj->getUsername())) print "value=\"" . $backup_obj->getUsername() . "\""; ?> >
			</div>
		</div>
	</div>	
	<div class=row>
		<div class="col-md-4 col-md-offset-4">
			<div class="form-group">		
				<label for="password">Password*</label>
				<input type="text" name="password" class="form-control" placeholder="Password" aria-describedby="sizing-addon1" id="username" <?php if(!empty($backup_obj->getPassword())) print "value=\"" . $backup_obj->getPassword() . "\""; ?> >
			</div>
		</div>
	</div>	
	<div class=row>
		<div class="col-md-4 col-md-offset-4">
			<div class="form-group">		
				<label for="directory">Directory*</label>
				<input type="text" name="directory" class="form-control" placeholder="Directory" aria-describedby="sizing-addon1" id="directory" <?php if(!empty($backup_obj->getDirectory())) print "value=\"" . $backup_obj->getDirectory() . "\""; ?> >
			</div>
		</div>
	</div>	
	<div class=row>
		<div class="col-md-4 col-md-offset-4">
			<div class="form-group">
				<?php
				if (null !== $backup_obj->getId()) {
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