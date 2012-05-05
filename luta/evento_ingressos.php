<?php
/**
 * Script responsável por cadastrar as lutas de um evento
 */
require_once('check_admin.php');
require_once('functions.php');
require_once('header.php');

$nro = $_GET['nro'];
$codLuta = post('codLuta');
$novo = !isset($_GET['codLuta']);
$action = getAction();
switch ($action) {
	case INSERT:
		$de = (int)post('de');
		$ate = (int)post('ate');
		$assento = $primeiro = (int)post('assento');
		$preco = post('preco');
		for ($i = $de; $i <= $ate; $i++) {
			//Insere dados de um ingresso novo 
			sqlQuery("
				insert into Ingresso(nroSerie, numEvento, preco, assento)
				values($i, $nro, $preco, $assento)
			");
			++$assento;
		}
		break;
	case DELETE:
		$de = (int)$_GET['de'];
		$ate = (int)$_GET['ate'];
		//Deleta intervalo de números de seria de ingresso
		sqlQuery("delete from Ingresso where nroSerie between $de and $ate and numEvento = $nro");
		echo '<div><strong>Registro excluído!</strong></div>';
		break;
}
/*
switch ($action) {
	case INSERT:
	case UPDATE:
		$codLuta = post('codLuta');
		$Lut1 = post('Lut1');
		$Lut2 = post('Lut2');
		$horario_luta = post('horario_luta');
		$juiz = post('juiz');
		$pontL1 = post('pontL1');
		$pontL2 = post('pontL2');
		$nro = post('nro');
		if (is_numeric($pontL1)) $sql_pontL1 = $pontL1;
		else $sql_pontL1 = 'null';
		if (is_numeric($pontL2)) $sql_pontL2 = $pontL2;
		else $sql_pontL2 = 'null';
		if ($action == INSERT) {
			$sql1 = "insert into Luta(Lut1, Lut2, numEvento)
					values('$Lut1', '$Lut2', '$nro')";
			$sql2 = "insert into InfosLuta(luta, horario, juiz, pontL1, pontL2)
					values((select max(codLuta) from Luta), '$horario_luta', '$juiz', $sql_pontL1, $sql_pontL2)";
		} else {
			$sql1 = "
				update Luta
				set Lut1 = '$Lut1',
					Lut2 = '$Lut2'
				where codLuta = '$codLuta'";
			$sql2 = "
				update InfosLuta
				set horario = '$horario_luta',
					juiz = '$juiz',
					pontL1 = $sql_pontL1,
					pontL2 = $sql_pontL2
				where luta = '$codLuta'";
		}
		sqlQuery($sql1);
		sqlQuery($sql2);
		echo '<div><strong>Registro salvo!</strong></div>';
		break;
	case EDIT:
		$codLuta = $_GET['codLuta'];
		$sql = "
			select l.Lut1, l.Lut2, i.horario, i.juiz, i.pontL1, i.pontL2
			from Luta l inner join InfosLuta i on l.codLuta = i.luta
			where l.codLuta = $codLuta";
		$rs = sqlQuery($sql);
		if (($d = mysql_fetch_object($rs)) !== false){
			$Lut1 = $d->Lut1;
			$Lut2 = $d->Lut2;
			$horario_luta = $d->horario;
			$juiz = $d->juiz;
			$pontL1 = $d->pontL1;
			$pontL2 = $d->pontL2;
		} else {
			die('Luta não encontrada!');
		}
		mysql_free_result($rs);
		break;
	case DELETE:
		$codLuta = $_GET['codLuta'];
		$sql = "delete from Luta where codLuta = '$codLuta'";
		sqlQuery($sql);
		echo '<div><strong>Registro excluído!</strong></div>';
		// Não dá um "break" pra entrar no "default"
		$codLuta = '';
	default:
		$codLuta = '';
		$Lut1 = '';
		$Lut2 = '';
		$horario_luta = '';
		$juiz = '';
		$pontL1 = '';
		$pontL2 = '';
}
//*/

// Recupera todos os registros
?>
<div><a href="./evento.php?nro=<?php echo $nro;?>&action=edit">Voltar para o evento</a></div>
<h2>Séries</h2>
<table>
<tr><th>Intervalo</th><th>Assentos</th><th>Quantidade</th><th>Preço</th><th>Vendidos</th></tr>
<?php
$rs = sqlQuery("
	select i.nroSerie, i.preco, i.compra, i.assento
	from Ingresso i
	where i.numEvento = '$nro'
	order by i.nroSerie");
$serie = -1;
$first = false;
$last_preco = false;
$last = false;
$afirst = false;
$alast = false;
$vendas = 0;
while (($d = mysql_fetch_object($rs)) !== false) {
	if ($first && ($d->assento != ($alast + 1) || $d->nroSerie != ($last + 1) || $d->preco != $last_preco)) {
		echo "<tr><td>$first a $last</td><td>$afirst a $alast</td><td>".($last - $first)."</td><td>$last_preco</td><td>$vendas</td></tr>";
		$first = $d->nroSerie;
		$afirst = $d->assento;
		$vendas = 0;
	}
	if (!$first) {
		$first = $d->nroSerie;
		$afirst = $d->assento;
	}
	$last = $d->nroSerie;
	$alast = $d->assento;
	$last_preco = $d->preco;
	if ($d->compra) ++$vendas;
}
if ($first)
	echo "<tr><td>$first a $last</td><td>$afirst a $alast</td><td>".($last - $first)."</td><td>$last_preco</td><td>$vendas</td></tr>";
?>
</table>
<h3><?php echo ($novo?'Nova':'Editar');?></h3>
<form method="post" action="">
	<fieldset>
		<legend><?php echo ($novo?'Novo':'Editar');?></legend>
		<input type="hidden" name="action" value="<?php echo ($novo?'new':'edit');?>" />
		<input type="hidden" name="nro" value="<?php echo $nro;?>" />
		Série: <br />
		De: <input type="text" name="de" /> até <input type="text" name="ate" /><br />
		Primeiro assento: <input type="text" name="assento" /><br />
		Preço: <input type="text" name="preco" /><br />
		<input type="submit" value="Salvar" /> <input type="reset" value="Cancelar" />
	</fieldset>
</form>
<form method="get" action="">
	<fieldset>
		<legend>Remover intervalo</legend>
		<input type="hidden" name="action" value="delete" />
		<input type="hidden" name="nro" value="<?php echo $nro;?>" />
		Série: <br />
		De: <input type="text" name="de" /> até <input type="text" name="ate" /><br />
		<input type="submit" value="Remover" /> <input type="reset" value="Cancelar" />
	</fieldset>
</form>
<?php
require_once('footer.php');
?>

