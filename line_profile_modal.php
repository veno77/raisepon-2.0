<?php
include ("common.php");
include ("classes/line_profile_class.php");

if ($user_class < "9")
	exit();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$line_profile_obj = new line_profile();
	$line_profile_obj->get_data_line_profile();					
	?>
	<form id="contact_form" action="line_profile.php" method="post">
	<?php if (null !== $line_profile_obj->getId()) {
		print "<input type=\"hidden\" name=\"id\" value=\"". $line_profile_obj->getId() ."\">";
	}
	?>
	<div class="row">
		<div class="col-md-4 col-md-offset-4">
			<div class="form-group">
				<label for="name">Name</label>
				<input type="text" name="name" class="form-control" placeholder="Name" aria-describedby="sizing-addon1" id="name" <?php if(null !== $line_profile_obj->getName()) print "value=\"" . $line_profile_obj->getName() . "\""; ?> >
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-4 col-md-offset-4">
			<div class="form-group">
				<label for="line_profile_id">line_profile_id</label>
				<input type="text" name="line_profile_id" class="form-control" placeholder="line_profile_id" aria-describedby="sizing-addon1" id="line_profile_id" <?php if(null !== $line_profile_obj->getLine_profile_id()) print "value=\"" . $line_profile_obj->getLine_profile_id() . "\""; ?> >
			</div>
		</div>
	</div>

	<div class=row>
		<div class="form-group">
			<div class="col-md-4 col-md-offset-4">
				<?php
				$edit = (isset($_GET['edit']) ? $_GET['edit'] : null);
				if ($edit == "1" || null !== $line_profile_obj->getId()) {
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