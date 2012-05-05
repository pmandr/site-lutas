<?php
/**
 * Script responsável por cadastrar o(s) lutador(es)
 */
require_once('check_admin.php');
require_once('functions.php');
require_once('header.php');

$novo = !isset($_GET['nro']);
$nro = post('nro');
$action = getAction();
switch ($action) {
	case INSERT:
	case UPDATE:
		$nome = post('nome');
		$data = post('data');
		$local = post('local');
		$horario = post('horario');
		$cnpjPromotora = post('cnpjPromotora');
		$lucro = post('lucro');
		$custo = post('custo');
		$responsavel = post('responsavel');
		$qtdIngressosOferecidos = post('qtdIngressosOferecidos');
		if ($action == INSERT){
			//Insere dados de um novo evento
			$sql = "
				insert into EventoDeLuta(nome, data, local, horario, cnpjPromotora, lucro, custo, responsavel, qtdIngressosOferecidos)
				values('$nome', '$data', '$local', '$horario', '$cnpjPromotora', '$lucro', '$custo', '$responsavel', '$qtdIngressosOferecidos')";
		}else{
			//atualiza valores de um evento já existente
			$sql = "
			update EventoDeLuta
			set nome = '$nome', 
				data = '$data', 
				local = '$local', 
				horario = '$horario', 
				cnpjPromotora = '$cnpjPromotora', 
				lucro = '$lucro', 
				custo = '$custo', 
				responsavel = '$responsavel', 
				qtdIngressosOferecidos = '$qtdIngressosOferecidos'
			where nro = '$nro'";
		}
		$ret = sqlQuery($sql);
		echo '<div><strong>Registro salvo!</strong></div>';
		break;
	case EDIT:
		$nro = $_GET['nro'];
		//Seleciona para editar dados de um evento existente
		$sql = "
			select e.nome, e.data, e.local, e.horario, e.cnpjPromotora, e.lucro, e.custo, e.responsavel, e.qtdIngressosOferecidos
			from EventoDeLuta e
			where e.nro = '$nro'";
		$rs = sqlQuery($sql);
		if (($d = mysql_fetch_object($rs)) !== false) {
			$nome = $d->nome;
			$data = $d->data;
			$local = $d->local;
			$horario = $d->horario;
			$cnpjPromotora = $d->cnpjPromotora;
			$lucro = $d->lucro;
			$custo = $d->custo;
			$responsavel = $d->responsavel;
			$qtdIngressosOferecidos = $d->qtdIngressosOferecidos;
		} else {
			die('Evento não encontrado!');
		}
		break;
	case DELETE:
		$nro = $_GET['nro'];
		//remove evento
		$sql = "delete from EventoDeLuta where nro = '$nro'";
		sqlQuery($sql);
		echo '<div><strong>Registro excluído!</strong></div>';
		// Não dá um "break" pra entrar no "default"
		$nro = '';
	default:
		$nome = '';
		$data = '';
		$local = '';
		$horario = '';
		$cnpjPromotora = '';
		$lucro = '';
		$custo = '';
		$responsavel = '';
		$qtdIngressosOferecidos = '';
}

// Recupera todos eventos
?>
<h2>Eventos cadastrados</h2>
<table>
<tr><th>#</th><th>Nome</th><th>Data</th><th>Local</th><th>Ação</th></tr>
<?php
//Seleciona dados de eventos  para serem exibidos
$sql = "SELECT nro, nome, data, local FROM EventoDeLuta";
$rs = sqlQuery($sql);
while (($d = mysql_fetch_object($rs)) !== false){ // pega cada registro
	echo '<tr><td>',$d->nro,'</td><td>',$d->nome,'</td><td>',$d->data,'</td><td><a href="./local.php?nomeLoc=',urlencode($d->local),'&action=edit">',$d->local,'</td><td><a href="./evento.php?nro=',$d->nro,'&action=edit">Editar</a> <a href="./evento.php?nro=',$d->nro,'&action=delete">Excluir</a></td></tr>';
}
mysql_free_result($rs);
?>
</table>

<?php
// Formulário para cadastrar ou editar os registros
?>
<h2><?php echo ($novo?'Novo':'Editar');?></h2>
<a href="./evento.php">Novo</a>
<?php
	if (!$novo) {
	?>
	<ul>
		<li><a href="./evento_lutas.php?nro=<?php echo $nro;?>">Lutas deste evento</a></li>
		<li><a href="./evento_patrocinadoras.php?nro=<?php echo $nro;?>">Patrocinadoras deste evento</a></li>
		<li><a href="./evento_ingressos.php?nro=<?php echo $nro;?>">Ingressos para este evento</a></li>
		<li><a href="./evento_vendas.php?nro=<?php echo $nro;?>">Vendas para este evento</a></li>
	</ul>
	<?php
	} //if (!$novo)
	else {
		echo '<div><strong>Por favor, salve o registro para adicionar lutas e patrocinadores!</strong></div>';
	}
?>
<form method="post" action="">
	<fieldset>
		<legend><?php echo ($novo?'Novo':'Editar');?></legend>
		<input type="hidden" name="action" value="<?php echo ($novo?'new':'edit');?>" />
		<input type="hidden" name="nro" value="<?php echo $nro;?>" />
		Nome: <input type="text" name="nome" value="<?php echo $nome;?>" size="40" /><br />
		Data: <input type="text" name="data" value="<?php echo $data;?>" size="10" /><br />
		Local:
		<select name="local">
		<?php
		//Seleciona local do evento para exibir
		$rs = sqlQuery("select nomeLoc from Local order by nomeLoc");
		while(($d = mysql_fetch_object($rs)) !== false) {
			$selected = ($d->nomeLoc == $local ? ' selected="selected"' : '');
			echo "<option value=\"$d->nomeLoc\"$selected>$d->nomeLoc</option>";
		}
		?></select><br />
		Horário: <input type="text" name="horario" value="<?php echo $horario;?>" size="5" /><br />
		Promotora:
		<select name="cnpjPromotora">
		<?php
		$rs = sqlQuery("select CNPJ, nome from OrganizacaoPromotora order by nome, CNPJ");
		while(($d = mysql_fetch_object($rs)) !== false) {
			$selected = ($d->CNPJ == $cnpjPromotora ? ' selected="selected"' : '');
			echo "<option value=\"$d->CNPJ\"$selected>$d->nome ($d->CNPJ)</option>";
		}
		?></select><br />
		Lucro: <input type="text" name="lucro" value="<?php echo $lucro;?>" size="10" /><br />
		Custo: <input type="text" name="custo" value="<?php echo $custo;?>" size="10" /><br />
		Responsável: <input type="text" name="responsavel" value="<?php echo $responsavel;?>" size="20" /><br />
		Ingressos oferecidos: <input type="text" name="qtdIngressosOferecidos" value="<?php echo $qtdIngressosOferecidos;?>" size="10" /><br />
		<input type="submit" value="Salvar" /> <input type="reset" value="Cancelar" />
	</fieldset>
</form>
<?php
require_once('footer.php');
?>

