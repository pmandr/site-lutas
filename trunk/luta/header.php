<?php
/**
 * Cabeçalho da página
 */
header('Content-type:text/html; charset=utf-8');
?>
<html>
<head>
	<title>Organizador de Eventos de Lutas</title>
	<link rel="stylesheet" href="style.css" media="all" />
</head>
<body>
<h1>Organizador de Eventos de Lutas</h1>
<?php
if (isset($_SESSION['user'])){
	echo 'Logado como: <em>'.$_SESSION['user'].'</em>, <a href="./login.php?action=logout">logout</a>';
	?>
	<div id="menu">
		<ul>
			<li><a href="lutador.php">Lutadores</a></li>
			<li><a href="categoria.php">Categorias</a></li>
			<li><a href="local.php">Locais</a></li>
			<li><a href="organizacao.php">Organizações</a></li>
			<li><a href="patrocinadora.php">Patrocinadoras</a></li>
			<li><a href="evento.php">Eventos</a></li>
			<li><a href="cliente.php">Clientes</a></li>
		</ul>
	</div>
	<?php
} // if (isset($_SESSION['user']))
else { // acesso para clientes
	echo '<div>Olá cliente, seja bem-vindo!</div>';
}
?>
