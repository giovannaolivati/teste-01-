<?php
/* ============================================================
   atualizar.php | UPDATE (Atualizar) do CRUD
   ============================================================
   Recebe os dados do formulário da página editar.php e altera o
   bruxo no banco com UPDATE ... SET.
   ============================================================ */

// Puxa a conexão com o banco
require "conexao.php";

// Confere se todos os dados chegaram pelo formulário (inclusive o id,
// que vem de um campo escondido, type="hidden", no editar.php)
if (!isset($_POST["id"], $_POST["nome"], $_POST["email"], $_POST["idade"], $_POST["casa"], $_POST["animal"])) {
    header("Location: ../pages/cadastro.php");
    exit;
}

// Pega os dados do formulário
$id = (int) $_POST["id"];
$nome = trim($_POST["nome"]);
$email = trim($_POST["email"]);
$idade = (int) $_POST["idade"];
$casa = $_POST["casa"];
$animal = $_POST["animal"];

// Validação no servidor (as mesmas regras do cadastrar.php).
// Se tiver erro, volta para a página de edição do mesmo bruxo.
if ($nome == "" || $email == "" || strpos($email, "@") === false || $idade < 11 || $idade > 17) {
    header("Location: ../pages/editar.php?id=" . $id . "&msg=erro");  // o ponto (.) junta textos no PHP
    exit;
}

// UPDATE com Prepared Statement.
// SET diz quais colunas mudam e WHERE id = ? diz QUAL bruxo muda.
// Sem o WHERE, TODOS os bruxos da tabela seriam alterados!
$stmt = $conexao->prepare("UPDATE bruxos SET nome = ?, email = ?, idade = ?, casa = ?, animal = ? WHERE id = ?");
$stmt->execute([$nome, $email, $idade, $casa, $animal, $id]);

// Volta para a lista com aviso de sucesso
header("Location: ../pages/cadastro.php?msg=atualizado");
exit;
?>
