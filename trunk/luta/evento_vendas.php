<?php
/**
 * Script responsável por cadastrar as lutas de um evento
 */
require_once('check_admin.php');
require_once('functions.php');
require_once('header.php');

$nro = $_GET['nro'];

// Recupera todos os registros
?>
<div><a href="./evento.php?nro=<?php echo $nro;?>&action=edit">Voltar para o evento</a></div>
<h2>Vendas deste evento</h2>
<table>
<tr><th>Venda</th><th>Cliente</th><th>Entrega</th><th>Preço</th><th>Ingressos</th></tr>
<?php
//Recupera registros para a visualização das compras de um determinado evento
$rs = sqlQuery("
	select
		cli.nome, cli.CPF, c.nroCompra, c.endEntrega, c.preco, c.quantidade,
		i.nroSerie, i.assento, i.preco as preco_ingresso
	from Compra c
		inner join Cliente cli on c.comprador = cli.CPF
		inner join Ingresso i on i.compra = c.nroCompra
	where i.numEvento = $nro
	order by c.nroCompra, i.nroSerie
");
$last = false;
function writeCompra($d, $rs) {
	if ($d === false)
		return;
	$current = $d->nroCompra;
	echo "<tr><td valign=top>$d->nroCompra</td><td valign=top>$d->nome ($d->CPF)</td><td valign=top>$d->endEntrega</td><td valign=top align=right>$d->preco</td>";
	echo "<td><table width=100%><tr><th>#</th><th>Assento</th><th>Preço</th></tr>";
	do {
		if ($d->nroCompra != $current) {
			echo "</td></tr></table>";
			writeCompra($d, $rs);
		} else {
			echo "<tr><td>$d->nroSerie</td><td>$d->assento</td><td>$d->preco_ingresso</td></tr>";
		}
	} while(($d = mysql_fetch_object($rs)) !== false);
	echo "</td></tr></table>";
}
writeCompra(mysql_fetch_object($rs), $rs);
?>
</table>
<?php
require_once('footer.php');
?>

