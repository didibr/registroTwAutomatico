# registroTwAutomatico
node.js + puppeteer: automatic scrap twitch register page<br>

Maquina Virtual deve conter<br>
main.sh e main.js<br>

main.sh:<br>
é o executavél responsavel por atualizar e instalar requesitos para rodar o Puppeteer que é um webscrap<br>
capaz de simular ações em um navegador, na primeira execução, deve ser escolhida a opção (0) e instalar<br>
todas dependencias.<br>
https://github.com/puppeteer/puppeteer

main.js:<br>
é o responsavel por simular cliques e navegação, receber email cadastrar usuario e colocar foto no perfil.<br>
tudo automatico exceto o captcha, que para automatizar pode se colocar 2captcha, porem é pago.<br>
https://2captcha.com/<br>

configurações:<br>
ao colocar o index.php em seu servidor web e abrir a pagina a primeira vez, clique no botão Gerar DB<br>
para criar o banco de dados SQLite.<br>
main.js: Linha 190 - configurar o local de index.php para salvar os logins criados<br>
