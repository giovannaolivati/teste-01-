<?php
/* ============================================================
   cadastrar.php | CREATE (Criar) do CRUD
   ============================================================
   Recebe os dados do formulário da página cadastro.php e grava
   um bruxo novo na tabela "bruxos" com INSERT INTO.

   Este arquivo não mostra nada na tela: ele salva os dados e
   manda o navegador de volta para a página de cadastro.
   ============================================================ */

// Puxa a conexão com o banco (cria a variável $conexao)
require "conexao.php";

// isset() confere se as informações chegaram pelo formulário.
// Se alguém abrir este arquivo direto no navegador, os dados não existem,
// então mandamos a pessoa de volta para a página de cadastro.
if (!isset($_POST["nome"], $_POST["email"], $_POST["idade"], $_POST["casa"], $_POST["animal"])) {
    header("Location: ../pages/cadastro.php");  // header Location = redireciona para outra página
    exit;                                       // exit = para o PHP aqui
}

// Pega os dados enviados pelo formulário (método POST).
// trim() tira os espaços sobrando no começo e no fim do texto.
$nome = trim($_POST["nome"]);
$email = trim($_POST["email"]);
$idade = (int) $_POST["idade"];   // (int) transforma o valor em número inteiro
$casa = $_POST["casa"];
$animal = $_POST["animal"];

// Validação no servidor.
// O JavaScript já confere os campos, mas o PHP confere de novo, porque
// o JavaScript pode ser desligado no navegador.
// || significa "ou": se QUALQUER uma dessas condições for verdadeira, tem erro.
// strpos($email, "@") === false significa "não tem @ no e-mail".
if ($nome == "" || $email == "" || strpos($email, "@") === false || $idade < 11 || $idade > 17) {
    header("Location: ../pages/cadastro.php?msg=erro");
    exit;
}

// Prepared Statement (declaração preparada):
// os ? são "espaços reservados". O comando SQL e os dados vão separados
// para o banco, assim ninguém consegue fazer SQL Injection pelo formulário.
$stmt = $conexao->prepare("INSERT INTO bruxos (nome, email, idade, casa, animal) VALUES (?, ?, ?, ?, ?)");

// Executa o comando trocando cada ? pelo valor da lista, na mesma ordem.
$stmt->execute([$nome, $email, $idade, $casa, $animal]);

// Volta para a página de cadastro com um aviso de sucesso na URL (?msg=cadastrado)
header("Location: ../pages/cadastro.php?msg=cadastrado");
exit;
?>
