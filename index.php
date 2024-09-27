
<?php
session_start(); // Iniciar a sessão
// Código de autenticação e lógica do seu aplicativo aqui

// Incluindo o menu
include 'menu.php';
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cronômetro de Tarefas</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container">
        <h2>Selecione sua Prova</h2>
        <div class="opcoes-provas">
            <ul id="lista-provas">
                <li data-value="Prova 1">Prova 1</li>
                <li data-value="Prova 2">Prova 2</li>
                <li data-value="Prova 3">Prova 3</li>
                <li data-value="Prova 4">Prova 4</li>
                

            </ul>
            <button id="salvar">Salvar Seleção</button>
            <div id="mensagem"></div>
        </div>

        <h1>Cronômetro de Tarefas</h1>
        <div id="cronometro">
            <span id="horas">00</span>:<span id="minutos">00</span>:<span id="segundos">00</span>
        </div>
        <div class="buttons">
            <button id="iniciarBtn">Iniciar Prova</button>
            <button id="pausarBtn">Pausar Prova</button> <!-- Botão de Pausar -->
            <button id="finalizarBtn">Finalizar Prova</button>
        </div>
    </div>


<!-- MODAL  -->

<!-- Modal de Confirmação -->
<!-- Modal de Confirmação -->
<div id="modalConfirmacao" class="modal">
    <div class="modal-conteudo">
        <h2>Confirmar Finalização</h2>
        <p>Você tem certeza que deseja finalizar a prova?</p>
        <button id="btnConfirmar">Confirmar</button>
        <button id="btnCancelar">Cancelar</button>
    </div>
</div>



    <script src="scripts.js"></script>
</body>
</html>
