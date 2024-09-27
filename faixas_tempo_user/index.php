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
if (!isset($_SESSION['user_id'])) {
    die("Usuário não autenticado.");
}

// Obter o ID do usuário da sessão
$user_id = $_SESSION['user_id'];

// Recuperar os dados da tabela, ordenando pelos tempos mais recentes primeiro
$sql = "SELECT * FROM tempos_tarefas WHERE user_id = :user_id ORDER BY timestamp DESC";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$tempos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Função para calcular o tempo total de todas as faixas
function calcularTempoTotal($tempos) {
    $totalSegundos = 0;

    // Somar todas as faixas de tempo em segundos
    foreach ($tempos as $tempo) {
        $totalSegundos += ($tempo['horas'] * 3600) + ($tempo['minutos'] * 60) + $tempo['segundos'];
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

    <!-- Mostrar o user_id da sessão para debug -->
    <p class="debug-info"><strong>User ID da sessão (para debug):</strong> <?= htmlspecialchars($user_id) ?></p>

    <!-- Mostrar o tempo total -->
    <p class="total"><strong>Tempo Total de todas as Provas:</strong> <?= htmlspecialchars($tempoTotal) ?></p>

    <?php if (count($tempos) > 0): ?>
        <?php foreach ($tempos as $tempo): ?>
            <div class="faixa">
                <span><strong>User ID:</strong> <?= htmlspecialchars($tempo['user_id']) ?></span> <!-- Mostrar user_id para debug -->
                
                <!-- Exibir o tempo formatado HH:MM:SS -->
                <span><strong>Tempo:</strong> 
                    <?php
                    $horas = str_pad($tempo['horas'], 2, '0', STR_PAD_LEFT);
                    $minutos = str_pad($tempo['minutos'], 2, '0', STR_PAD_LEFT);
                    $segundos = str_pad($tempo['segundos'], 2, '0', STR_PAD_LEFT);
                    echo htmlspecialchars("$horas:$minutos:$segundos");
                    ?>
                </span>
                
                <span><strong>Prova Selecionada:</strong> <?= htmlspecialchars($tempo['prova_selecionada']) ?></span>
                <span><strong>Data e Hora:</strong> <?= htmlspecialchars($tempo['timestamp']) ?></span>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="no-tempos">Nenhum tempo salvo encontrado.</p>
    <?php endif; ?>
</div>

</body>
</html>
