/* ============================================================
   script.js | JavaScript usado em TODAS as páginas
   ============================================================
   Função: abrir e fechar o menu no celular.

   No celular o menu começa escondido (regra .menu no CSS) e
   aparece um botão "☰ Menu". Quando o botão é clicado, este
   código coloca ou tira a classe "aberto" da lista do menu.
   A classe "aberto" está no CSS com display: flex (menu aparece).

   Em telas a partir de 768px o CSS esconde o botão e deixa o
   menu sempre visível, então este código não muda nada lá.
   ============================================================ */

// 1. Seleciona os elementos pelo id (o # no querySelector significa id)
const botaoMenu = document.querySelector('#btn-menu');
const menu = document.querySelector('#menu');

// 2. addEventListener "escuta" um evento. Aqui o evento é o 'click' no botão.
//    Toda vez que o botão for clicado, a função abaixo é executada.
botaoMenu.addEventListener('click', function () {

    // 3. classList.toggle alterna a classe:
    //    se o menu NÃO tem a classe "aberto", ela é adicionada (menu aparece);
    //    se o menu JÁ tem a classe "aberto", ela é removida (menu some).
    menu.classList.toggle('aberto');
});
