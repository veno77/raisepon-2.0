<?php
include ("common.php");
include ("classes/accounts_class.php");

if ($user_class < "9")
	exit();
/*** set a form token ***/
$form_token = md5( uniqid('auth', true) );

/*** set the session form token ***/
$_SESSION['form_token'] = $form_token;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$accounts_obj = new accounts();
	$accounts_obj->get_data();					
	?>
	<form id="contact_form" action="accounts.php" method="post">
	<input type="hidden" name="form_token" value="<?php echo $form_token; ?>" />
	<?php 
	if (null !== $accounts_obj->getAccount_id()) {
		print "<input type=\"hidden\" name=\"account_id\" value=\"". $accounts_obj->getAccount_id() ."\">";		
	}
	?>
	<input type="hidden" name="form_token" value="<?php echo $form_token; ?>" />
	<div class="row">
		<div class="col-md-4 col-md-offset-4">
			<div class="form-group">
				<label for="username">Username</label>
				<input type="text" name="username" class="form-control" placeholder="Username" aria-describedby="sizing-addon1" id="username" <?php if(null !== $accounts_obj->getUsername()) print "value=\"" . $accounts_obj->getUsername() . "\""; ?> >
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-4 col-md-offset-4">
			<div class="form-group">
				<label for="password">Password</label>
				<input type="password" name="password" class="form-control" placeholder="Password" aria-describedby="sizing-addon1" id="password">
			</div>
		</div>
	</div>
	<div class=row>
		<div class="col-md-4 col-md-offset-4">
			<div class="form-group">
				<label for="type">Type</label>
				<select class="form-control" id="select-role" name="type">
					<option value="" class="rhth">Select</option>
					<?php 
					$type = $accounts_obj->getType();
					if ($type == "9") {
						print "<option value=\"9\" selected>Admin</option>";
					}else{
						print "<option value=\"9\">Admin</option>";
					}
					if ($type == "6") {
						print "<option value=\"6\" selected>Operator</option>";
					}else{
						print "<option value=\"6\">Operator</option>";
					}
					if ($type == "3") {
						print "<option value=\"3\" selected>Visitor</option>";
					}else{
						print "<option value=\"3\">Visitor</option>";
					}
					?>
				</select>
			</div>
		</div>
	</div>		


	<div class=row>
		<div class="form-group">
			<div class="col-md-4 col-md-offset-4">
				<?php
				$edit = (isset($_GET['edit']) ? $_GET['edit'] : null);
				if ($edit == "1" || null !== $accounts_obj->getAccount_id()) {
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