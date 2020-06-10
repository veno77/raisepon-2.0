<?php
include ("common.php");
include ("classes/backup_class.php");

if ($user_class < "9")
	exit();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$backup_obj = new backup();
	$backup_obj->get_data_email();					
	
?>
	<form id="contact_form" action="backup.php" method="post">
	<?php if (null !== $backup_obj->getEmailid()) {
		print "<input type=\"hidden\" name=\"email_id\" value=\"". $backup_obj->getEmailid() ."\">";
	}
	?>
	<div class="row">
		<div class="col-md-4 col-md-offset-4">
			<div class="form-group">
				<label for="email">Email*</label>
				<input type="text" name="email" class="form-control" placeholder="Email" aria-describedby="sizing-addon1" id="email" <?php if(!empty($backup_obj->getEmail())) print "value=\"" . $backup_obj->getEmail() . "\""; ?> >
			</div>
		</div>
	</div>
	<div class=row>
		<div class="col-md-4 col-md-offset-4">
			<div class="form-group">
				<?php
				if (null !== $backup_obj->getEmailid()) {
				?>
				<button type="submit" name="SUBMIT" class="btn btn-basic" value="EDIT_EMAIL">EDIT</button> 
				<button type="submit" name="SUBMIT" class="btn btn-basic" value="DELETE_EMAIL">DELETE</button>
				<?php }else{ ?>
				<button type="submit" name="SUBMIT" class="btn btn-basic" value="ADD_EMAIL">ADD</button>
				<?php } ?>
			</div>
		</div>
	</div>	
<?php
}
?>