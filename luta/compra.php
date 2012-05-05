<?php
require_once('connection.php');
require_once('functions.php');
require_once('header.php');

// Recupera informações do local e do evento
$nro = post('nro');
$step = post('step');
$cpf = post('CPF');
$endEntrega = post('endEntrega');

// Recupera informações básicas do local e do evento (para montar o "breadcumb")
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


?>
<div><a href="./index.php">Início</a> » <a href="./list_events.php?local=<?php echo urlencode($e->cidade.'/'.$e->estado);?>">Eventos em <?php echo $e->cidade.'/'.$e->estado;?></a> » <a href="./info_evento.php?evento=<?php echo $nro;?>"><?php echo $e->nome;?></a> » <em>Compra</em></div>
<?php

if ($step == 'finaliza') {
	if (!isset($_POST['ingresso']) || sizeof($_POST['ingresso']) == 0) {
		die('Nenhum ingresso selecionado!');
	}
	$ingresso = $_POST['ingresso'];
	//Insere valores relativos às escolhas de ingresso para compra
	sqlQuery("
		insert into Compra(comprador,preco,quantidade,desconto,endEntrega)
		values('$cpf', 0.00, ".sizeof($ingresso).", 0.00, '$endEntrega')
	");
	// Recupera o nroCompra
	$rs = sqlQuery("select max(nroCompra) as nroCompra from Compra");
	$d = mysql_fetch_object($rs);
	$nroCompra = $d->nroCompra;
	mysql_free_result($rs);
	foreach ($ingresso as $nroSerie => $value) {
		//o	Liga o ingresso à compra em questão
		sqlQuery("update Ingresso set compra = $nroCompra where nroSerie = $nroSerie");
	}
	// Atualiza o preco total
	sqlQuery("
		update Compra
		set preco = (select sum(preco) from Ingresso where compra = $nroCompra)
		where nroCompra = $nroCompra
	");
	echo '<strong>Compra finalizada!</strong><br />Os detalhes serão enviados ao seu e-mail!';
	require_once('footer.php');
	exit;
}
//Tenta encontrar o cliente da compra
$rs = sqlQuery("select count(*) total from Cliente where CPF = '$cpf'");
if (($d = mysql_fetch_object($rs)) === false || $d->total == 0)
	die('Cliente não encontrado!');
mysql_free_result($rs);
//Seleciona dados de cada ingresso para colocá-los na tabela
$rs = sqlQuery("
	select i.nroSerie, i.assento, i.preco
	from Ingresso i
	where i.numEvento = '$nro' and i.compra is null
");
?>
<form method="post" action="">
<input type="submit" value="Finalizar compra" /><br />
<input type="hidden" name="step" value="finaliza" />
<input type="hidden" name="CPF" value="<?php echo $cpf;?>" />
<input type="hidden" name="nro" value="<?php echo $nro;?>" />
Confira o endereço: <input type="text" name="endEntrega" value="<?php echo $endEntrega;?>" maxlength="30" />
<h2>Selecione os assentos desejados:</h2>
<table>
	<tr><th>#</th><th>Assento</th><th>Preço</th></tr>
<?php
while (($d = mysql_fetch_object($rs)) !== false) {
	echo "<tr><td><input type=\"checkbox\" name=\"ingresso[$d->nroSerie]\" value=\"y\" /> $d->nroSerie</td>";
	echo "<td>$d->assento</td><td>$d->preco</td></tr>";
}
?>
</table>
<input type="submit" value="Finalizar compra" />
</form>
<?php
require_once('footer.php');

