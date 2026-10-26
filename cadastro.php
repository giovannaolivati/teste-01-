<?php
/* ============================================================
   cadastro.php | PÁGINA DE MATRÍCULA (CRUD)
   ============================================================
   Esta página é .php (e não .html) porque, ANTES de ir para o
   navegador, ela precisa buscar no banco a lista de bruxos.

   Ela junta 3 partes do CRUD:
   - CREATE (criar): formulário que envia para php/cadastrar.php
   - READ (ler):     tabela com os bruxos buscados no banco (aqui embaixo)
   - DELETE (excluir): botão Excluir de cada linha (php/excluir.php)
   O UPDATE (editar) fica na página editar.php.

   Importante: arquivos PHP só funcionam pelo servidor do XAMPP
   (http://localhost/...), e não abrindo o arquivo direto.
   ============================================================ */

// Puxa a conexão com o banco. O "../" volta da pasta pages/ para a raiz.
require "../php/conexao.php";


// ---------- READ: busca todos os bruxos ----------
// query() roda um SELECT simples (sem dados do usuário, então não precisa de prepare).
// ORDER BY id = ordena pelo número do id, do menor para o maior.
$stmt = $conexao->query("SELECT * FROM bruxos ORDER BY id");

// fetchAll() pega todas as linhas do resultado e guarda em uma lista.
$bruxos = $stmt->fetchAll();


// ---------- MENSAGEM DE AVISO ----------
// Depois de cadastrar, editar ou excluir, os arquivos da pasta php/ voltam para
// esta página com ?msg=... no endereço. Aqui transformamos isso em um aviso.
$mensagem = "";       // texto do aviso (vazio = sem aviso)
$tipoMensagem = "";   // classe CSS: mensagem-sucesso (verde) ou mensagem-erro (vermelho)

// $_GET pega informações que vêm no endereço (URL), depois do ?
if (isset($_GET["msg"])) {
    if ($_GET["msg"] == "cadastrado") {
        $mensagem = "Matrícula feita com sucesso! Bem-vindo a Hogwarts.";
        $tipoMensagem = "mensagem-sucesso";
    } elseif ($_GET["msg"] == "atualizado") {
        $mensagem = "Dados do bruxo atualizados com sucesso.";
        $tipoMensagem = "mensagem-sucesso";
    } elseif ($_GET["msg"] == "excluido") {
        $mensagem = "Bruxo removido do livro de matrículas.";
        $tipoMensagem = "mensagem-sucesso";
    } elseif ($_GET["msg"] == "erro") {
        $mensagem = "Não foi possível salvar. Confira se todos os campos estão preenchidos corretamente.";
        $tipoMensagem = "mensagem-erro";
    }
}


// ---------- CASA VINDA DO CHAPÉU SELETOR ----------
// No resultado do teste existe um link "Matricular-me" que abre esta página
// com ?casa=NomeDaCasa. Aqui guardamos essa casa para já deixá-la marcada no select.
$casaSelecionada = "";
if (isset($_GET["casa"])) {
    $casaSelecionada = $_GET["casa"];
}

// Listas usadas para montar as opções do formulário com um laço (foreach)
$casas = ["Grifinória", "Sonserina", "Corvinal", "Lufa-Lufa"];
$animais = ["Coruja", "Gato", "Sapo"];
?>
<!DOCTYPE html>
<!-- Daqui para baixo é HTML normal. Os trechos entre < ?php e ? > são PHP
     e são trocados pelo servidor antes de a página chegar no navegador. -->
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal de Hogwarts | Cadastro</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Crimson+Text:wght@400;700&display=swap" rel="stylesheet">

    <!-- A página está dentro de pages/, por isso o ../ para voltar à raiz e achar o CSS -->
    <link rel="stylesheet" href="../css/style.css">

    <!-- JavaScript:
         script.js   = menu do celular (usado em todas as páginas)
         cadastro.js = valida o formulário e confirma antes de excluir
         defer = só roda o JavaScript depois que todo o HTML carregar -->
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

            <!-- Botão que abre e fecha o menu no celular (js/script.js) -->
            <button type="button" id="btn-menu" class="botao-menu">☰ Menu</button>

            <nav class="navegacao">
                <ul id="menu" class="menu">
                    <li><a href="../index.html">Início</a></li>
                    <li><a href="cadastro.php" class="ativo">Cadastro</a></li>
                    <li><a href="chapeu-seletor.html">Chapéu Seletor</a></li>
                    <li><a href="saloes.html">Salões Comunais</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">

        <!-- ==================== TÍTULO DA PÁGINA ==================== -->
        <section class="secao">
            <p class="subtitulo">Livro de matrículas</p>
            <h1 class="titulo-pagina">Registro de Bruxos</h1>
            <p class="descricao-pagina">Todo aluno de Hogwarts precisa estar registrado. Preencha seus dados abaixo para entrar no livro de matrículas da escola.</p>
        </section>

        <!-- ==================== AVISO ====================
             if do PHP: esta parte só é colocada na página se existir uma mensagem.
             htmlspecialchars() evita que algum código malicioso seja mostrado como HTML (XSS). -->
        <?php if ($mensagem != "") { ?>
            <p class="mensagem <?php echo $tipoMensagem; ?>"><?php echo htmlspecialchars($mensagem); ?></p>
        <?php } ?>

        <div class="cadastro-layout">

            <!-- ==================== CREATE: FORMULÁRIO ====================
                 A classe "painel" dá o fundo escuro com borda dourada. -->
            <section class="painel">
                <h2>Nova matrícula</h2>

                <!-- Mensagem de erro do JavaScript. Começa escondida (classe "escondido")
                     e o cadastro.js mostra quando algum campo estiver errado. -->
                <p id="mensagem-erro" class="mensagem mensagem-erro escondido"></p>

                <!-- form = formulário
                     action = arquivo que vai receber os dados (o PHP que salva no banco)
                     method="post" = os dados vão escondidos no corpo da requisição, não na URL
                     novalidate = desliga a validação automática do navegador,
                                  porque quem valida aqui é o nosso JavaScript -->
                <form id="form-cadastro" class="formulario" action="../php/cadastrar.php" method="post" novalidate>

                    <!-- Cada "campo" é um rótulo (label) + a caixa de digitação (input) -->
                    <div class="campo">
                        <!-- for="nome" liga o label ao input com id="nome":
                             clicar no texto coloca o cursor na caixa -->
                        <label for="nome">Nome completo</label>
                        <!-- type="text" = texto | name = nome com que o dado chega no PHP ($_POST["nome"])
                             placeholder = texto de exemplo em cinza | required = campo obrigatório -->
                        <input type="text" id="nome" name="nome" placeholder="Ex.: Hermione Granger" required>
                    </div>

                    <div class="campo">
                        <label for="email">E-mail</label>
                        <!-- type="email" = no celular abre o teclado com @ -->
                        <input type="email" id="email" name="email" placeholder="Ex.: hermione@hogwarts.com" required>
                    </div>

                    <div class="campo">
                        <label for="idade">Idade</label>
                        <!-- type="number" = só números | min e max = limites (11 a 17 anos) -->
                        <input type="number" id="idade" name="idade" min="11" max="17" placeholder="De 11 a 17 anos" required>
                    </div>

                    <div class="campo">
                        <label for="casa">Casa</label>
                        <!-- select = lista de opções que abre para escolher -->
                        <select id="casa" name="casa">
                            <!-- value="" = valor vazio, quer dizer "sem casa ainda" -->
                            <option value="">Ainda não fui selecionado</option>

                            <!-- foreach = repete o código para cada casa da lista $casas.
                                 Se a casa for a que veio do Chapéu Seletor, escreve "selected"
                                 para ela já aparecer escolhida. -->
                            <?php foreach ($casas as $casa) { ?>
                                <option value="<?php echo $casa; ?>" <?php if ($casa == $casaSelecionada) { echo "selected"; } ?>><?php echo $casa; ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <!-- fieldset agrupa campos relacionados e legend é o título do grupo -->
                    <fieldset class="campo">
                        <legend>Animal de estimação</legend>

                        <!-- Radios com o MESMO name formam um grupo: só dá para marcar um.
                             A primeira opção (Coruja) já vem marcada (checked). -->
                        <?php foreach ($animais as $animal) { ?>
                            <label class="opcao">
                                <input type="radio" name="animal" value="<?php echo $animal; ?>" <?php if ($animal == "Coruja") { echo "checked"; } ?>>
                                <?php echo $animal; ?>
                            </label>
                        <?php } ?>
                    </fieldset>

                    <div class="botoes-formulario">
                        <!-- type="submit" envia o formulário | type="reset" limpa todos os campos -->
                        <button type="submit" class="botao">Cadastrar</button>
                        <button type="reset" class="botao botao-secundario">Limpar</button>
                    </div>

                </form>
            </section>

            <!-- ==================== READ: LISTA DE BRUXOS ==================== -->
            <section class="secao-tabela">
                <h2>Bruxos matriculados</h2>

                <!-- Esta div deixa a tabela rolar para o lado no celular -->
                <div class="tabela-rolagem">

                    <!-- table = tabela | thead = cabeçalho | tbody = corpo
                         tr = linha | th = célula de título | td = célula comum -->
                    <table class="tabela">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>E-mail</th>
                                <th>Idade</th>
                                <th>Casa</th>
                                <th>Animal</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>

                            <!-- Se a lista estiver vazia (count = 0), mostra um aviso ocupando a linha toda.
                                 colspan="7" = a célula ocupa as 7 colunas -->
                            <?php if (count($bruxos) == 0) { ?>
                                <tr>
                                    <td colspan="7" class="tabela-vazia">Nenhum bruxo matriculado ainda.</td>
                                </tr>
                            <?php } ?>

                            <!-- foreach: cria uma linha (tr) para cada bruxo que veio do banco.
                                 $bruxo["nome"] pega a coluna "nome" daquele bruxo. -->
                            <?php foreach ($bruxos as $bruxo) { ?>
                                <tr>
                                    <td><?php echo $bruxo["id"]; ?></td>
                                    <td><?php echo htmlspecialchars($bruxo["nome"]); ?></td>
                                    <td><?php echo htmlspecialchars($bruxo["email"]); ?></td>
                                    <td><?php echo $bruxo["idade"]; ?></td>

                                    <!-- Se a casa estiver vazia, escreve "Não selecionado" em itálico -->
                                    <td>
                                        <?php if ($bruxo["casa"] == "") { ?>
                                            <span class="sem-casa">Não selecionado</span>
                                        <?php } else { ?>
                                            <?php echo htmlspecialchars($bruxo["casa"]); ?>
                                        <?php } ?>
                                    </td>

                                    <td><?php echo htmlspecialchars($bruxo["animal"]); ?></td>

                                    <td class="acoes">
                                        <!-- UPDATE: link para a página de edição levando o id no endereço
                                             (ex.: editar.php?id=3). Lá o PHP lê com $_GET["id"]. -->
                                        <a href="editar.php?id=<?php echo $bruxo["id"]; ?>" class="botao-editar">Editar</a>

                                        <!-- DELETE: um mini formulário com o id escondido (type="hidden").
                                             Ele usa POST porque apaga dados do banco.
                                             O cadastro.js pergunta "Tem certeza?" antes de enviar. -->
                                        <form action="../php/excluir.php" method="post" class="form-excluir">
                                            <input type="hidden" name="id" value="<?php echo $bruxo["id"]; ?>">
                                            <button type="submit" class="botao-excluir">Excluir</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php } ?>

                        </tbody>
                    </table>
                </div>
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
