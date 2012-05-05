<?php
// Checa se o usuário é administrador
session_start();
if (!isset($_SESSION['user'])) {
	require_once('login.php');
	if (!isset($_SESSION['user'])) // Não fez login
		exit;
}

