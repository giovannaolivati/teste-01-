<?php
/* ============================================================
   conexao.php | Conexão com o banco de dados MySQL
   ============================================================
   Este arquivo é chamado (com "require") por todas as páginas e
   arquivos PHP que precisam usar o banco. Assim os dados de acesso
   ficam escritos em um lugar só.

   Usamos o PDO (PHP Data Objects), que é a forma recomendada de
   conectar o PHP ao MySQL, porque ele permite usar Prepared
   Statements (proteção contra SQL Injection).
   ============================================================ */

// Dados de acesso ao MySQL.
// No XAMPP o padrão é: servidor "localhost", usuário "root" e senha vazia.
$servidor = "localhost";
$banco = "hogwarts";
$usuario = "root";
$senha = "";

// try = "tente fazer isto". Se der algum erro lá dentro, o PHP pula para o catch.
try {
    // Cria a conexão.
    // charset=utf8mb4 garante que os acentos cheguem certos do banco para o site.
    $conexao = new PDO("mysql:host=$servidor;dbname=$banco;charset=utf8mb4", $usuario, $senha);

    // Faz o PDO avisar quando um comando SQL der erro, em vez de falhar em silêncio.
    $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $erro) {
    // catch = o que fazer se a conexão falhar.
    // die() mostra a mensagem e para o PHP na hora.
    die("Erro ao conectar com o banco de dados. Confira se o MySQL está ligado no XAMPP e se o banco hogwarts foi criado. Detalhe: " . $erro->getMessage());
}
?>
