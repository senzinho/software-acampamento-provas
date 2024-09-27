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

// Recuperar os dados da tabela
$sql = "SELECT * FROM tempos_tarefas WHERE user_id = :user_id ORDER BY timestamp DESC"; // Ordenando pelo mais recente
$stmt = $conn->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$tempos = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tempos Tarefas</title>
    <style>
        .faixa {
            background-color: #f2f2f2;
            padding: 20px;
            margin: 10px 0;
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .faixa span {
            font-size: 16px;
            font-family: Arial, sans-serif;
        }
        .faixa strong {
            color: #333;
        }
    </style>
</head>
<body>

<h1>Tempos Salvos</h1>

<!-- Mostrar o user_id da sessão para debug -->
<p><strong>User ID da sessão (para debug):</strong> <?= htmlspecialchars($user_id) ?></p>

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
    <p>Nenhum tempo salvo encontrado.</p>
<?php endif; ?>

</body>
</html>
