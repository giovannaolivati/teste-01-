/* ============================================================
   cadastro.js | JavaScript das páginas cadastro.php e editar.php
   ============================================================
   Faz duas coisas:
   1. Valida o formulário ANTES de enviar para o PHP
      (nome, e-mail e idade). Se tiver erro, não envia e mostra
      uma mensagem explicando o que corrigir.
   2. Pergunta "Tem certeza?" antes de excluir um bruxo.
   ============================================================ */


// ---------- 1. VALIDAÇÃO DO FORMULÁRIO ----------

// Seleciona o formulário e a caixa de mensagem de erro
const formCadastro = document.querySelector('#form-cadastro');
const mensagemErro = document.querySelector('#mensagem-erro');

// Evento 'submit': acontece quando o botão de enviar é clicado
formCadastro.addEventListener('submit', function (evento) {

    // .value pega o que foi digitado no campo.
    // .trim() tira os espaços do começo e do fim (ex.: "  Harry  " vira "Harry").
    const nome = document.querySelector('#nome').value.trim();
    const email = document.querySelector('#email').value.trim();

    // Number() transforma o texto digitado em número, para podermos comparar
    const idade = Number(document.querySelector('#idade').value);

    // Variável que vai guardar o texto do erro. Vazia = nenhum erro.
    let erro = '';

    // Confere um campo de cada vez. O primeiro erro encontrado é o que aparece.
    // .length = quantidade de letras do texto
    // .includes('@') = verdadeiro se o texto tiver @
    // ! = "não" (inverte verdadeiro/falso)
    // || = "ou"
    if (nome.length < 3) {
        erro = 'Escreva seu nome completo (pelo menos 3 letras).';
    } else if (email === '' || !email.includes('@')) {
        erro = 'Digite um e-mail válido, com @.';
    } else if (idade < 11 || idade > 17) {
        erro = 'Hogwarts só aceita alunos de 11 a 17 anos.';
    }

    if (erro !== '') {
        // Tem erro: cancela o envio para o PHP...
        evento.preventDefault();

        // ...coloca o texto na caixa de mensagem (textContent só troca o texto, é seguro)...
        mensagemErro.textContent = erro;

        // ...e mostra a caixa, tirando a classe "escondido"
        mensagemErro.classList.remove('escondido');
    }
    // Se não tiver erro, não fazemos nada e o formulário é enviado normalmente.
});


// ---------- 2. CONFIRMAÇÃO ANTES DE EXCLUIR ----------

// Pega TODOS os mini formulários de excluir (um em cada linha da tabela).
// Na página editar.php não existe nenhum, então a lista fica vazia e o laço não roda.
const formsExcluir = document.querySelectorAll('.form-excluir');

for (let i = 0; i < formsExcluir.length; i++) {

    formsExcluir[i].addEventListener('submit', function (evento) {

        // confirm() abre uma caixinha do navegador com "OK" e "Cancelar".
        // Devolve true se clicar em OK e false se clicar em Cancelar.
        const confirmou = confirm('Tem certeza que deseja excluir este bruxo do livro de matrículas?');

        // Se clicou em Cancelar, impede o envio (o bruxo não é excluído)
        if (!confirmou) {
            evento.preventDefault();
        }
    });
}
