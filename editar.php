<?php
/* ============================================================
   editar.php | PÁGINA DE EDIÇÃO (UPDATE do CRUD)
   ============================================================
   Abre pelo link "Editar" da tabela, com o id no endereço
   (exemplo: editar.php?id=3).

   1. Lê o id que veio na URL ($_GET)
   2. Busca esse bruxo no banco (SELECT ... WHERE id = ?)
   3. Mostra o formulário já preenchido com os dados dele
   4. Ao salvar, envia para php/atualizar.php, que faz o UPDATE
   ============================================================ */

// Puxa a conexão com o banco
require "../php/conexao.php";

// Se a página foi aberta sem id no endereço, volta para a lista
if (!isset($_GET["id"])) {
    header("Location: cadastro.php");
    exit;
}

// (int) garante que o id seja um número
$id = (int) $_GET["id"];

// Busca o bruxo com esse id usando Prepared Statement (o id vem do usuário pela URL)
$stmt = $conexao->prepare("SELECT * FROM bruxos WHERE id = ?");
$stmt->execute([$id]);

// fetch() pega UMA linha só (o bruxo encontrado).
// Se não achar nenhum, ele devolve false.
$bruxo = $stmt->fetch();

// Se não existe bruxo com esse id (ex.: foi excluído), volta para a lista
if (!$bruxo) {
    header("Location: cadastro.php");
    exit;
}

// Aviso de erro, caso o atualizar.php tenha mandado de volta com ?msg=erro
$erro = isset($_GET["msg"]) && $_GET["msg"] == "erro";

// Listas para montar as opções do formulário
$casas = ["Grifinória", "Sonserina", "Corvinal", "Lufa-Lufa"];
$animais = ["Coruja", "Gato", "Sapo"];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal de Hogwarts | Editar matrícula</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Crimson+Text:wght@400;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../css/style.css">

    <!-- Usa o mesmo cadastro.js da página de cadastro, para validar o formulário -->
    <script src="../js/script.js" defer></script>
    <script src="../js/cadastro.js" defer></script>
</head>
<body>

    <!-- ==================== CABEÇALHO ==================== -->
    <header class="cabecalho">
        <div class="container cabecalho-conteudo">
            <a href="../index.html" class="logo">
                <img src="../img/brasao-hogwarts.svg" alt="Brasão de Hogwarts">
                Hogwarts
            </a>

            <button type="button" id="btn-menu" class="botao-menu">☰ Menu</button>

            <nav class="navegacao">
                <ul id="menu" class="menu">
                    <li><a href="../index.html">Início</a></li>
                    <!-- Continua marcando "Cadastro" como ativo, porque a edição faz parte dele -->
                    <li><a href="cadastro.php" class="ativo">Cadastro</a></li>
                    <li><a href="chapeu-seletor.html">Chapéu Seletor</a></li>
                    <li><a href="saloes.html">Salões Comunais</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">

        <section class="secao">
            <p class="subtitulo">Livro de matrículas</p>
            <h1 class="titulo-pagina">Editar matrícula</h1>
            <!-- Mostra o nome do bruxo que está sendo editado -->
            <p class="descricao-pagina">Alterando os dados de <strong><?php echo htmlspecialchars($bruxo["nome"]); ?></strong>.</p>
        </section>

        <!-- Aviso de erro vindo do PHP (só aparece se $erro for verdadeiro) -->
        <?php if ($erro) { ?>
            <p class="mensagem mensagem-erro">Não foi possível salvar. Confira se todos os campos estão preenchidos corretamente.</p>
        <?php } ?>

        <div class="cadastro-layout">
            <section class="painel">
                <h2>Dados do bruxo</h2>

                <!-- Mensagem de erro do JavaScript (começa escondida) -->
                <p id="mensagem-erro" class="mensagem mensagem-erro escondido"></p>

                <!-- Mesmo id do formulário de cadastro (form-cadastro),
                     assim o cadastro.js valida este formulário também.
                     Envia para o atualizar.php, que faz o UPDATE. -->
                <form id="form-cadastro" class="formulario" action="../php/atualizar.php" method="post" novalidate>

                    <!-- Campo escondido com o id: o usuário não vê,
                         mas ele é enviado para o PHP saber QUAL bruxo atualizar -->
                    <input type="hidden" name="id" value="<?php echo $bruxo["id"]; ?>">

                    <!-- Os campos já vêm preenchidos: o PHP escreve os dados
                         do banco dentro do atributo value de cada input -->
                    <div class="campo">
                        <label for="nome">Nome completo</label>
                        <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($bruxo["nome"]); ?>" required>
                    </div>

                    <div class="campo">
                        <label for="email">E-mail</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($bruxo["email"]); ?>" required>
                    </div>

                    <div class="campo">
                        <label for="idade">Idade</label>
                        <input type="number" id="idade" name="idade" min="11" max="17" value="<?php echo $bruxo["idade"]; ?>" required>
                    </div>

                    <div class="campo">
                        <label for="casa">Casa</label>
                        <select id="casa" name="casa">
                            <option value="">Ainda não fui selecionado</option>
                            <!-- Marca como "selected" a casa que está salva no banco -->
                            <?php foreach ($casas as $casa) { ?>
                                <option value="<?php echo $casa; ?>" <?php if ($casa == $bruxo["casa"]) { echo "selected"; } ?>><?php echo $casa; ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <fieldset class="campo">
                        <legend>Animal de estimação</legend>
                        <!-- Marca como "checked" o animal que está salvo no banco -->
                        <?php foreach ($animais as $animal) { ?>
                            <label class="opcao">
                                <input type="radio" name="animal" value="<?php echo $animal; ?>" <?php if ($animal == $bruxo["animal"]) { echo "checked"; } ?>>
                                <?php echo $animal; ?>
                            </label>
                        <?php } ?>
                    </fieldset>

                    <div class="botoes-formulario">
                        <button type="submit" class="botao">Salvar alterações</button>
                        <!-- Link com cara de botão que volta para a lista sem salvar nada -->
                        <a href="cadastro.php" class="botao botao-secundario">Cancelar</a>
                    </div>

                </form>
            </section>
        </div>

    </main>

    <!-- ==================== RODAPÉ ==================== -->
    <footer class="rodape">
        <p>Portal de Hogwarts — Projeto de DWS | UNASP-HT 2026</p>
        <p>Harry Potter e os nomes relacionados são marcas da Warner Bros. Site de fã, sem fins lucrativos.</p>
    </footer>

</body>
</html>
