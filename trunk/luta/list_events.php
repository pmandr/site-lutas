<?php
require_once('connection.php');
require_once('functions.php');
require_once('header.php');

if (!preg_match('/^(.*)\\/([^\\/]*)$/', $_GET['local'], $ar)) {
	die('Local inválido!');
}
$cidade = $ar[1];
$estado = $ar[2];
?>
<div><a href="./index.php">Início</a> » Eventos em <em><?php echo $_GET['local'];?></em></div>
<h1>Eventos em <?php echo $_GET['local'];?></h1>
<table>
<tr><th>Local</th><th>Eventos</th></tr>
<?php
//seleciona eventos para serem mostrados ao usuario de acordo com a cidade
//... where l.cidade = $cidade
$rs = sqlQuery("
	select l.nomeLoc as local, e.nome, e.nro, e.data
	from Local l inner join EventoDeLuta e on l.nomeLoc = e.local
	order by l.nomeLoc, e.data, e.horario
");
$last = '';
while (($d = mysql_fetch_object($rs)) !== false) {
	if ($d->local != $last) {
		if ($last != '')
			echo '</td></tr>';
		echo "<tr><td>$d->local</td><td>";
		$last = $d->local;
	}
	echo "<strong>$d->data</strong>: <a href=\"info_evento.php?evento=$d->nro\">$d->nome</a><br />";
}
if ($last != '')
	echo '</td></tr>';
mysql_free_result($rs);
?>
</table>
<?php
require_once('footer.php');

