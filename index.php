<!DOCTYPE html>
<html>
<head>
	<title>Contact Form</title>
	<meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="favicon.ico">
	<link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="favicon-16x16.png">
	<link rel="manifest" href="site.webmanifest">
	<link rel="mask-icon" href="safari-pinned-tab.svg" color="#5bbad5">
	<meta name="msapplication-TileColor" content="#da532c">
	<meta name="theme-color" content="#ffffff">
	<link href="style.css" rel="stylesheet" type="text/css" />
</head>
<body>
	<?php
		$name = "";
		$email = "";
		$message = "";
		$success = "";
		$warning = "";
		$ok = "";

		if (isset($_POST['submit'])) {
			$ok = true;

			if (!isset($_POST['name']) || $_POST['name'] === '') {
				$ok = false;
				$warning = 'All fields must be filled in.';
			} else {
				$name = $_POST['name'];
			}

			if (!isset($_POST['email']) || $_POST['email'] === '') {
				$ok = false;
				$warning = 'All fields must be filled in.';
			} else {
				$email = $_POST['email'];
			}

			if (!isset($_POST['message']) || $_POST['message'] === '') {
				$ok = false;
				$warning = 'All fields must be filled in.';
			} else {
				$message = $_POST['message'];
			}

			if ($ok) {
				if (getenv('SERVER_NAME') === 'localhost') {
					$db = mysqli_connect('localhost:3307', 'root', '', 'php');

					$sql = sprintf("INSERT INTO messages (name, email, message) Values ('%s', '%s', '%s'
					)", mysqli_real_escape_string($db, $name),
					mysqli_real_escape_string($db, $email),
					mysqli_real_escape_string($db, $message));
					mysqli_query($db, $sql);
					mysqli_close($db);

					$success = 'Thank you. Your submission was saved.';
					$name = "";
					$email = "";
					$message = "";
				}
				
				else {
					$url = parse_url(getenv('CLEARDB_DATABASE_URL'));
					$server = $url['host'];
					$username = $url['user'];
					$password = $url['pass'];
					$db = substr($url['path'], 1);
					
					$conn = mysqli_connect($server, $username, $password, $db);

					$sql = sprintf("INSERT INTO messages (name, email, message) VALUES ('%s', '%s', '%s')",
						mysqli_real_escape_string($conn, $name),
						mysqli_real_escape_string($conn, $email),
						mysqli_real_escape_string($conn, $message));

					$result = mysqli_query($conn, $sql);

					if (!$result) {
						echo 'an error occurred.';
					}

					else {
						$success = 'Thank you. Your submission was saved.';
						mysqli_close($conn);
					}

					$name = "";
					$email = "";
					$message = "";
				}
			}
		}
	?>

	<div class="form-container">
		<div class="form-message-container">
			<h3 class="text-center warning-message"><i><?php echo $warning ?></i></h3>
			<h3 class="text-center success-message"><?php echo $success ?></h3>
		</div>

		<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
			<label for="name">Name: </label>
			<br>
			<input name="name" type="text" value="<?php echo htmlspecialchars($name) ?>">
			<br>

			<label for="email">Email: </label>
			<br>
			<input name="email" type="email" value="<?php echo htmlspecialchars($email) ?>">
			<br>

			<label for="message">Message: </label>
			<br>
			<textarea name="message" type="text"><?php echo htmlspecialchars($message) ?></textarea>
			<br>

			<input class="submit-btn" name="submit" type="submit" value="Submit">
		</form>
	</div>

	<div class="message-container">
		<?php
			if (getenv('SERVER_NAME') === 'localhost') {
				$db2 = mysqli_connect('localhost:3307', 'root', '', 'php');

				$sql2 = 'SELECT * FROM messages ORDER BY id DESC LIMIT 15';

				$result = mysqli_query($db2, $sql2);

				foreach ($result as $row) {
					printf('<h6 class="user-name">%s</h6><h4 class="user-message">%s</h4>',
						htmlspecialchars($row['name']),
						htmlspecialchars($row['message'])
					);
				}

				mysqli_close($db2);
			}

			else {
				$url = parse_url(getenv('CLEARDB_DATABASE_URL'));
				$server = $url['host'];
				$username = $url['user'];
				$password = $url['pass'];
				$db = substr($url['path'], 1);

				$conn2 = mysqli_connect($server, $username, $password, $db);

				$sql2 = 'SELECT * FROM messages ORDER BY id DESC LIMIT 15';
				
				$result = mysqli_query($conn2, $sql2);

				if (!$result) {
					echo 'Something went wrong '.$conn2->errorno;
				}

				else {
					foreach ($result as $row) {
						printf('<h6 class="user-name">%s</h6><h4 class="user-message">%s</h4>',
							htmlspecialchars($row['name']),
							htmlspecialchars($row['message'])
						);
					}
				}
			}
		?>
	</div>
</body>
</html>