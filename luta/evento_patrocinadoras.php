<?php
/**
 * Script responsável por cadastrar as lutas de um evento
 */
require_once('check_admin.php');
require_once('functions.php');
require_once('header.php');

$nro = $_GET['nro'];
$nroContrato = post('nroContrato');
$novo = !isset($_GET['nroContrato']);
$action = getAction();
switch ($action) {
	case INSERT:
	case UPDATE:
		$nroContrato = post('nroContrato');
		$nro = post('nro');
		$cnpjPat = post('cnpjPat');
		$preco = post('preco');
		$termos = post('termos');
		if (is_numeric($preco)) $sql_preco = $preco;
		else $sql_preco = 'null';
		if ($action == INSERT) {
			//Insere novo patrocinio
			$sql1 = "insert into Patrocinio(numEvento, cnpjPat)
					values('$nro', '$cnpjPat')";
			//Insere informações deste novo patrocínio
			$sql2 = "insert into InfosPatrocinio(nroContrato,preco,termos)
					values((select max(nroContrato) from Patrocinio), $sql_preco, '$termos')";
		} else {
			//Atualiza dados de um patrocínio já existente
			$sql1 = "
				update Patrocinio
				set cnpjPat = '$cnpjPat'
				where nroContrato = '$nroContrato'";
			//Atualiza dados das informações de um patrocínio já existente
			$sql2 = "
				update InfosPatrocinio
				set preco = $sql_preco,
					termos = '$termos'
				where nroContrato = '$nroContrato'";
		}
		sqlQuery($sql1);
		sqlQuery($sql2);
		echo '<div><strong>Registro salvo!</strong></div>';
		break;
	case EDIT:
		$nroContrato = $_GET['nroContrato'];
		//	Seleciona para editar os dados e informações de um patrocínio já existente
		$sql = "
			select p.cnpjPat, i.preco, i.termos, e.nome
			from Patrocinio p
				inner join InfosPatrocinio i on p.nroContrato = i.nroContrato
				inner join EmpresaPatrocinadora e on p.cnpjPat = e.CNPJ
			where p.nroContrato = $nroContrato";
		$rs = sqlQuery($sql);
		if (($d = mysql_fetch_object($rs)) !== false){
			$cnpjPat = $d->cnpjPat;
			$nomePat = $d->nome;
			$preco = $d->preco;
			$termos = $d->termos;
		} else {
			die('Patrocínio não encontrada!');
		}
		mysql_free_result($rs);
		break;
	case DELETE:
		$nroContrato = $_GET['nroContrato'];
		//Remove um patrocínio
		$sql = "delete from Patrocinio where nroContrato = '$nroContrato'";
		sqlQuery($sql);
		echo '<div><strong>Registro excluído!</strong></div>';
		// Não dá um "break" pra entrar no "default"
		$nroContrato = '';
	default:
		$nroContrato = '';
		$cnpjPat = '';
		$preco = '';
		$termos = '';
		$nomePat = '';
}

// Recupera todos os registros
?>
<div><a href="./evento.php?nro=<?php echo $nro;?>&action=edit">Voltar para o evento</a></div>
<h2>Patrocínios cadastradas</h2>
<table>
<tr><th>Patrocinador</th><th>Contrato</th><th>Ação</th></tr>
<?php
//Seleciona para exibir os dados de todos patrocínios e patrocinadoras relacionados
$rs = sqlQuery("
	select e.CNPJ, e.nome, p.nroContrato
	from Patrocinio p
		inner join EmpresaPatrocinadora e on p.cnpjPat = e.CNPJ
	where $nro = p.numEvento
	order by p.nroContrato");
while (($d = mysql_fetch_object($rs)) !== false) {
	echo "<tr><td><a href=\"./patrocinadora.php?CNPJ=$d->CNPJ&action=edit\">$d->nome</a></td><td>$d->nroContrato</td><td><a href=\"./evento_patrocinadoras.php?action=edit&nroContrato=$d->nroContrato&nro=$nro\">Editar</a> <a href=\"./evento_patrocinadoras.php?action=delete&nroContrato=$d->nroContrato&nro=$nro\">Excluir</a></td></tr>";
}
?>
</table>
<h3><?php echo ($novo?'Nova patrocinadora':'Editar patrocinadora');?></h3>
<a href="./evento_patrocinadoras.php?nro=<?php echo $nro;?>">Novo</a>
<form method="post" action="">
	<fieldset>
		<legend><?php echo ($novo?'Novo':'Editar');?></legend>
		<input type="hidden" name="action" value="<?php echo ($novo?'new':'edit');?>" />
		<input type="hidden" name="nro" value="<?php echo $nro;?>" />
		<input type="hidden" name="nroContrato" value="<?php echo $nroContrato;?>" />
		Patrocinador:
		<?php
		if (!$novo) {
			echo "<a href=\"./patrocinadora.php?CNPJ=$cnpjPat&action=edit\">$nomePat</a>";
			echo '<input type="hidden" name="cnpjPat" value="',$cnpjPat,'" />';
		} else {
			//Seleciona dados de empresas patrocinadoras que não possuem patrocínios para aparecer como “adicionável”	
			$rs = sqlQuery("
				select CNPJ, nome from EmpresaPatrocinadora
				where CNPJ not in (select cnpjPat from Patrocinio where numEvento='$nro')
			");
			echo '<select name="cnpjPat">';
			while (($d = mysql_fetch_object($rs)) !== false) {
				echo "<option value=\"$d->CNPJ\">$d->nome ($d->CNPJ)</option>";
			}
			echo '</select>';
			mysql_free_result($rs);
		}
		?><br />
		Preço: <input type="text" name="preco" value="<?php echo $preco;?>" maxlength="10" /><br />
		Termos: <input type="text" name="termos" value="<?php echo $termos;?>" maxlength="100" /><br />
		<input type="submit" value="Salvar" /> <input type="reset" value="Cancelar" />
	</fieldset>
</form>
<?php
require_once('footer.php');
?>

