<?php
/**
 * Script responsável por cadastrar o(s) lutador(es)
 */
require_once('check_admin.php');
require_once('functions.php');
require_once('header.php');

$novo = !isset($_GET['nomeLoc']);
$nomeLoc = post('nomeLoc');
$action = getAction();
switch ($action) {
	case INSERT:
	case UPDATE:
		$nomeLoc = post('nomeLoc');
		$cidade = post('cidade');
		$estado = post('estado');
		$endereco = post('endereco');
		$capacidade = post('capacidade');
		if ($action == INSERT){
			//Insere um local novo
			$sql = "insert into Local(nomeLoc, cidade, estado, endereco, capacidade)
				values('$nomeLoc','$cidade','$estado','$endereco','$capacidade')";
		}else{
			$oldnomeLoc = post('oldnomeLoc');
			//Atualiza valores de um local já existente
			$sql = "
				update Local
				set nomeLoc = '$nomeLoc',
					cidade = '$cidade',
					estado = '$estado',
					endereco = '$endereco',
					capacidade = '$capacidade'
				where nomeLoc = '$oldnomeLoc'";
		}
		$ret = sqlQuery($sql);
		echo '<div><strong>Registro salvo!</strong></div>';
		break;
	case EDIT:
		$nomeLoc = $_GET['nomeLoc'];
		//seleciona os dados de um local para esrem editados
		$sql = "select nomeLoc, cidade, estado, endereco, capacidade from Local where nomeLoc = '$nomeLoc'";
		$rs = sqlQuery($sql);
		if (($d = mysql_fetch_object($rs)) !== false) {
			$cidade = $d->cidade;
			$estado = $d->estado;
			$endereco = $d->endereco;
			$capacidade = $d->capacidade;
		} else {
			die('Local não encontrado!');
		}
		break;
	case DELETE:
		$nomeLoc = $_GET['nomeLoc'];
		//remove um local
		$sql = "delete from Local where nomeLoc = '$nomeLoc'";
		sqlQuery($sql);
		echo '<div><strong>Registro excluído!</strong></div>';
		// Não dá um "break" pra entrar no "default"
		$nomeLoc = '';
	default:
		$cidade = '';
		$estado = '';
		$endereco = '';
		$capacidade = '';
}

// Recupera todos os registros
?>
<h2>Locais cadastrados</h2>
<table>
<tr><th>Nome</th><th>Cidade</th><th>Ação</th></tr>
<?php
//Seleciona todos os locais cadastrados para exibir
$sql = "SELECT nomeLoc, cidade, estado FROM Local ";
$rs = sqlQuery($sql);
while (($d = mysql_fetch_object($rs)) !== false){ // pega cada registro
	echo '<tr><td>',$d->nomeLoc,'</td><td>',$d->cidade.'/'.$d->estado,'</td><td><a href="./local.php?nomeLoc=',urlencode($d->nomeLoc),'&action=edit">Editar</a> <a href="./local.php?nomeLoc=',urlencode($d->nomeLoc),'&action=delete">Excluir</a></td></tr>';
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
		<input type="hidden" name="oldnomeLoc" value="<?php echo $nomeLoc;?>" />
		Nome: <input type="text" name="nomeLoc" value="<?php echo $nomeLoc;?>" size="20" /><br />
		Cidade: <input type="text" name="cidade" value="<?php echo $cidade;?>" size="10"/><br />
		Estado: <input type="text" name="estado" value="<?php echo $estado;?>" size="2" /><br />
		Endereço: <input type="text" name="endereco" value="<?php echo $endereco;?>" size="20" /><br />
		Capacidade: <input type="text" name="capacidade" value="<?php echo $capacidade;?>" /><br />
		<input type="submit" value="Salvar" /> <input type="reset" value="Cancelar" />
	</fieldset>

<?php
require_once('footer.php');
?>
