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
		$presidente = post('presidente');
		$telefone = post('telefone');
		if ($action == INSERT){
			$sql = "insert into OrganizacaoPromotora(CNPJ, nome, presidente, telefone)
					values('$CNPJ', '$nome', '$presidente', '$telefone')";
		}else{
			$oldCNPJ = post('oldCNPJ');
			$sql = "
			update OrganizacaoPromotora
			set CNPJ = '$CNPJ',
				nome = '$nome',
				presidente = '$presidente',
				telefone = '$telefone'
			where CNPJ = '$oldCNPJ'";
		}
		$ret = sqlQuery($sql);
		echo '<div><strong>Registro salvo!</strong></div>';
		break;
	case EDIT:
		$CNPJ = $_GET['CNPJ'];
		$sql = "select CNPJ, nome, presidente, telefone from OrganizacaoPromotora where CNPJ = '$CNPJ'";
		$rs = sqlQuery($sql);
		if (($d = mysql_fetch_object($rs)) !== false) {
			$CNPJ = $d->CNPJ;
			$nome = $d->nome;
			$presidente = $d->presidente;
			$telefone = $d->telefone;
		} else {
			die('Organização não encontrada!');
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
		$presidente = '';
		$telefone = '';
}

// Recupera todos registros
?>
<h2>Organizações cadastradas</h2>
<table>
<tr><th>Nome</th><th>CNPJ</th><th>Ação</th></tr>
<?php
//seleciona promotoras para lista-las
$sql = "SELECT CNPJ, nome FROM OrganizacaoPromotora ";
$rs = sqlQuery($sql);
while (($d = mysql_fetch_object($rs)) !== false){ // pega cada registro
	echo '<tr><td>',$d->nome,'</td><td>',$d->CNPJ,'</td><td><a href="./organizacao.php?CNPJ=',$d->CNPJ,'&action=edit">Editar</a> <a href="./organizacao.php?CNPJ=',$d->CNPJ,'&action=delete">Excluir</a></td></tr>';
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
		Presidente: <input type="text" name="presidente" value="<?php echo $presidente;?>" size="20" /><br />
		Telefone: <input type="text" name="telefone" value="<?php echo $telefone;?>" /><br />
		<input type="submit" value="Salvar" /> <input type="reset" value="Cancelar" />
	</fieldset>

<?php
require_once('footer.php');
?>
