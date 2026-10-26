@echo off
REM ==================================================================
REM  abrir-site.bat | Liga o XAMPP, prepara o banco e abre o site
REM ==================================================================
REM  Um arquivo .bat é uma lista de comandos do Windows que rodam um
REM  depois do outro quando você dá dois cliques nele.
REM
REM  O que este arquivo faz:
REM  1. Confere se o .zip foi extraído e se o XAMPP está instalado
REM  2. Copia o projeto para C:\xampp\htdocs\hogwarts
REM     (o Apache só enxerga sites que estão dentro da pasta htdocs)
REM  3. Liga o Apache (servidor que roda o PHP) e o MySQL (banco)
REM  4. Cria o banco "hogwarts" usando banco\hogwarts.sql
REM     (só na primeira vez, se o banco ainda não existir)
REM  5. Abre o site no navegador
REM
REM  Sobre os comandos:
REM  - "@echo off" (primeira linha) esconde os comandos da tela preta
REM    e deixa aparecer só as mensagens escritas com "echo".
REM  - "REM" no começo da linha = comentário (o Windows ignora).
REM  - As mensagens não têm acento porque a tela preta do Windows
REM    mostra os acentos errado.
REM ==================================================================


REM Título da janela preta
title Portal de Hogwarts


REM ---------- CONFIGURAÇÕES ----------
REM "set" cria uma variável. Depois ela é usada escrevendo %NOME%.

REM Pasta onde o XAMPP está instalado. Se no seu computador for outra, troque aqui.
set "XAMPP=C:\xampp"

REM Pasta htdocs do XAMPP, onde ficam os sites
set "HTDOCS=%XAMPP%\htdocs"

REM Nome da pasta do site dentro do htdocs. Vira o endereço http://localhost/hogwarts/
set "PASTA_SITE=hogwarts"

REM Endereço que vai abrir no navegador
set "ENDERECO=http://localhost/%PASTA_SITE%/"

REM Programa do MySQL que roda comandos SQL pela linha de comando
set "MYSQL=%XAMPP%\mysql\bin\mysql.exe"

REM %~dp0 = pasta onde ESTE arquivo .bat está (sempre termina com \).
REM Assim funciona em qualquer computador, sem caminho fixo.
set "PROJETO=%~dp0"


echo.
echo  ==================================================
echo                PORTAL DE HOGWARTS
echo  ==================================================
echo.


REM ---------- 1. CONFERÊNCIAS ----------

REM "if not exist" = se o arquivo NÃO existir...
REM Se o index.html não está do lado deste .bat, o .zip não foi extraído.
REM "goto" pula para a parte do arquivo com aquele nome (ex.: :erro_zip).
if not exist "%PROJETO%index.html" goto erro_zip

REM Se o painel do XAMPP não existe, o XAMPP não está instalado nesse caminho
if not exist "%XAMPP%\xampp-control.exe" goto erro_xampp


REM ---------- 2. COPIA O PROJETO PARA O HTDOCS ----------

REM Se o projeto já está dentro do htdocs, não precisa copiar.
REM /I = compara sem diferenciar maiúsculas de minúsculas.
if /I "%PROJETO%"=="%HTDOCS%\%PASTA_SITE%\" goto ligar_servidores

echo  [1/5] Copiando o projeto para %HTDOCS%\%PASTA_SITE%
REM robocopy copia pastas inteiras.
REM /E = copia as subpastas (até as vazias).
REM /NFL /NDL /NJH /NJS /NP = não lista cada arquivo na tela.
REM > nul = joga fora o texto que o comando escreveria.
robocopy "%PROJETO%." "%HTDOCS%\%PASTA_SITE%" /E /NFL /NDL /NJH /NJS /NP > nul


:ligar_servidores
REM ---------- 3. LIGA O APACHE E O MYSQL ----------
echo  [2/5] Ligando o Apache e o MySQL

REM tasklist lista os programas abertos e find procura o nome do programa.
REM Se NÃO encontrar (errorlevel 1), o programa está desligado, então ligamos.
REM "start" abre em outra janela; /min deixa essa janela minimizada.
tasklist /FI "IMAGENAME eq httpd.exe" | find /I "httpd.exe" > nul
if errorlevel 1 start "Apache - nao feche esta janela" /min "%XAMPP%\apache_start.bat"

tasklist /FI "IMAGENAME eq mysqld.exe" | find /I "mysqld.exe" > nul
if errorlevel 1 start "MySQL - nao feche esta janela" /min "%XAMPP%\mysql_start.bat"


REM ---------- 4. ESPERA O MYSQL E CRIA O BANCO ----------
echo  [3/5] Esperando o MySQL ficar pronto

REM Laço de espera: tenta falar com o MySQL até 20 vezes.
REM "set /a" faz conta: soma 1 na variável TENTATIVAS.
set TENTATIVAS=0

:esperar_mysql
set /a TENTATIVAS=TENTATIVAS+1

REM Manda um comando simples (SELECT 1). Se funcionar, o MySQL está pronto.
REM 2>&1 junta as mensagens de erro com as normais, e > nul esconde as duas.
"%MYSQL%" -u root -e "SELECT 1" > nul 2>&1

REM "if not errorlevel 1" = se NÃO deu erro, pula para verificar o banco
if not errorlevel 1 goto verificar_banco

REM Depois de 20 tentativas sem resposta, desiste e mostra o erro
if %TENTATIVAS% GEQ 20 goto erro_mysql

REM Espera 1 segundo (o ping para o próprio computador é um jeito simples de esperar)
ping -n 2 127.0.0.1 > nul
goto esperar_mysql


:verificar_banco
REM "USE hogwarts" dá erro se o banco ainda não existir.
"%MYSQL%" -u root -e "USE hogwarts" > nul 2>&1
if errorlevel 1 goto criar_banco

echo  [4/5] Banco de dados hogwarts ja existe
goto esperar_apache

:criar_banco
echo  [4/5] Criando o banco de dados hogwarts
REM O sinal < manda o conteúdo do arquivo .sql para dentro do MySQL
"%MYSQL%" -u root < "%PROJETO%banco\hogwarts.sql"
if errorlevel 1 goto erro_mysql


REM ---------- 5. ESPERA O APACHE E ABRE O SITE ----------
:esperar_apache
set TENTATIVAS_APACHE=0

:laco_apache
set /a TENTATIVAS_APACHE=TENTATIVAS_APACHE+1

REM curl acessa o endereço como se fosse um navegador. Se responder, o Apache está pronto.
REM -s = modo silencioso | -o nul = não guarda a página | --max-time 2 = desiste depois de 2 segundos
REM 127.0.0.1 é o mesmo que localhost (o próprio computador).
curl -s --max-time 2 -o nul "http://127.0.0.1/" > nul 2>&1
if not errorlevel 1 goto abrir_site

REM Espera no máximo 10 tentativas. Depois abre o site mesmo assim.
if %TENTATIVAS_APACHE% GEQ 10 goto abrir_site
ping -n 2 127.0.0.1 > nul
goto laco_apache


:abrir_site
echo  [5/5] Abrindo o site no navegador
REM start com um endereço abre o navegador padrão.
REM As aspas vazias "" são o título da janela, que o start exige quando usamos aspas.
start "" "%ENDERECO%"

echo.
echo  Pronto! O site esta em: %ENDERECO%
echo.
echo  Deixe as janelas do Apache e do MySQL abertas enquanto usar o site.
echo  Se mudar algum arquivo do projeto, rode este .bat de novo para copiar.
echo.
pause
REM "exit /b" encerra este arquivo aqui, sem rodar as partes de erro abaixo
exit /b


REM ---------- MENSAGENS DE ERRO ----------

:erro_zip
echo  ERRO: parece que o arquivo .zip nao foi extraido.
echo  Clique com o botao direito no .zip, escolha "Extrair tudo"
echo  e rode o abrir-site.bat de dentro da pasta extraida.
echo.
pause
exit /b

:erro_xampp
echo  ERRO: o XAMPP nao foi encontrado em %XAMPP%
echo  Instale o XAMPP ou corrija a linha "set XAMPP=" deste arquivo.
echo.
echo  Abrindo so as paginas em HTML. O cadastro precisa do XAMPP.
start "" "%PROJETO%index.html"
echo.
pause
exit /b

:erro_mysql
echo.
echo  ERRO: nao foi possivel usar o MySQL.
echo  Abra o painel do XAMPP, clique em Start no MySQL
echo  e rode este arquivo de novo.
start "" "%XAMPP%\xampp-control.exe"
echo.
pause
exit /b
