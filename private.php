<!DOCTYPE html>
<html>
    <head>
        <title>The Griffen - Nothing suspicious here ;)</title>
        <link rel="stylesheet" type="text/css" href="style.css" />
		

		
    </head>
    
    <body>
    
        <header id="head" >
			<p><a href="index.php?page=login" style="text-decoration:none"><span id="main">The Griffen</span></a></p>
			<p><a href="logout.php"><span id="logout">Logout</span></a></p>
        </header>
        
        <div id="main-wrapper">
			
			<?php

				// First we execute our common code to connection to the database and start the session
				require("common.php");
				
				// At the top of the page we check to see whether the user is logged in or not
				if(empty($_SESSION['user']))
				{
					// If they are not, we redirect them to the login page.
					header("Location: index.php?page=login");
					
					// Remember that this die statement is absolutely critical.  Without it,
					// people can view your members-only content without logging in.
					die("Redirecting to login.php");
				}
				
				// Everything below this point in the file is secured by the login system
				
				// We can display the user's username to them by reading it from the session array.  Remember that because
				// a username is user submitted content we must use htmlentities on it before displaying it to the user.
			?>
			<div id="content">
				Hello <?php echo htmlentities($_SESSION['user']['username'], ENT_QUOTES, 'UTF-8'); ?>, secret content!<br />
				<a href="index.php?page=members">Memberlist</a><br />
				<a href="index.php?page=edit">Edit Account</a><br />
				<a href="logout.php">Logout</a>
				<?php include('files.php'); ?>
			</div>
			
        </div>
    </body>
</html> 