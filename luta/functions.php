<?php
/**
 * Funções genéricas
 */

require_once('connection.php');

// Ações a serem tomadas nas páginas
define('UPDATE', 0); // Executa uma query UPDATE
define('INSERT', 1); // Executa uma query INSERT
define('EDIT', 2);   // Edita um registro (com form)
define('DELETE', 3); // Executa uma query DELETE
define('SELECT', 4); // Seleciona os registros e um form para adicionar items

/**
 * Define a ação a ser executada de forma genérica (via variáveis GET e POST)
 */
function getAction() {
	$action = SELECT; // Padrão
	if (post('action') == 'new')
		$action = INSERT;
	else if (post('action') == 'edit')
		$action = UPDATE;
	else if (isset($_GET['action'])) {
		if ($_GET['action'] == 'delete')
			$action = DELETE;
		else if ($_GET['action'] == 'edit')
			$action = EDIT;
	}
	return $action;
}

function post($name) {
	if (isset($_POST[$name]))
		return $_POST[$name];
	return '';
}

/**
 * Executa uma query com mysql_query e faz o tratamento de erros, além de guardar num log
 */
function sqlQuery($query){
	$ret = @mysql_query($query);
	file_put_contents('sql.log', strftime('%c').': '.$query . "\n\n", FILE_APPEND);
	if ($ret === false) {
		switch (mysql_errno()) {
			case 1062:
				$error = 'Entrada de valor duplicada!<br />Por favor, verifique os dados e tente novamente.';
				break;
			case 1452:
				$fk_fails = Array(
					'fk_loc' => 'Por favor, defina um local válido!',
					'fk_promotora' => 'Por favor, informe uma promotora',
					'fkTels' => 'Evento não encontrado!',
					'fk_TelsPat' => 'Patrocinadora não encontrada!',
					'fk_ContPat' => 'Patrocinadora não encontrada!',
					'fk_patro' => 'Por favor, defina uma empresa para este patrocínio',
					'fk_evento' => 'Evento não encontrado!',
					'fk_infospat' => 'Contrato não encontrado!',
					'fkCat' => 'Categoria inválida!',
					'fk_lut1' => 'Lutador 1 inválido!',
					'fk_lut2' => 'Lutador 2 inválido!',
					'fk_lutaevento' => 'Evento não encontrado!',
					'fkInfosLuta' => 'Luta não encontrada!',
					'fkLut' => 'Lutador não encontrado!',
					'fkEmi' => 'Por favor, informe uma emissora válida!',
					'fkLuta' => 'Por favor, informe uma luta válida!',
					'fkITrans' => 'Por favor, informe uma transmissão válida!',
					'fkReptTrans' => 'Por favor, informe uma transmissão válida!',
					'fkCanal' => 'Por favor, informe uma emissora válida!',
					'fkEve' => 'Por favor, informe um evento válido!',
					'fkWeb' => 'Por favor, informe um website válido!',
					'fkCar' => 'Cliente não encontrado!',
					'fkTcli' => 'Cliente não encontrado!',
					'fkComprad' => 'Cliente não encontrado! Verifique o CPF e tente novamente.',
					'fkIngreEve' => 'Evento inválido!',
					'fkIngreCompra' => 'Compra inválida!',
					'fkTelsPV' => 'Ponto de venda inválido!',
					'fkVendeEve' => 'Evento inválido!',
					'fkPon' => 'Ponto de venda inválido!'
				);
				$mysql_error = mysql_error();
				$error = false;
				// Pega cada mensagem e chave (chave = nome da foreign key) do array $fk_fails
				foreach ($fk_fails as $key => $msg) {
					// Verifica se a chave $key está na mensagem $mysql_error (o MySQL insere o nome da chave no erro)
					if (strstr($mysql_error, $key)) {
						$error = $msg;
						break;
					}
				}
				if ($error) {
					$error = '<strong>Erro!</strong><br />'.$error;
					break;
				}
			default:
				$error = '<div><em>SQL Error: </em> '.mysql_errno().': '.mysql_error().'</div>';
		}
		die($error.'<br /><a href="javascript:history.go(-1)">Voltar à página anterior</a>');
	}
	return $ret;
}

