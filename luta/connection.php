<?php
/**
 * Realiza a conexão com o banco de dados
 */

@mysql_connect('localhost', 'root', '') or die('Erro na conexão!');
@mysql_select_db('siteluta') or die('Erro ao abrir banco de dados!');

