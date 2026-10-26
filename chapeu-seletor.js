/* ============================================================
   chapeu-seletor.js | Lógica do teste do Chapéu Seletor
   ============================================================
   Como o Chapéu escolhe a casa:
   1. Cada resposta do questionário tem como "value" o nome de
      uma casa (ex.: value="grifinoria").
   2. Quando o questionário é enviado, contamos quantas respostas
      marcadas cada casa recebeu. Isso vira a pontuação.
   3. A casa com mais pontos é a escolhida. Se der empate, o
      Chapéu SORTEIA uma entre as casas empatadas.
   4. O questionário some, aparece uma mensagem de suspense por
      2 segundos e depois só o cartão da casa escolhida é mostrado.
   ============================================================ */


// ---------- 1. SELECIONANDO OS ELEMENTOS DA PÁGINA ----------
// querySelector pega UM elemento; querySelectorAll pega TODOS que combinam (uma lista).
const questionario = document.querySelector('#questionario');   // o formulário com as perguntas
const pensando = document.querySelector('#pensando');           // mensagem "O Chapéu está pensando..."
const resultado = document.querySelector('#resultado');         // seção inteira do resultado
const cartoesCasas = document.querySelectorAll('.casa-card');   // os 4 cartões das casas
const botaoRefazer = document.querySelector('#btn-refazer');    // botão "Fazer o teste de novo"


// ---------- 2. QUANDO O QUESTIONÁRIO FOR ENVIADO ----------
// O evento 'submit' acontece quando o botão "Colocar o chapéu" é clicado.
// Obs.: como os radios têm "required", o navegador só deixa enviar
// se TODAS as perguntas estiverem respondidas.
questionario.addEventListener('submit', function (evento) {

    // O padrão de um formulário é recarregar a página ao enviar.
    // preventDefault() cancela isso, para o JavaScript cuidar do resultado.
    evento.preventDefault();

    // Placar: um objeto com 0 ponto para cada casa.
    // Os nomes aqui são iguais aos "value" das respostas no HTML.
    const pontos = {
        'grifinoria': 0,
        'sonserina': 0,
        'corvinal': 0,
        'lufa-lufa': 0
    };

    // Pega só as opções marcadas (:checked) dentro do questionário
    const respostas = questionario.querySelectorAll('input:checked');

    // Laço for: passa por cada resposta marcada, uma de cada vez.
    // i começa em 0 e vai até o total de respostas (respostas.length).
    for (let i = 0; i < respostas.length; i++) {
        const casa = respostas[i].value;   // ex.: 'corvinal'
        pontos[casa] = pontos[casa] + 1;   // soma 1 ponto nessa casa
    }

    // Descobre qual foi a MAIOR pontuação.
    // for...in passa por cada nome de casa dentro do objeto pontos.
    let maiorPontuacao = 0;
    for (const casa in pontos) {
        if (pontos[casa] > maiorPontuacao) {
            maiorPontuacao = pontos[casa];
        }
    }

    // Guarda numa lista (array) todas as casas que chegaram na maior pontuação.
    // Se só uma casa tiver essa pontuação, a lista vai ter só ela.
    const casasEmpatadas = [];
    for (const casa in pontos) {
        if (pontos[casa] === maiorPontuacao) {
            casasEmpatadas.push(casa);     // push adiciona no fim da lista
        }
    }

    // Sorteio:
    // Math.random() gera um número quebrado entre 0 e 1 (ex.: 0.73).
    // Multiplicando pelo tamanho da lista e arredondando para baixo (Math.floor),
    // temos uma posição válida da lista (0, 1, 2...).
    const posicaoSorteada = Math.floor(Math.random() * casasEmpatadas.length);
    const casaEscolhida = casasEmpatadas[posicaoSorteada];

    // Esconde o questionário e mostra a mensagem de suspense.
    // A classe "escondido" está no CSS com display: none.
    questionario.classList.add('escondido');
    pensando.classList.remove('escondido');
    pensando.scrollIntoView({ behavior: 'smooth' });   // rola a tela suavemente até a mensagem

    // setTimeout espera um tempo antes de rodar a função.
    // 2000 milissegundos = 2 segundos de suspense.
    setTimeout(function () {
        mostrarResultado(casaEscolhida);
    }, 2000);
});


// ---------- 3. FUNÇÃO QUE MOSTRA A CASA ESCOLHIDA ----------
// Recebe o nome da casa (ex.: 'sonserina') e mostra só o cartão dela.
function mostrarResultado(casaEscolhida) {

    // Tira a mensagem de suspense
    pensando.classList.add('escondido');

    // Esconde os 4 cartões
    for (let i = 0; i < cartoesCasas.length; i++) {
        cartoesCasas[i].classList.add('escondido');
    }

    // Mostra só o cartão da casa escolhida.
    // Os cartões têm id="resultado-grifinoria", id="resultado-sonserina" etc.,
    // então juntamos o texto '#resultado-' com o nome da casa.
    const cartaoEscolhido = document.querySelector('#resultado-' + casaEscolhida);
    cartaoEscolhido.classList.remove('escondido');

    // Mostra a seção de resultado e rola a tela até ela
    resultado.classList.remove('escondido');
    resultado.scrollIntoView({ behavior: 'smooth' });
}


// ---------- 4. BOTÃO "FAZER O TESTE DE NOVO" ----------
botaoRefazer.addEventListener('click', function () {

    // reset() desmarca todas as respostas do formulário
    questionario.reset();

    // Esconde o resultado e mostra o questionário de novo
    resultado.classList.add('escondido');
    questionario.classList.remove('escondido');

    // Volta a tela para o começo do questionário
    questionario.scrollIntoView({ behavior: 'smooth' });
});
