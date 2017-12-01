<nav class="navbar navbar-inverse">
        <div class="container-fluid" style="display: flex;justify-content: center;">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="index.php">Raisepon</a>
		</div>
		<?php if (isset($user_class) && $user_class >= "1") { ?>
		<div class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
				<li <?=echoActiveClassIfRequestMatches("index")?>><a href="index.php">Home</a></li>
				<li <?=echoActiveClassIfRequestMatches("customers")?>><a href="customers.php">Customers</a></li>
				<?php if ($user_class >= "6") { ?>
				<li <?=echoActiveClassIfRequestMatches("olt")?>><a href="olt.php">OLT</a></li>
				<li <?=echoActiveClassIfRequestMatches("pon")?>><a href="pon.php">PON</a></li>
				<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown" id="xpon" role="button" aria-haspopup="true" aria-expanded="false">Profiles <span class="caret"></span></a>
                <ul class="dropdown-menu">
					<li class="dropdown-header">xPON</li>
					<li role="separator" class="divider"></li>
					<li <?=echoActiveClassIfRequestMatches("line_profile")?>><a href="line_profile.php">Line Profiles</a></li>
					<li <?=echoActiveClassIfRequestMatches("service_profile")?>><a href="service_profile.php">Service Profiles</a></li>
					<li <?=echoActiveClassIfRequestMatches("services")?>><a href="services.php">Services</a></li>
                </ul>
            </li>
			<?php } ?>
			<li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" id="tools" role="button" aria-haspopup="true" aria-expanded="false">Tools <span class="caret"></span></a>
                <ul class="dropdown-menu">
					<li <?=echoActiveClassIfRequestMatches("graphs")?>><a href="graphs.php">Graphs</a></li>
					<li <?=echoActiveClassIfRequestMatches("logs")?>><a href="logs.php">Logs</a></li>
					<li <?=echoActiveClassIfRequestMatches("mac_trace")?>><a href="mac_trace.php">Trace</a></li>
				</ul>
			</li>
				<?php if ($user_class == "9") { ?>
				<li><a href="accounts.php">Accounts</a></li>
				<?php } ?>
			  
            </ul>
			<a href="logout.php"><button class="btn navbar-btn">Logout</button></a>
          </div><!--/.nav-collapse -->
		  <?php } ?>
        </div>
      </nav>
	  
		
