<?php
// Configurações do banco de dados
$host = 'localhost';
$dbname = 'tarefas_db';
$username = 'root';
$password = 'root';

// Conectar ao banco de dados
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(['success' => false, 'message' => 'Erro de conexão: ' . $e->getMessage()]));
}

// Verificar se os dados foram enviados corretamente
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['pontos'])) {
    $id = (int)$_POST['id'];
    $pontos = (int)$_POST['pontos'];

    // Atualizar os pontos na tabela tempos_tarefas
    $sql = "UPDATE tempos_tarefas SET pontos = :pontos WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':pontos', $pontos, PDO::PARAM_INT);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao atualizar pontos.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos.']);
}
?>
