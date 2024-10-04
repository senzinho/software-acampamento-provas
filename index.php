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
                <li data-value="Tangram PROVA 1"><b>1º Tangram sexta à Noite</b></li>
                <li>2º Sábado 8:00 <b>PROVA 2</b>
                    <ul>
                        <li data-value="Sábado-Manhã 8h - [Trenó]">Trenó</li>
                        <li data-value="Sábado-Manhã 8h - [Tarzan]">Tarzan</li>
                        <li data-value="Sábado-Manhã 8h - [Falsa Baiana]">Falsa Baiana</li>
                        <li data-value="Sábado-Manhã 8h - [Labirinto]">Labirinto</li>
                        <li data-value="Sábado-Manhã 8h - [Tiro ao Alvo]">Tiro ao Alvo</li>
                        <li data-value="Sábado-Manhã 8h - [Enfermagem]">Enfermagem</li>
                        <li data-value="Sábado-Manhã 8h - [Outra]">Outra</li>
                    </ul>
                </li>
                <li data-value="Vencendo os medos enfrentando o dragão 10:40 - PROVA 3">Vencendo os medos enfrentando o dragão 10:40</li>
                <li data-value="Prova de amor maior não há 15:50 - PROVA 4">Prova de amor maior não há 15:50</li>
                <li data-value="Caça ao tesouro 9:00 - PROVA 5">Caça ao Tesouro 9:00</li>
                <li data-value="Águas que purificam 12:00 - PROVA 6">Águas que purificam 12:00</li>
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
        <button id="pausarBtn">Pausar Prova</button>
        <button id="finalizarBtn">Finalizar Prova</button>
    </div>
</div>

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
