<?php
// Configurações do banco de dados
$host = 'localhost';
$dbname = 'tarefas_db'; // Altere para o nome do seu banco de dados
$username = 'root'; // Altere para seu usuário
$password = 'root'; // Altere para sua senha

// Conectar ao banco de dados
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

// Recuperar o último tempo_total_formatado de cada usuário
$sql = "
    SELECT u.username, t.tempo_total_formatado
    FROM tempos_tarefas t
    JOIN usuarios u ON t.user_id = u.id
    WHERE (t.user_id, t.data_hora) IN (
        SELECT user_id, MAX(data_hora) 
        FROM tempos_tarefas 
        GROUP BY user_id
    )
    ORDER BY TIME_FORMAT(t.tempo_total_formatado, '%H:%i:%s') ASC
";
$stmt = $conn->prepare($sql);
$stmt->execute();
$tempos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Últimos Tempos dos Usuários</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css"> <!-- Link para o CSS externo -->
</head>
<body>

<?php
// Incluindo o menu
include '../menu.php';
?>

<div class="container">
    <h1>Painel de Pontuação das Equipes</h1>

    <table>
        <thead>
            <tr>
                <th>Posição</th>
                <th>Equipes</th> <!-- Alterado para Username -->
                <th>Tempo Total</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($tempos) > 0): ?>
                <?php foreach ($tempos as $index => $tempo): ?>
                    <tr class="
                        <?php 
                            if ($index == 0) echo 'gold';
                            elseif ($index == 1) echo 'silver';
                            elseif ($index == 2) echo 'platinum';
                        ?>
                    ">
                        <td class="ranking"><?= ($index + 1) ?>º</td>
                        <td><?= htmlspecialchars($tempo['username']) ?></td> <!-- Exibe o username -->
                        <td><?= htmlspecialchars($tempo['tempo_total_formatado']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">Nenhum tempo salvo encontrado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
