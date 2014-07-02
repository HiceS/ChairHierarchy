<?php

	$pagename = htmlspecialchars($_GET["page"]);

	if ($pagename == "") {
		$pagename = "login";
	}

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Login Page</title>
        <link rel="stylesheet" type="text/css" href="style.css" />
		

		
    </head>
    
    <body>
    
        <header id="head" >
			<p><a href="index.php?page=login" style="text-decoration:none"><span id="main">ShacksonAdventures</span></a></p>
			<p><a href="logout.php"><span id="logout">Logout</span></a></p>
        </header>
        
        <div id="main-wrapper">
			
			<?php 				
				if ($pagename == "login") {
					include("login.php");
				} elseif ($pagename == "private") {
					include("private.php");
				} elseif ($pagename == "register") {
					include("register.php");
				} elseif ($pagename == "members") {
					include("memberlist.php");
				} elseif ($pagename == "edit") {
					include("edit_account.php");
				} elseif ($pagename == "finalstep") {
					include("finalstep.php");
				}
			?>
			
        </div>
    </body>
</html> 