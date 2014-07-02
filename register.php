<div id="register-wrapper">
	<form action="insert.php" method="post">
		<ul>
			<li>
				<label for="username">Username</label>
				<input type="varchar" name="username"/>
			</li>
			<li>
				<label for="password">Password</label>
				<input type="password" name="password" />
			</li>
			<li>
				<label for="conpassword">Confirm Password</label>
				<input type="password" name="conpassword">
			</li>
			<li class="buttons">
				<input type="submit" onclick="location.href='index.php?page=login'" />
			</li>
		</ul>
	</form>
</div>