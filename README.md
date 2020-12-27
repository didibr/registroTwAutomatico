# registroTwAutomatico
node.js + puppeteer: automatic scrap twitch register page

Maquina Virtual deve conter
main.sh e main.js

main.sh:
é o executavél responsavel por atualizar e instalar requesitos para rodar o Puppeteer que é um webscrap
capaz de simular ações em um navegador, na primeira execução, deve ser escolhida a opção (0) e instalar
todas dependencias.
https://github.com/puppeteer/puppeteer

main.js:
é o responsavel por simular cliques e navegação, receber email cadastrar usuario e colocar foto no perfil.
tudo automatico exceto o captcha, que para automatizar pode se colocar 2captcha, porem é pago.
https://2captcha.com/

configurações:
ao colocar o index.php em seu servidor web e abrir a pagina a primeira vez, clique no botão Gerar DB
para criar o banco de dados SQLite.
main.js: Linha 190 - configurar o local de index.php para salvar os logins criados
