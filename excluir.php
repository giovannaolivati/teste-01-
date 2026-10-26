<?php
/* ============================================================
   excluir.php | DELETE (Excluir) do CRUD
   ============================================================
   Recebe o id do bruxo (enviado pelo botão "Excluir" da tabela,
   em cadastro.php) e apaga essa linha do banco com DELETE FROM.

   O id chega por POST (e não por um link com GET) porque, como
   vimos na apostila, envios que ALTERAM o banco devem usar POST.
   ============================================================ */

// Puxa a conexão com o banco
require "conexao.php";

// Se o id não veio, volta para o cadastro sem fazer nada
if (!isset($_POST["id"])) {
    header("Location: ../pages/cadastro.php");
    exit;
}

// Transforma o id em número inteiro
$id = (int) $_POST["id"];

// DELETE com Prepared Statement.
// O WHERE id = ? garante que só o bruxo escolhido seja apagado.
$stmt = $conexao->prepare("DELETE FROM bruxos WHERE id = ?");
$stmt->execute([$id]);

// Volta para a lista com aviso de que o bruxo foi excluído
header("Location: ../pages/cadastro.php?msg=excluido");
exit;
?>
