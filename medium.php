<html>
    <head>
        <title>Login Page</title>
        <link rel="stylesheet" type="text/css" href="style.css" />
		

		
    </head>
	
	<body>
		<header id="head" >
			<p><a href="index.php?page=login" style="text-decoration:none"><span id="main">CrouchingRussians</span></a></p>
			<p><a href="logout.php"><span id="logout">Logout</span></a></p>
		</header>
	

<div id="login-wrapper">
	<form action="insertMedium.php" method="post">
		<?php require("common.php"); ?>
		
		<ul>
			<li>
				<label for="num">Team : </label>
				<input type="text" id="num" maxlength="4" required autofocus name="Team" value="<?php echo($_SESSION['user']['team']); ?>" />
			</li>
					
			<li class="buttons">
				<input type="submit" name="Swerve" value="Swerve" />
			</li>
			<li class="buttons">
				<input type="submit" name="Tank" value="Tank" />
			</li>	
		</ul>
	</form>
</div>
</body>
</html>