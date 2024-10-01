<?php
// Configurações do banco de dados
$host = 'localhost';
$dbname = 'tarefas_db';
$username = 'root'; // Mude para seu usuário
$password = 'root'; // Mude para sua senha

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

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['prova_selecionada'], $_POST['observacoes'], $_POST['pontos_ganhos'])) {
        $provaSelecionada = $_POST['prova_selecionada'];
        $observacoes = $_POST['observacoes'];
        $pontosGanhos = (int)$_POST['pontos_ganhos'];

        // Preparar a query de inserção para a tabela observacoes_tarefas
        $sql = "INSERT INTO observacoes_tarefas (user_id, prova_selecionada, observacoes, pontos_ganhos) 
                VALUES (:user_id, :prova_selecionada, :observacoes, :pontos_ganhos)";
        $stmt = $conn->prepare($sql);

        // Vincular os parâmetros e executar a query
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':prova_selecionada', $provaSelecionada, PDO::PARAM_STR);
        $stmt->bindParam(':observacoes', $observacoes, PDO::PARAM_STR);
        $stmt->bindParam(':pontos_ganhos', $pontosGanhos, PDO::PARAM_INT);

        if ($stmt->execute()) {
            
        } else {
            echo 'Erro ao salvar os dados.';
        }
    } else {
        echo 'Dados incompletos.';
    }
} else {
    
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Observações de Tarefas</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    
    <form action="" method="POST">
        <label for="prova_selecionada">Nome da Tarefa:</label><br>
        <input type="text" id="prova_selecionada" name="prova_selecionada" required><br><br>

        <label for="observacoes">Observações:</label><br>
        <textarea id="observacoes" name="observacoes" rows="4" cols="50" required></textarea><br><br>

        <label for="pontos_ganhos">Pontos Ganhos:</label><br>
        <input type="number" id="pontos_ganhos" name="pontos_ganhos" required><br><br>

        <input type="submit" value="Salvar Tarefa e Observações">
    </form>
</body>
</html>
