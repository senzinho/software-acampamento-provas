<?php
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

// Inicializando a variável $tempos
$tempos = []; // Sempre inicializa como um array vazio

// Recuperar o último tempo_total_formatado de cada usuário e a soma de seus pontos
try {
    $sql = "
    SELECT u.username, 
           SEC_TO_TIME(SUM(TIME_TO_SEC(t.tempo_total_formatado))) AS tempo_total_formatado, 
           SUM(t.pontos) AS total_pontos
    FROM tempos_tarefas t
    JOIN usuarios u ON t.user_id = u.id
    GROUP BY u.username
    ORDER BY total_pontos DESC, SUM(TIME_TO_SEC(t.tempo_total_formatado)) ASC
";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $tempos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Em caso de erro, captura e exibe a mensagem
    echo "Erro ao recuperar dados: " . $e->getMessage();
    // A variável $tempos já foi inicializada como um array vazio
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Últimos Tempos dos Usuários</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css"> <!-- Link para o CSS externo -->

    <style>
        body {
    font-family: 'Roboto', sans-serif;
    background-color: #f4f4f9;
    color: #333;
    margin: 0;
    padding: 0;
    }

    .container {
        max-width: 800px;
        margin: 50px auto;
        padding: 20px;
        background-color: #fff;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        border-radius: 8px;
        margin-top: 140px;
    }

    h1 {
        text-align: center;
        color: #333;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    th, td {
        padding: 12px;
        text-align: center;
        border-bottom: 1px solid #ddd;
    }

    th {
        background-color: #DD2A7B;
        color: white;
    }

    tr:nth-child(even) {
        color:black;
    }

    tr:hover {
        background-color: #ddd;
    }

    .ranking {
        font-weight: bold;
        padding: 10px;
    }

    .gold {
    background-color: #F58529; /* Cor Laranja */
    }

    .pink {
        background-color: #DD2A7B; /* Cor Rosa */
        color:white;
    }

    .purple {
        background-color: #8134B8; /* Cor Roxa */
        color:white;
    }

    .yellow {
        background-color: #F9D249; /* Cor Amarela */
    }

    .blue {
        background-color: #4A4BFD; /* Cor Azul */
        color:white;
    }


    </style>
</head>
<body>
<?php
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
                <th>Total de Pontos</th> <!-- Nova coluna para total de pontos -->
            </tr>
        </thead>
        <tbody>
            <?php if (count($tempos) > 0): ?>
                <?php foreach ($tempos as $index => $tempo): ?>
                    <tr class="
                        <?php 
                            if ($index == 0) echo 'gold'; // 1ª posição
                            elseif ($index == 1) echo 'pink'; // 2ª posição
                            elseif ($index == 2) echo 'blue'; // 3ª posição
                            elseif ($index == 3) echo 'yellow'; // 4ª posição
                            elseif ($index == 4) echo 'purple'; // 5ª posição
                        ?>
                    ">
                        <td class="ranking"><?= ($index + 1) ?>º</td>
                        <td><?= htmlspecialchars($tempo['username']) ?></td> <!-- Exibe o username -->
                        <td><?= htmlspecialchars($tempo['tempo_total_formatado']) ?></td>
                        <td><?= htmlspecialchars($tempo['total_pontos']) ?></td> <!-- Exibe a soma dos pontos -->
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">Nenhum tempo salvo encontrado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
