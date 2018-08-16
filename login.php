<?php
include ("header.php");
include ("common.php");
include ("navigation.php");

?>
<body><center>
<h2>Login Here</h2>

<div class="container">
	<form action="login_submit.php" method="post">
		<div class="row">
			<div class="col-md-4"></div>
			<div class="col-md-4">
				<div class="form-group input-group">
					<span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
					<input type="text" id="username" name="username" class="form-control" placeholder="username" aria-describedby="sizing-addon1" value="" maxlength="20" size="30"/>
				</div>
			</div>
			<div class="col-md-4"></div>
		</div>
		<div class="row">
			<div class="col-md-4"></div>
			<div class="col-md-4">
				<div class="form-group input-group">
					<span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
					<input type="password" id="password" name="password" class="form-control" placeholder="password" aria-describedby="sizing-addon1" value="" maxlength="20" />
				</div>
			</div>
			<div class="col-md-4"></div>
		</div>
		
			<div class="form-group">
				<button type="submit"  class="btn btn-default" value="Login">Login</button>
			</div>
	</form>
</div>
</body>
