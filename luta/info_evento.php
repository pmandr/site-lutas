<?php
require_once('connection.php');
require_once('functions.php');
require_once('header.php');

// Recupera informações do local e do evento
$nro = $_GET['evento'];
$rs = sqlQuery("
	select
		l.nomeLoc as local, l.cidade, l.estado, l.endereco, l.capacidade,
		e.nome, e.data, e.horario, 
		p.nome as promotora,
		lt.Lut1, lt.Lut2, li.juiz, li.pontL1, li.pontL2, li.horario as horarioLuta,
		l1.nomeFantasia as nome1, l2.nomeFantasia as nome2
	from Local l
		inner join EventoDeLuta e on l.nomeLoc = e.local
		inner join OrganizacaoPromotora p on e.cnpjPromotora = p.CNPJ
		left join Luta lt on e.nro = lt.numEvento
		left join InfosLuta li on lt.codLuta = li.luta
		left join Lutador l1 on lt.Lut1 = l1.CPF
		left join Lutador l2 on lt.Lut2 = l2.CPF
	where e.nro = '$nro'
");
if (($d = mysql_fetch_object($rs)) === false)
	die('Evento não encontrado!');
?>
<div><a href="./index.php">Início</a> » <a href="./list_events.php?local=<?php echo urlencode($d->cidade.'/'.$d->estado);?>">Eventos em <?php echo $d->cidade.'/'.$d->estado;?></a> » <em><?php echo $d->nome;?></em></div>
<h1>Evento: <?php echo $d->nome;?></h1>
<dl>
<dt>Nome</dt>
<dd><?php echo $d->nome;?></dd>
<dt>Data</dt>
<dd><?php echo $d->data;?></dd>
<dt>Horário</dt>
<dd><?php echo $d->horario;?></dd>
<dt>Local</dt>
<dd><?php echo $d->local.'<br />'.$d->endereco.'<br />'.$d->cidade.'/'.$d->estado;?></dd>
<dt>Capacidade</dt>
<dd><?php echo $d->capacidade;?></dd>
<dt>Promotora</dt>
<dd><?php echo $d->promotora;?></dd>
<dt>Lutas</dt>
<dd>
<table>
<tr><th>Luta</th><th>Juiz</th><th>Horário</th></tr>
<?php
do {
	if ($d->nome1 != null)
		echo "<tr><td><a href=\"./info_lutador.php?lutador=$d->Lut1&evento=$nro\">$d->nome1</a> ($d->pontL1) x ($d->pontL2) <a href=\"./info_lutador.php?lutador=$d->Lut2&evento=$nro\">$d->nome2</a></td><td>$d->juiz</td><td>$d->horarioLuta</td></tr>";
} while(($d = mysql_fetch_object($rs)) !== false);
?>
</table>
</dd>
<dt>Patrocinadoras</dt>
<dd>
<?php
mysql_free_result($rs);
//Seleciona as patrocinadoras de um evento para divulgá-las junto dele
$rs = sqlQuery("
	select e.nome, e.website
	from EmpresaPatrocinadora e
		inner join Patrocinio p on p.cnpjPat = e.CNPJ
	where p.numEvento = $nro
");
while (($d = mysql_fetch_object($rs)) !== false) {
	echo "<a href=\"$d->website\">$d->nome</a><br />";
}
?>
</dd>
<dt>Comprar Ingressos</dt>
<dd>
	<form method="post" action="./compra.php">
		<fieldset>
			<legend>Informações da compra</legend>
			<div><em>Compra online apenas para clientes cadastrados!</em></div>
			<input type="hidden" name="nro" value="<?php echo $nro;?>" />
			<input type="hidden" name="step" value="compra" />
			Seu CPF: <input type="text" name="CPF" /><br />
			Endereço de Entrega: <input type="text" name="endEntrega" maxlength="30" /><br />
			<input type="submit" value="Continuar &gt;&gt;" />
	</form>
</dd>
</dl>
<?php
mysql_free_result($rs);
require_once('footer.php');

