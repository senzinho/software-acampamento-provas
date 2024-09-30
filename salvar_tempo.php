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
        $horas = (int)$_POST['horas'];
        $minutos = (int)$_POST['minutos'];
        $segundos = (int)$_POST['segundos'];
        $provaSelecionada = $_POST['prova_selecionada'];

        // Formatar o tempo da prova no padrão HH:MM:SS
        $tempo_formatado = sprintf('%02d:%02d:%02d', $horas, $minutos, $segundos);

        // Consultar todos os tempos do usuário para somar
        $sql = "SELECT tempo_formatado FROM tempos_tarefas WHERE user_id = :user_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        // Inicializar o tempo total
        $tempo_total = '00:00:00';

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Somar cada tempo formatado ao tempo total
            $tempo_total = somarTempos($tempo_total, $row['tempo_formatado']);
        }

        // Somar o tempo da nova prova ao tempo total
        $tempo_total = somarTempos($tempo_total, $tempo_formatado);

        // Preparar a query de inserção
        $sql = "INSERT INTO tempos_tarefas (user_id, tempo_formatado, tempo_total_formatado, prova_selecionada) VALUES (:user_id, :tempo_formatado, :tempo_total_formatado, :prova_selecionada)";
        $stmt = $conn->prepare($sql);

        // Vincular os parâmetros e executar a query
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':tempo_formatado', $tempo_formatado, PDO::PARAM_STR);
        $stmt->bindParam(':tempo_total_formatado', $tempo_total, PDO::PARAM_STR);
        $stmt->bindParam(':prova_selecionada', $provaSelecionada, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo 'Tempo e prova salvos com sucesso no banco de dados! Tempo total atualizado: ' . $tempo_total;
        } else {
            echo 'Erro ao salvar o tempo e a prova.';
        }
    } else {
        echo 'Dados incompletos.';
    }
} else {
    echo 'Método inválido!';
}

// Função para somar tempos no formato HH:MM:SS
function somarTempos($tempo1, $tempo2) {
    list($h1, $m1, $s1) = explode(':', $tempo1);
    list($h2, $m2, $s2) = explode(':', $tempo2);

    // Calcular a soma
    $novo_segundos = $s1 + $s2;
    $novo_minutos = $m1 + $m2;
    $novo_horas = $h1 + $h2;

    // Ajustar se os segundos ultrapassarem 60
    if ($novo_segundos >= 60) {
        $novo_minutos += floor($novo_segundos / 60);
        $novo_segundos = $novo_segundos % 60;
    }

    // Ajustar se os minutos ultrapassarem 60
    if ($novo_minutos >= 60) {
        $novo_horas += floor($novo_minutos / 60);
        $novo_minutos = $novo_minutos % 60;
    }

    // Formatar o novo tempo total
    return sprintf('%02d:%02d:%02d', $novo_horas, $novo_minutos, $novo_segundos);
}
