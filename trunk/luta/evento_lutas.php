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
			//Insere dados de uma nova luta
			$sql1 = "insert into Luta(Lut1, Lut2, numEvento)
					values('$Lut1', '$Lut2', '$nro')";
			//Insere dados das informações de uma nova luta
			$sql2 = "insert into InfosLuta(luta, horario, juiz, pontL1, pontL2)
					values((select max(codLuta) from Luta), '$horario_luta', '$juiz', $sql_pontL1, $sql_pontL2)";
		} else {
			//Atualiza dados de uma luta já existente
			$sql1 = "
				update Luta
				set Lut1 = '$Lut1',
					Lut2 = '$Lut2'
				where codLuta = '$codLuta'";
			//Atualiza dados de informações de uma luta já existente
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
		//Seleciona dados e informações de uma luta já existente para editar
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
		//remove luta
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

// Recupera todos os registros
?>
<div><a href="./evento.php?nro=<?php echo $nro;?>&action=edit">Voltar para o evento</a></div>
<h2>Lutas cadastradas</h2>
<table>
<tr><th>Lutador 1</th><th>Lutador 2</th><th>Horário</th><th>Ação</th></tr>
<?php
//Seleciona dados e informações de todas as lutas para exibir
$rs = sqlQuery("
	select l.codLuta, l.Lut1, l.Lut2, l1.nomeFantasia as nome1, l2.nomeFantasia as nome2, i.horario
	from Luta l
		inner join InfosLuta i on l.codLuta = i.luta
		inner join Lutador l1 on l.Lut1 = l1.CPF
		inner join Lutador l2 on l.Lut2 = l2.CPF
	where $nro = l.numEvento
	order by i.horario, l.codLuta");
while (($d = mysql_fetch_object($rs)) !== false) {
	echo "<tr><td>$d->nome1</td><td>$d->nome2</td><td>$d->horario</td><td><a href=\"./evento_luta.php?action=edit&codLuta=$d->codLuta&nro=$nro\">Editar</a> <a href=\"./evento_lutas.php?action=delete&codLuta=$d->codLuta&nro=$nro\">Excluir</a></td></tr>";
}
?>
</table>
<h3><?php echo ($novo?'Nova luta':'Editar luta');?></h3>
<a href="./evento_lutas.php?nro=<?php echo $nro;?>">Novo</a>
<form method="post" action="">
	<fieldset>
		<legend><?php echo ($novo?'Novo':'Editar');?></legend>
		<input type="hidden" name="action" value="<?php echo ($novo?'new':'edit');?>" />
		<input type="hidden" name="nro" value="<?php echo $nro;?>" />
		<input type="hidden" name="codLuta" value="<?php echo $codLuta;?>" />
		<?php
		$rs = sqlQuery("select CPF, nomeFantasia as nome from Lutador order by nomeFantasia, CPF");
		$options1 = $options2 = '';
		while (($d = mysql_fetch_object($rs)) !== false) {
			$selected1 = ($d->CPF == $Lut1 ? ' selected="selected"' : '');
			$selected2 = ($d->CPF == $Lut2 ? ' selected="selected"' : '');
			$options1 .= "<option value=\"$d->CPF\"$selected1>$d->nome ($d->CPF)</option>";
			$options2 .= "<option value=\"$d->CPF\"$selected2>$d->nome ($d->CPF)</option>";
		}
		mysql_free_result($rs);
		?>
		Lutador 1: <select name="Lut1"><?php echo $options1;?></select><br />
		Lutador 2: <select name="Lut2"><?php echo $options2;?></select><br />
		Horário: <input type="text" name="horario_luta" value="<?php echo $horario_luta;?>" size="5" /><br />
		Juiz: <input type="text" name="juiz" value="<?php echo $juiz;?>" size="15" /><br />
		Pontuação Lutador 1: <input type="text" name="pontL1" value="<?php echo $pontL1;?>" size="10" /><br />
		Pontuação Lutador 2: <input type="text" name="pontL2" value="<?php echo $pontL2;?>" size="10" /><br />
		<input type="submit" value="Salvar" /> <input type="reset" value="Cancelar" />
	</fieldset>
</form>
<?php
require_once('footer.php');
?>

