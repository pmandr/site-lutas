<?php
require_once('connection.php');
require_once('functions.php');
require_once('header.php');
?>
Selecione o local do evento que está procurando:
<form method="get" action="./list_events.php">
<select name="local">
<?php
$rs = sqlQuery("
	select distinct cidade, estado
	from Local
	order by cidade, estado
");
while (($d = mysql_fetch_object($rs)) !== false) {
	echo "<option value=\"$d->cidade/$d->estado\">$d->cidade - $d->estado</option>";
}
mysql_free_result($rs);
?>
</select>
<input type="submit" value="Ok" />
</form>
Ou então, veja informações sobre um lutador:
<form method="get" action="./info_lutador.php">
<select name="lutador">
<?php
$rs = sqlQuery("
	select CPF, nome, nomeFantasia
	from Lutador
	order by nomeFantasia, nome
");
while (($d = mysql_fetch_object($rs)) !== false) {
	echo "<option value=\"$d->CPF\">$d->nomeFantasia - $d->nome</option>";
}
mysql_free_result($rs);
?>
</select>
<input type="submit" value="Ok" />
</form>
<?php
require_once('footer.php');

