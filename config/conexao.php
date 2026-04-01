<?php

// config/conexao.php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');  // Digite o nome do usuário do banco de dados
define('DB_PASS', 'root'); // Digite a senha do banco de dados
define('DB_NAME', 'nomedobanco'); // Alterar para o nome do seu banco de dados!

// Conexão com MySQLi 
$conexao = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Verifica a conexão
if ($conexao->connect_error) {
    die("Erro na conexão: " . $conexao->connect_error);
}

// Define o charset para utf8
$conexao->set_charset("utf8");

?>
