<?php
// Configurações do banco de dados
$host = 'localhost';
$dbname = 'tarefas_db';
$username = 'root'; // Altere para seu usuário
$password = 'root'; // Altere para sua senha

// Iniciar a sessão
session_start();

// Conectar ao banco de dados
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

// Verificar se o usuário está logado
if (!isset($_SESSION['username'])) {
    die("Usuário não autenticado.");
}

// Obter o username da sessão
$username = $_SESSION['username'];

// Recuperar o user_id correspondente ao username
$sql = "SELECT id FROM usuarios WHERE username = :username"; // Ajuste para o nome correto da tabela
$stmt = $conn->prepare($sql);
$stmt->bindParam(':username', $username, PDO::PARAM_STR);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Verificar se o usuário foi encontrado
if (!$user) {
    die("Usuário não encontrado.");
}

// Obter o ID do usuário da consulta
$user_id = $user['id'];

// Recuperar os dados da tabela, ordenando pelos tempos mais recentes primeiro
$sql = "SELECT * FROM tempos_tarefas WHERE user_id = :user_id ORDER BY data_hora DESC"; // Alterado para data_hora
$stmt = $conn->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$tempos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Função para calcular o tempo total de todas as provas
function calcularTempoTotal($tempos) {
    $totalSegundos = 0;

    // Somar todos os tempos formatados
    foreach ($tempos as $tempo) {
        // Extrair horas, minutos e segundos do formato HH:MM:SS
        list($horas, $minutos, $segundos) = explode(':', $tempo['tempo_formatado']);
        
        $totalSegundos += ($horas * 3600) + ($minutos * 60) + $segundos;
    }

    // Converter o tempo total em horas, minutos e segundos
    $horas = floor($totalSegundos / 3600);
    $resto = $totalSegundos % 3600;
    $minutos = floor($resto / 60);
    $segundos = $resto % 60;

    // Retornar o tempo formatado como HH:MM:SS
    return sprintf('%02d:%02d:%02d', $horas, $minutos, $segundos);
}

$tempoTotal = calcularTempoTotal($tempos);
?>

<?php
include '../menu.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tempos Tarefas</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css"> <!-- Link para o CSS externo -->
</head>
<body>

<div class="container">
    <h1>Tempos Salvos</h1>

    <!--Mostrar o username da sessão para debug 
    <p class="debug-info"><strong>Username da sessão (para debug):</strong> </p> -->

    <!-- Mostrar o tempo total -->
    <p class="total"><strong>Tempo Total de todas as Provas:</strong> <?= htmlspecialchars($tempoTotal) ?></p>

    <?php if (count($tempos) > 0): ?>
        <?php foreach ($tempos as $tempo): ?>
            <div class="faixa">
                <span><strong>Equipe:</strong> <?= htmlspecialchars($username) ?></span> <!-- Mostrar username para debug -->
                
                <!-- Exibir o tempo formatado HH:MM:SS -->
                <span><strong>Tempo:</strong> <?= htmlspecialchars($tempo['tempo_formatado']) ?></span>
                
                <span><strong>Prova Selecionada:</strong> <?= htmlspecialchars($tempo['prova_selecionada']) ?></span>
                <span><strong>Data e Hora:</strong> <?= htmlspecialchars($tempo['data_hora']) ?></span> <!-- Alterado para data_hora -->
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="no-tempos">Nenhum tempo salvo encontrado.</p>
    <?php endif; ?>
</div>

</body>
</html>
