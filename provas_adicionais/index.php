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

// Variáveis para armazenar mensagens e total de pontos
$message = '';
$totalPontos = 0;

// Consultar a soma total dos pontos ganhos pelo usuário inicialmente
$sqlTotal = "SELECT SUM(pontos_ganhos) as total_pontos FROM observacoes_tarefas WHERE user_id = :user_id";
$stmtTotal = $conn->prepare($sqlTotal);
$stmtTotal->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmtTotal->execute();
$result = $stmtTotal->fetch(PDO::FETCH_ASSOC);
$totalPontos = $result['total_pontos'] ? $result['total_pontos'] : 0; // Verifica se há pontos

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['prova_selecionada'], $_POST['observacoes'], $_POST['pontos_ganhos'])) {
        $provaSelecionada = $_POST['prova_selecionada'];
        $observacoes = $_POST['observacoes'];
        $pontosGanhos = (int)$_POST['pontos_ganhos'];

        // Preparar a query de inserção
        $sql = "INSERT INTO observacoes_tarefas (user_id, prova_selecionada, observacoes, pontos_ganhos) 
                VALUES (:user_id, :prova_selecionada, :observacoes, :pontos_ganhos)";
        $stmt = $conn->prepare($sql);

        // Vincular os parâmetros e executar a query
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':prova_selecionada', $provaSelecionada, PDO::PARAM_STR);
        $stmt->bindParam(':observacoes', $observacoes, PDO::PARAM_STR);
        $stmt->bindParam(':pontos_ganhos', $pontosGanhos, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $message = 'Prova e observações salvas com sucesso';

            // Atualizar o total de pontos
            $totalPontos += $pontosGanhos; // Adiciona os pontos ganhos aos pontos totais
        } else {
            $message = 'Erro ao salvar os dados.';
        }
    } else {
        $message = 'Dados incompletos.';
    }
}

// Consultar as provas e suas pontuações
$sqlProvas = "SELECT prova_selecionada, observacoes, pontos_ganhos FROM observacoes_tarefas WHERE user_id = :user_id";
$stmtProvas = $conn->prepare($sqlProvas);
$stmtProvas->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmtProvas->execute();
$provas = $stmtProvas->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Observações de Provas</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php
// Incluindo o menu
include '../menu.php';
?>

<div class="pontos">
    Total de Pontos:  <strong><?php echo $totalPontos; ?></strong>
</div>

<h2>Adicionar Observações de Provas</h2>

<form action="" method="POST">
    <label for="prova_selecionada">Nome da Prova:</label><br>
    <input type="text" id="prova_selecionada" name="prova_selecionada" required><br><br>

    <label for="observacoes">Observações:</label><br>
    <textarea id="observacoes" name="observacoes" rows="4" cols="50" required></textarea><br><br>

    <label for="pontos_ganhos">Pontos Ganhos:</label><br>
    <input type="number" id="pontos_ganhos" name="pontos_ganhos" required><br><br>

    <input type="submit" value="Salvar Informações">
</form>

<?php if ($message): ?>
    <p><?php echo $message; ?></p>
<?php endif; ?>

<h2>Provas Adicionadas</h2>
<table>
    <thead>
        <tr>
            <th>Nome da Prova</th>
            <th>Observações</th>
            <th>Pontos Ganhos</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($provas as $prova): ?>
        <tr>
            <td data-label="Nome da Prova"><?php echo htmlspecialchars($prova['prova_selecionada']); ?></td>
            <td data-label="Observações"><?php echo htmlspecialchars($prova['observacoes']); ?></td>
            <td data-label="Pontos Ganhos"><?php echo htmlspecialchars($prova['pontos_ganhos']); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
