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
    die("Erro de conexão: " . $e->getMessage());
}

// Verificar se o POST contém pontos e tempo_id
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pontos'], $_POST['tempo_id'])) {
    $pontos = (int)$_POST['pontos'];
    $tempo_id = (int)$_POST['tempo_id'];

    // Atualizar os pontos na tabela tempos_tarefas
    $sql = "UPDATE tempos_tarefas SET pontos = :pontos WHERE id = :tempo_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':pontos', $pontos, PDO::PARAM_INT);
    $stmt->bindParam(':tempo_id', $tempo_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
}
?>
