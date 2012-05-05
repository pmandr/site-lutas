<?php

$request_login = true;
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
	session_start();
	unset($_SESSION['user']);
	$request_login = false;
	require_once('index.php');
} else if (isset($_POST['user']) && isset($_POST['password'])) {
	$users = Array(
		'admin' => 'admin'
	);
	if (!isset($users[$_POST['user']]) || $users[$_POST['user']] != $_POST['password']) {
		$message = 'Usuário ou senha inválidos!';
		$request_login = true;
	} else {
		$_SESSION['user'] = $_POST['user'];
		$request_login = false;
	}
}

if ($request_login) {
	require_once('header.php');
	if (isset($message)) {
		echo "<div><strong>$message</strong></div>";
	}
	?>
	<form method="post" action="">
		<fieldset>
			<legend>Login</legend>
			Usuário: <br />
			<input type="text" name="user" /><br />
			Senha: <br />
			<input type="password" name="password" /><br />
			<input type="submit" value="Acessar" />
	</form>
	<?php
	require_once('footer.php');
}
