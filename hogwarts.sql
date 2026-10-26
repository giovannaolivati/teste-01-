-- ============================================================
-- hogwarts.sql | Criação do banco de dados do Portal de Hogwarts
-- ============================================================
-- Este arquivo cria o banco, a tabela e alguns bruxos de exemplo.
--
-- Como usar (escolha UMA das formas):
-- 1. Dar dois cliques no abrir-site.bat (ele roda este arquivo sozinho
--    na primeira vez), OU
-- 2. Abrir o phpMyAdmin (http://localhost/phpmyadmin), clicar na aba
--    "Importar", escolher este arquivo e clicar em "Importar".
--
-- Em SQL, as linhas que começam com dois tracinhos são comentários.
-- ============================================================


-- Avisa ao MySQL que este arquivo está escrito em UTF-8,
-- para os acentos (como em "Grifinória") serem gravados certos.
SET NAMES utf8mb4;


-- ---------- CRIA O BANCO DE DADOS ----------
-- IF NOT EXISTS: só cria se ainda não existir (não dá erro se rodar de novo).
-- utf8mb4 é o conjunto de caracteres que aceita acentos e emojis.
CREATE DATABASE IF NOT EXISTS hogwarts
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

-- A partir daqui, todos os comandos são feitos dentro do banco "hogwarts".
USE hogwarts;


-- ---------- CRIA A TABELA DOS BRUXOS ----------
-- Cada linha da tabela é um bruxo matriculado. Cada coluna é uma informação dele.
CREATE TABLE IF NOT EXISTS bruxos (

    -- id: número único de cada bruxo.
    -- INT = número inteiro | AUTO_INCREMENT = o MySQL gera o número sozinho (1, 2, 3...)
    -- PRIMARY KEY = chave primária, é o que identifica cada linha (nunca se repete)
    id INT AUTO_INCREMENT PRIMARY KEY,

    -- VARCHAR(100) = texto de até 100 caracteres | NOT NULL = campo obrigatório
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,

    -- Idade em número inteiro (de 11 a 17, conferido no JavaScript e no PHP)
    idade INT NOT NULL,

    -- Casa do bruxo. DEFAULT '' = se não for informada, fica vazia ("ainda não selecionado")
    casa VARCHAR(20) NOT NULL DEFAULT '',

    -- Animal de estimação: Coruja, Gato ou Sapo
    animal VARCHAR(20) NOT NULL
);


-- ---------- BRUXOS DE EXEMPLO ----------
-- INSERT INTO = cadastra linhas novas na tabela.
-- O IGNORE faz o MySQL pular a linha se aquele id já existir,
-- assim os exemplos não ficam duplicados se o arquivo for importado de novo.
INSERT IGNORE INTO bruxos (id, nome, email, idade, casa, animal) VALUES
    (1, 'Harry Potter',    'harry@hogwarts.com',   11, 'Grifinória', 'Coruja'),
    (2, 'Draco Malfoy',    'draco@hogwarts.com',   11, 'Sonserina',  'Coruja'),
    (3, 'Luna Lovegood',   'luna@hogwarts.com',    12, 'Corvinal',   'Gato'),
    (4, 'Cedrico Diggory', 'cedrico@hogwarts.com', 14, 'Lufa-Lufa',  'Sapo');
