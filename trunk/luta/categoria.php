<?php
/**
 * Script responsável por cadastrar o(s) lutador(es)
 */
require_once('check_admin.php');
require_once('functions.php');
require_once('header.php');

$novo = !isset($_GET['tipo']);
$tipo = post('tipo');
$action = getAction();
switch ($action) {
	case INSERT:
	case UPDATE:
		$faixaDePeso = post('faixaDePeso');
		if ($action == INSERT){
			$sql = "insert into Categoria(tipo, faixaDePeso)
			values('$tipo','$faixaDePeso')";
		}else{
			$oldtipo = post('oldtipo');
			$sql = "
				update Categoria
				set tipo = '$tipo',
					faixaDePeso = '$faixaDePeso'
				where tipo = '$oldtipo'";
		}
		$ret = sqlQuery($sql);
		echo '<div><strong>Registro salvo!</strong></div>';
		break;
	case EDIT:
		$tipo = $_GET['tipo'];
		$sql = "select tipo, faixaDePeso from Categoria where tipo = '$tipo'";
		$rs = sqlQuery($sql);
		if (($d = mysql_fetch_object($rs)) !== false) {
			$faixaDePeso = $d->faixaDePeso;
		} else {
			die('Categoria não encontrada!');
		}
		break;
	case DELETE:
		$tipo = $_GET['tipo'];
		$sql = "delete from Categoria where tipo = '$tipo'";
		sqlQuery($sql);
		echo '<div><strong>Registro excluído!</strong></div>';
		// Não dá um "break" pra entrar no "default"
		$tipo = '';
	default:
		$faixaDePeso = '';
}

// Recupera todos as categorias
?>
<h2>Categorias cadastradas</h2>
<table>
<tr><th>Tipo</th><th>Faixa de Peso</th><th>Ação</th></tr>
<?php
$sql = "SELECT tipo, faixaDePeso FROM Categoria ";
$rs = sqlQuery($sql);
while (($d = mysql_fetch_object($rs)) !== false){ // pega cada registro
	echo '<tr><td>',$d->tipo,'</td><td>',$d->faixaDePeso,'</td><td><a href="./categoria.php?tipo=',$d->tipo,'&action=edit">Editar</a> <a href="./categoria.php?tipo=',$d->tipo,'&action=delete">Excluir</a></td></tr>';
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
		<input type="hidden" name="oldtipo" value="<?php echo $tipo;?>" />
		Tipo: <input type="text" name="tipo" value="<?php echo $tipo;?>" /><br />
		Faixa de Peso: <input type="text" name="faixaDePeso" value="<?php echo $faixaDePeso;?>" /><br />
		<input type="submit" value="Salvar" /> <input type="reset" value="Cancelar" />
	</fieldset>

<?php
require_once('footer.php');
?>
