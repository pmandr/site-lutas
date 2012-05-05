<?php
require_once('connection.php');
require_once('functions.php');
require_once('header.php');


// Recupera informações básicas do local e do evento (para montar o "breadcumb")
$nro = (isset($_GET['evento']) ? $_GET['evento'] : false);
if ($nro) {
	$rs = sqlQuery("
		select
			l.cidade, l.estado, e.nome
		from Local l
			inner join EventoDeLuta e on l.nomeLoc = e.local
		where e.nro = '$nro'
	");
	if (($e = mysql_fetch_object($rs)) === false)
		die('Evento não encontrado!');
	mysql_free_result($rs);
}

// Recupera informações do lutador
$CPF = $_GET['lutador'];
$rs = sqlQuery("
	select CPF, nome, nomeFantasia, peso, altura, nacionalidade, vitorias, empates, derrotas, categoria
	from Lutador
	where CPF = '$CPF'
");
if (($d = mysql_fetch_object($rs)) === false)
	die('Lutador não encontrado!');
?>
<?php
	if ($nro) {
	?>
	<div><a href="./index.php">Início</a> » <a href="./list_events.php?local=<?php echo urlencode($e->cidade.'/'.$e->estado);?>">Eventos em <?php echo $e->cidade.'/'.$e->estado;?></a> » <a href="./info_evento.php?evento=<?php echo $nro;?>"><?php echo $e->nome;?></a> » <em><?php echo $d->nomeFantasia;?></em></div>
	<?php
	} else {
	?>
	<div><a href="./index.php">Início</a> » <em><?php echo $d->nomeFantasia;?></em></div>
	<?php
	}
	?>

<h1>Lutador: <?php echo $d->nomeFantasia;?></h1>
<dl>
<dt>Nome</dt>
<dd><?php echo $d->nome;?></dd>
<dt>Apelido</dt>
<dd><?php echo $d->nomeFantasia;?></dd>
<dt>Peso</dt>
<dd><?php echo $d->peso;?> Kg</dd>
<dt>Altura</dt>
<dd><?php echo $d->altura;?> m</dd>
<dt>Nacionalidade</dt>
<dd><?php echo $d->nacionalidade;?></dd>
<dt>Número de vitórias</dt>
<dd><?php echo $d->vitorias;?></dd>
<dt>Número de empates</dt>
<dd><?php echo $d->empates;?></dd>
<dt>Categoria</dt>
<dd><?php echo ($d->categoria == null ? '<em>Não especificado</em>' : $d->categoria);?></dd>
</dl>
<?php
mysql_free_result($rs);
require_once('footer.php');

