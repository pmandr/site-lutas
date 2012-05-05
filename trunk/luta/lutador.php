<?php
/**
 * Script responsável por cadastrar o(s) lutador(es)
 */
require_once('check_admin.php');
require_once('functions.php');
require_once('header.php');

$novo = !isset($_GET['cpf']);
$cpf = post('cpf');
// Define a ação a ser tomada
$action = getAction();
switch ($action) {
	case INSERT:
	case UPDATE:
		$nome = post('nome');
		$nomeFantasia = post('nomeFantasia');
		$peso = post('peso');
		$altura = post('altura');
		$nacionalidade = post('nacionalidade');
		$vitorias = post('vitorias');
		$empates = post('empates');
		$derrotas = post('derrotas');
		$categoria = post('categoria');
		$habilidades = explode("\n", post('habilidades'));
		if ($categoria == '') $sql_categ = 'null';
		else $sql_categ = "'$categoria'";
		if ($action == INSERT){
			$sql = "insert into Lutador(CPF, nome, nomeFantasia, peso, altura, nacionalidade, vitorias, empates, derrotas, categoria)
			values('$cpf', '$nome','$nomeFantasia','$peso','$altura','$nacionalidade','$vitorias','$empates','$derrotas', $sql_categ)";
		}else{
			$oldcpf = post('oldcpf');
			//Remove habilidades do lutador midificado
			sqlQuery("delete from HabilidadesLut where lutador = '$oldcpf'");
			//Atualiza os dados de um lutador já existente
			$sql = "update Lutador
			set CPF = '$cpf',
				nome = '$nome',
				nomeFantasia = '$nomeFantasia',
				peso = '$peso',
				altura = '$altura',
				nacionalidade = '$nacionalidade',
				vitorias = '$vitorias',
				empates = '$empates',
				derrotas = '$derrotas',
				categoria = $sql_categ
			where CPF = '$oldcpf'";
		}
		$ret = @sqlQuery($sql);
		if ($ret === false) {
			die('Erro!');
		}
		foreach ($habilidades as $h) {
			if (trim($h))
				sqlQuery("insert into HabilidadesLut(lutador, habilidade) values('$cpf', '$h')");
		}
		$habilidades = implode("\n", $habilidades);
		echo '<div><strong>Registro salvo!</strong></div>';
		break;
	case EDIT:
		$cpf = $_GET['cpf'];
		//Seleciona as informações de um lutador para edita-las
		$sql = "select l.CPF, l.nome, l.nomeFantasia, l.peso, l.altura, l.nacionalidade, l.vitorias, l.empates, l.derrotas, l.categoria, h.habilidade
		from Lutador l left join HabilidadesLut h on l.CPF = h.lutador
		where cpf='$cpf'";
		$habilidades = '';
		$rs = sqlQuery($sql);
		if (($d = mysql_fetch_object($rs)) !== false) {
			$nome = $d->nome;
			$nomeFantasia = $d->nomeFantasia;
			$peso = $d->peso;
			$altura = $d->altura;
			$nacionalidade = $d->nacionalidade;
			$vitorias = $d->vitorias;
			$empates = $d->empates;
			$derrotas = $d->derrotas;
			$categoria = $d->categoria;
			$habilidades = ($d->habilidade == null ? '' : $d->habilidade);
			while(($d = mysql_fetch_object($rs)) !== false)
				$habilidades .= "\n".$d->habilidade;
			mysql_free_result($rs);
		} else {
			die('CPF não encontrado!');
		}
		break;
	case DELETE:
		$cpf = $_GET['cpf'];
		$sql = "delete from Lutador where cpf = '$cpf'";
		sqlQuery($sql);
		echo '<div><strong>Registro excluído!</strong></div>';
		// Não dá um "break" pra entrar no "default"
		$cpf = '';
	default:
		$nome = '';
		$nomeFantasia = '';
		$peso = '';
		$altura = '';
		$nacionalidade = '';
		$vitorias = '';
		$empates = '';
		$derrotas = '';
		$categoria = '';
		$habilidades = '';
}

// Recupera todos os lutadores
?>
<h2>Lutadores cadastrados</h2>
<table>
<tr><th>CPF</th><th>Nome</th><th>Nome fantasia</th><th>Ação</th></tr>
<?php
//Lista todos os lutadores
$sql = "select CPF, nome, nomeFantasia from Lutador";
$rs = sqlQuery($sql) or die('sql error');
while (($d = mysql_fetch_object($rs)) !== false){ // pega cada registro
	echo '<tr><td>',$d->CPF,'</td><td>',$d->nome,'<td>',$d->nomeFantasia,'</td></td><td><a href="./lutador.php?cpf=',$d->CPF,'&action=edit">Editar</a> <a href="./lutador.php?cpf=',$d->CPF,'&action=delete">Excluir</a></td></tr>';
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
		<input type="hidden" name="oldcpf" value="<?php echo $cpf;?>" />
		CPF: <input type="text" name="cpf" value="<?php echo $cpf;?>" /><br />
		Nome: <input type="text" name="nome" value="<?php echo $nome;?>" /><br />
		Nome Fantasia: <input type="text" name="nomeFantasia" value="<?php echo $nomeFantasia;?>" /><br />
		Peso: <input type="text" name="peso" value="<?php echo $peso;?>" /><br />
		Altura: <input type="text" name="altura" value="<?php echo $altura;?>" /><br />
		Nacionalidade: <input type="text" name="nacionalidade" value="<?php echo $nacionalidade;?>" /><br />
		Vitórias: <input type="text" name="vitorias" value="<?php echo $vitorias;?>" /><br />
		Empates: <input type="text" name="empates" value="<?php echo $empates;?>" /><br />
		Derrotas: <input type="text" name="derrotas" value="<?php echo $derrotas;?>" /><br />
		<!--
		classificacaoCat: <input type="text" name="classificacaoCat" value="<?php echo post('classificacaoCat');?>" /><br />
		categoria: <input type="text" name="categoria" value="<?php echo post('categoria');?>" /><br />
		-->
		Categoria: <select name="categoria">
		<option value="">---</option>
		<?php
		//Seleciona categorias para poderem ser escolhidas no formulário
		$rs = sqlQuery('SELECT tipo FROM Categoria');
		while (($d = mysql_fetch_object($rs)) !== false){
			$selected = ($d->tipo == $categoria ? ' selected="selected"' : '');
			echo '<option value="',$d->tipo,'"',$selected,'>',$d->tipo,'</option>';
		}
		mysql_free_result($rs);
		?>
		</select><br />
		Habilidades (uma por linha):<br /><textarea name="habilidades" rows="5" cols="80"><?php echo $habilidades;?></textarea><br />
		<input type="submit" value="Salvar" /> <input type="reset" value="Cancelar" />
	</fieldset>

<?php
require_once('footer.php');
?>
