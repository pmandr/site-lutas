<?php
/**
 * Script responsável por cadastrar o(s) lutador(es)
 */
require_once('check_admin.php');
require_once('functions.php');
require_once('header.php');

$novo = !isset($_GET['CNPJ']);
$CNPJ = post('CNPJ');
$action = getAction();
switch ($action) {
	case INSERT:
	case UPDATE:
		$nome = post('nome');
		$website = post('website');
		$telefone = post('telefone');
		$telefones = explode("\n", post('telefones'));
		$contatos = explode("\n", post('contatos'));
		if ($action == INSERT){
			$sql = "insert into EmpresaPatrocinadora(CNPJ, nome, website)
					values('$CNPJ', '$nome', '$website')";
		}else{
			$oldCNPJ = post('oldCNPJ');
			$sql = "
			update EmpresaPatrocinadora
			set CNPJ = '$CNPJ',
				nome = '$nome',
				website = '$website'
			where CNPJ = '$oldCNPJ'";
		}
		$ret = sqlQuery($sql);
		sqlQuery("delete from Tels_Patrocinador where cnpjPat = '$CNPJ'");
		foreach ($telefones as $h) {
			if (trim($h))
				sqlQuery("insert into Tels_Patrocinador(cnpjPat, telefone)  values('$CNPJ', '$h')");
		}
		sqlQuery("delete from Contatos_Patrocinador where cnpjPat = '$CNPJ'");
		foreach ($contatos as $h) {
			if (trim($h))
				sqlQuery("insert into Contatos_Patrocinador(cnpjPat, nomeContato) values('$CNPJ', '$h')");
		}
		$telefones = implode("\n", $telefones);
		$contatos = implode("\n", $contatos);
		echo '<div><strong>Registro salvo!</strong></div>';
		break;
	case EDIT:
		$CNPJ = $_GET['CNPJ'];
		$sql = "select CNPJ, nome, website, telefone from OrganizacaoPromotora where CNPJ = '$CNPJ'";
		$sql = "select c.CNPJ, c.nome, c.website, t.telefone, co.nomeContato
		from EmpresaPatrocinadora c
			left join Tels_Patrocinador t on c.CNPJ = t.cnpjPat
			left join Contatos_Patrocinador co on c.CNPJ = co.cnpjPat
		where c.CNPJ = '$CNPJ'";
		$rs = sqlQuery($sql);
		if (($d = mysql_fetch_object($rs)) !== false) {
			$CNPJ = $d->CNPJ;
			$nome = $d->nome;
			$website = $d->website;
			$telefones = '';
			$contatos = '';
			// Recupera os contatos e telefones
			$tel_aux = Array();
			$con_aux = Array();
			do {
				if (!isset($tel_aux[$d->telefone])) {
					$telefones .= $d->telefone . "\n";
					$tel_aux[$d->telefone] = true;
				}
				if (!isset($con_aux[$d->nomeContato])) {
					$contatos .= $d->nomeContato . "\n";
					$con_aux[$d->nomeContato] = true;
				}
			} while(($d = mysql_fetch_object($rs)) !== false);
			mysql_free_result($rs);
		} else {
			die('Empresa não encontrada!');
		}
		break;
	case DELETE:
		$CNPJ = $_GET['CNPJ'];
		$sql = "delete from OrganizacaoPromotora where CNPJ = '$CNPJ'";
		sqlQuery($sql);
		echo '<div><strong>Registro excluído!</strong></div>';
		// Não dá um "break" pra entrar no "default"
		$CNPJ = '';
	default:
		$nome = '';
		$website = '';
		$telefones = '';
		$contatos = '';
}

// Recupera todos registros
?>
<h2>Empresas patrocinadoras cadastradas</h2>
<table>
<tr><th>Nome</th><th>CNPJ</th><th>Ação</th></tr>
<?php
$sql = "SELECT CNPJ, nome FROM EmpresaPatrocinadora ";
$rs = sqlQuery($sql);
while (($d = mysql_fetch_object($rs)) !== false){ // pega cada registro
	echo '<tr><td>',$d->nome,'</td><td>',$d->CNPJ,'</td><td><a href="./patrocinadora.php?CNPJ=',$d->CNPJ,'&action=edit">Editar</a> <a href="./patrocinadora.php?CNPJ=',$d->CNPJ,'&action=delete">Excluir</a></td></tr>';
}
mysql_free_result($rs);
?>
</table>

<?php
// Formulário para cadastrar ou editar os registros
?>
<h2><?php echo ($novo?'Novo':'Editar');?></h2>
<form method="post" action="">
	<fieldset>
		<legend><?php echo ($novo?'Novo':'Editar');?></legend>
		<input type="hidden" name="action" value="<?php echo ($novo?'new':'edit');?>" />
		<input type="hidden" name="oldCNPJ" value="<?php echo $CNPJ;?>" />
		CNPJ: <input type="text" name="CNPJ" value="<?php echo $CNPJ;?>" /><br />
		Nome: <input type="text" name="nome" value="<?php echo $nome;?>" size="20" /><br />
		Website: <input type="text" name="website" value="<?php echo $website;?>" size="20" /><br />
		Telefones (um por linha):<br/>
		<textarea name="telefones" rows="5" cols="80"><?php echo $telefones;?></textarea><br />
		Contatos (um por linha):<br />
		<textarea name="contatos" rows="5" cols="80"><?php echo $contatos;?></textarea><br />
		<input type="submit" value="Salvar" /> <input type="reset" value="Cancelar" />
	</fieldset>

<?php
require_once('footer.php');
?>
