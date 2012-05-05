<?php
/**
 * Script responsável por cadastrar o(s) cliente(s)
 */
require_once('check_admin.php');
require_once('functions.php');
require_once('header.php');

$novo = !isset($_GET['CPF']);
$CPF = post('CPF');
$action = getAction();
switch ($action) {
	case INSERT:
	case UPDATE:
		$CPF = post('CPF');
		$email = post('email');
		$nome = post('nome');
		$endereco = post('endereco');
		$telefones = explode("\n", post('telefones'));
		$cartoes = explode("\n", post('cartoes'));
		if ($action == INSERT){
			$sql = "
				insert into Cliente(CPF, email, nome, endereco)
				values('$CPF', '$email', '$nome', '$endereco')";
		}else{
			$oldCPF = post('oldCPF');
			$sql = "
			update Cliente
			set CPF = '$CPF',
				email = '$email',
				nome = '$nome',
				endereco = '$endereco'
			where CPF = '$oldCPF'";
		}
		$ret = sqlQuery($sql);
		sqlQuery("delete from TelsCliente where cliente = '$CPF'");
		foreach ($telefones as $h) {
			if (trim($h))
				sqlQuery("insert into TelsCliente(cliente, telefone)  values('$CPF', '$h')");
		}
		sqlQuery("delete from CartoesCliente where cliente = '$CPF'");
		foreach ($cartoes as $h) {
			if (trim($h))
				sqlQuery("insert into CartoesCliente(cliente, infosCartao) values('$CPF', '$h')");
		}
		$telefones = implode("\n", $telefones);
		$cartoes = implode("\n", $cartoes);
		echo '<div><strong>Registro salvo!</strong></div>';
		break;
	case EDIT:
		$CPF = $_GET['CPF'];
		$sql = "select c.CPF, c.email, c.nome, c.endereco, t.telefone, ca.infosCartao
		from Cliente c
			left join TelsCliente t on c.CPF = t.cliente
			left join CartoesCliente ca on c.CPF = ca.cliente
		where c.CPF = '$CPF'";
		$rs = sqlQuery($sql);
		if (($d = mysql_fetch_object($rs)) !== false) {
			$CPF = $d->CPF;
			$email = $d->email;
			$nome = $d->nome;
			$endereco = $d->endereco;
			$telefones = '';
			$cartoes = '';
			// Recupera os cartões e telefones
			$tel_aux = Array();
			$car_aux = Array();
			do {
				if (!isset($tel_aux[$d->telefone])) {
					$telefones .= $d->telefone . "\n";
					$tel_aux[$d->telefone] = true;
				}
				if (!isset($car_aux[$d->infosCartao])) {
					$cartoes .= $d->infosCartao . "\n";
					$car_aux[$d->infosCartao] = true;
				}
			} while(($d = mysql_fetch_object($rs)) !== false);
			mysql_free_result($rs);
		} else {
			die('Cliente não encontrado!');
		}
		break;
	case DELETE:
		$CPF = $_GET['CPF'];
		$sql = "delete from Cliente where CPF = '$CPF'";
		sqlQuery($sql);
		echo '<div><strong>Registro excluído!</strong></div>';
		// Não dá um "break" pra entrar no "default"
		$CPF = '';
	default:
		$email = '';
		$nome = '';
		$endereco = '';
		$telefones = '';
		$cartoes = '';
}

// Recupera todos os clientes
?>
<h2>Clientes cadastrados</h2>
<table>
<tr><th>CPF</th><th>Nome</th><th>e-mail</th><th>Ação</th></tr>
<?php
$sql = "select CPF, email, nome, endereco from Cliente order by nome,CPF";
$rs = sqlQuery($sql);
while (($d = mysql_fetch_object($rs)) !== false){ // pega cada registro
	echo '<tr><td>',$d->CPF,'</td><td>',$d->nome,'</td><td>',$d->email,'<td><a href="./cliente.php?CPF=',$d->CPF,'&action=edit">Editar</a> <a href="./cliente.php?CPF=',$d->CPF,'&action=delete">Excluir</a></td></tr>';
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
		<input type="hidden" name="oldCPF" value="<?php echo $CPF;?>" />
		CPF: <input type="text" name="CPF" value="<?php echo $CPF;?>" size="10" /><br />
		e-mail: <input type="text" name="email" value="<?php echo $email;?>" size="30" /><br />
		Nome: <input type="text" name="nome" value="<?php echo $nome;?>" size="30" /><br />
		Endereço: <input type="text" name="endereco" value="<?php echo $endereco;?>" size="50" /><br />
		Telefones (um por linha):<br/>
		<textarea name="telefones" rows="5" cols="80"><?php echo $telefones;?></textarea><br />
		Cartões (um por linha):<br />
		<textarea name="cartoes" rows="5" cols="80"><?php echo $cartoes;?></textarea><br />
		<input type="submit" value="Salvar" /> <input type="reset" value="Cancelar" />
	</fieldset>

<?php
require_once('footer.php');
?>
