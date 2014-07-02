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
	require("common.php");
	// retrieve token
	if (isset($_GET["token"]) && preg_match('/^[0-9A-Z]{5}$/i', $_GET["token"])) {
		$token = $_GET["token"];
	}
	else {
		echo'<div id="content" style="text-align:center; font-size:40pt"><strong>STOP!!!</strong></div>';
		throw new Exception("Valid token not provided.");
	}
 
	// verify token
	$query = $db->prepare("SELECT username, tstamp FROM pending_users WHERE token = ?");
	$query->execute(array($token));
	$row = $query->fetch(PDO::FETCH_ASSOC);
	$query->closeCursor();
 
	if ($row) {
		extract($row);
	}
	else {
		echo'<div id="content" style="text-align:center; font-size:40pt"><strong>SERIOSLY, I\'LL KILL YOU!!!</strong></div>';
		throw new Exception("Valid token not provided.");
	}
 
	// do one-time action here, like activating a user account
	// ...
 

    // First we execute our common code to connection to the database and start the session
    
    // This if statement checks to determine whether the registration form has been submitted
    // If it has, then the registration code is run, otherwise the form is displayed
    if(!empty($_POST))
    {
        // Ensure that the user has entered a non-empty username
        if(empty($_POST['username']))
        {
            // Note that die() is generally a terrible way of handling user errors
            // like this.  It is much better to display the error with the form
            // and allow the user to correct their mistake.  However, that is an
            // exercise for you to implement yourself.
            die("Please enter a username.");
        }
        
        // Ensure that the user has entered a non-empty password
        if(empty($_POST['password']))
        {
            die("Please enter a password.");
        }
        
        // Make sure the user entered a valid E-Mail address
        // filter_var is a useful PHP function for validating form input, see:
        // http://us.php.net/manual/en/function.filter-var.php
        // http://us.php.net/manual/en/filter.filters.php
        if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
        {
            die("Invalid E-Mail Address");
        }
        
        // We will use this SQL query to see whether the username entered by the
        // user is already in use.  A SELECT query is used to retrieve data from the database.
        // :username is a special token, we will substitute a real value in its place when
        // we execute the query.
        $query = "
            SELECT
                1
            FROM login
            WHERE
                username = :username
        ";
        
        // This contains the definitions for any special tokens that we place in
        // our SQL query.  In this case, we are defining a value for the token
        // :username.  It is possible to insert $_POST['username'] directly into
        // your $query string; however doing so is very insecure and opens your
        // code up to SQL injection exploits.  Using tokens prevents this.
        // For more information on SQL injections, see Wikipedia:
        // http://en.wikipedia.org/wiki/SQL_Injection
        $query_params = array(
            ':username' => $_POST['username']
        );
        
        try
        {
            // These two statements run the query against your database table.
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex)
        {
            // Note: On a production website, you should not output $ex->getMessage().
            // It may provide an attacker with helpful information about your code. 
            die("Failed to run query: " . $ex->getMessage());
        }
        
        // The fetch() method returns an array representing the "next" row from
        // the selected results, or false if there are no more rows to fetch.
        $row = $stmt->fetch();
        
        // If a row was returned, then we know a matching username was found in
        // the database already and we should not allow the user to continue.
        if($row)
        {
            die("This username is already in use");
        }
        
        // Now we perform the same type of check for the email address, in order
        // to ensure that it is unique.
        $query = "
            SELECT
                1
            FROM login
            WHERE
                email = :email
        ";
        
        $query_params = array(
            ':email' => $_POST['email']
        );
        
        try
        {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex)
        {
            die("Failed to run query: " . $ex->getMessage());
        }
        
        $row = $stmt->fetch();
        
        if($row)
        {
            die("This email address is already registered");
        }
        
        // An INSERT query is used to add new rows to a database table.
        // Again, we are using special tokens (technically called parameters) to
        // protect against SQL injection attacks.
        $query = "
            INSERT INTO login (
                username,
                password,
                salt,
                email
            ) VALUES (
                :username,
                :password,
                :salt,
                :email
            )
        ";
        
        // A salt is randomly generated here to protect again brute force attacks
        // and rainbow table attacks.  The following statement generates a hex
        // representation of an 8 byte salt.  Representing this in hex provides
        // no additional security, but makes it easier for humans to read.
        // For more information:
        // http://en.wikipedia.org/wiki/Salt_%28cryptography%29
        // http://en.wikipedia.org/wiki/Brute-force_attack
        // http://en.wikipedia.org/wiki/Rainbow_table
		if($_POST['password'] == $_POST['conpassword']) {
			$salt = dechex(mt_rand(0, 2147483647)) . dechex(mt_rand(0, 2147483647));
			
			// This hashes the password with the salt so that it can be stored securely
			// in your database.  The output of this next statement is a 64 byte hex
			// string representing the 32 byte sha256 hash of the password.  The original
			// password cannot be recovered from the hash.  For more information:
			// http://en.wikipedia.org/wiki/Cryptographic_hash_function
			$password = hash('sha256', $_POST['password'] . $salt);
			
			// Next we hash the hash value 65536 more times.  The purpose of this is to
			// protect against brute force attacks.  Now an attacker must compute the hash 65537
			// times for each guess they make against a password, whereas if the password
			// were hashed only once the attacker would have been able to make 65537 different 
			// guesses in the same amount of time instead of only one.
			for($round = 0; $round < 65536; $round++)
			{
				$password = hash('sha256', $password . $salt);
			}
			
			// Here we prepare our tokens for insertion into the SQL query.  We do not
			// store the original password; only the hashed version of it.  We do store
			// the salt (in its plaintext form; this is not a security risk).
			$query_params = array(
				':username' => $_POST['username'],
				':password' => $password,
				':salt' => $salt,
				':email' => $_POST['email']
			);
			
			try
			{
				// Execute the query to create the user
				$stmt = $db->prepare($query);
				$result = $stmt->execute($query_params);
			}
			catch(PDOException $ex)
			{
				// Note: On a production website, you should not output $ex->getMessage().
				// It may provide an attacker with helpful information about your code. 
				die("Failed to run query: " . $ex->getMessage());
			}
				// delete token so it can't be used again
				$query = $db->prepare(
					"DELETE FROM pending_users WHERE username = ? AND token = ? AND tstamp = ?"
				);
				$query->execute(
					array(
						$username,
						$token,
						$tstamp
					)
				);
		}
		else {
			die("Password don't match.\nPleas try again");
		}
        // This redirects the user back to the login page after they register
        header("Location: index.php?page=login");
        
        // Calling die or exit after performing a redirect using the header function
        // is critical.  The rest of your PHP script will continue to execute and
        // will be sent to the user if you do not die or exit.
        die("Redirecting to login.php");
    }
    
?>
			<div id="register-wrapper">
				<form action="finalstep.php?token=<?php echo($token) ?>" method="post">
					<ul>
						<li>
							<label for="usn">Username : </label>
							<input type="text" id="usn" value "" maxlength="30" required autofocus name="username" />
						</li>

						<li>
							<label for="email">Email : </label>
							<input type="text" id="email" value="" maxlength="30" required name="email" />
						</li>

						<li>
							<label for="passwd">Password : </label>
							<input type="password" id="passwd" value="" maxlength="30" required name="password" />
						</li>

						<li>
							<label for="conpasswd">Confirm Password : </label>
							<input type="password" id="conpasswd" maxlength="30" required name="conpassword" />
						</li>
						<li class="buttons">
							<input type="submit" value="Register" />
							<input type="button" name="cancel" value="Cancel" onclick="location.href='index.php?page=login'" />
						</li>

					</ul>
				</form>
			</div>
        </div>
    </body>
</html> 