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
    // Configurar o PDO para lançar exceções
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
    // Verificar se todos os campos necessários estão presentes
    if (isset($_POST['horas'], $_POST['minutos'], $_POST['segundos'], $_POST['prova_selecionada'])) {
        $horas = $_POST['horas'];
        $minutos = $_POST['minutos'];
        $segundos = $_POST['segundos'];
        $provaSelecionada = $_POST['prova_selecionada']; // Obtendo a prova selecionada
        
        // Preparar a query de inserção
        $sql = "INSERT INTO tempos_tarefas (user_id, horas, minutos, segundos, prova_selecionada) VALUES (:user_id, :horas, :minutos, :segundos, :prova_selecionada)";
        $stmt = $conn->prepare($sql);

        // Vincular os parâmetros e executar a query
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':horas', $horas, PDO::PARAM_INT);
        $stmt->bindParam(':minutos', $minutos, PDO::PARAM_INT);
        $stmt->bindParam(':segundos', $segundos, PDO::PARAM_INT);
        $stmt->bindParam(':prova_selecionada', $provaSelecionada, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo 'Tempo e prova salvos com sucesso no banco de dados!';
        } else {
            echo 'Erro ao salvar o tempo e a prova.';
        }
    } else {
        echo 'Dados incompletos.';
    }
} else {
    echo 'Método inválido!';
}

