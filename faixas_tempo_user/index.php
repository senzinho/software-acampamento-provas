<?php
// Configurações do banco de dados
$host = 'localhost';
$dbname = 'tarefas_db';
$username = 'root';
$password = 'root';

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
if (!isset($_SESSION['username'])) {
    die("Usuário não autenticado.");
}

// Obter o username da sessão
$username = $_SESSION['username'];

// Recuperar o user_id correspondente ao username
$sql = "SELECT id FROM usuarios WHERE username = :username";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':username', $username, PDO::PARAM_STR);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Verificar se o usuário foi encontrado
if (!$user) {
    die("Usuário não encontrado.");
}

// Obter o ID do usuário da consulta
$user_id = $user['id'];

// Verificar se o formulário foi submetido para salvar os pontos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pontos'], $_POST['tempo_id'])) {
    $pontos = (int)$_POST['pontos'];
    $tempo_id = (int)$_POST['tempo_id'];

    // Atualizar os pontos na tabela tempos_tarefas
    $sql = "UPDATE tempos_tarefas SET pontos = :pontos WHERE id = :tempo_id AND user_id = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':pontos', $pontos, PDO::PARAM_INT);
    $stmt->bindParam(':tempo_id', $tempo_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    // Define uma mensagem de sucesso na sessão
    $_SESSION['message'] = "Dados salvos com sucesso!";
}

// Recuperar os dados da tabela, ordenando pelos tempos mais recentes primeiro
$sql = "SELECT * FROM tempos_tarefas WHERE user_id = :user_id ORDER BY data_hora DESC";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$tempos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Função para calcular o tempo total de todas as provas
function calcularTempoTotal($tempos) {
    $totalSegundos = 0;
    foreach ($tempos as $tempo) {
        list($horas, $minutos, $segundos) = explode(':', $tempo['tempo_formatado']);
        $totalSegundos += ($horas * 3600) + ($minutos * 60) + $segundos;
    }
    $horas = floor($totalSegundos / 3600);
    $resto = $totalSegundos % 3600;
    $minutos = floor($resto / 60);
    $segundos = $resto % 60;
    return sprintf('%02d:%02d:%02d', $horas, $minutos, $segundos);
}

$tempoTotal = calcularTempoTotal($tempos);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Pontos</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include '../menu.php'; ?>

    <div class="container">
        <h1>Tempos Salvos</h1>
        <p class="total"><strong>Tempo Total de todas as Provas:</strong> <?= htmlspecialchars($tempoTotal) ?></p>

        <?php if (isset($_SESSION['message'])): ?>
            <script>
                alert("<?= addslashes($_SESSION['message']) ?>"); // Exibe o alerta
            </script>
            <?php unset($_SESSION['message']); // Limpa a mensagem após exibi-la ?>
        <?php endif; ?>

        <?php if (count($tempos) > 0): ?>
            <?php foreach ($tempos as $tempo): ?>
                <div class="faixa">
                    <span><strong>Equipe:</strong> <?= htmlspecialchars($username) ?></span>
                    <span><strong>Tempo:</strong> <?= htmlspecialchars($tempo['tempo_formatado']) ?></span>
                    <span><strong>Prova:</strong> <?= htmlspecialchars($tempo['prova_selecionada']) ?></span>
                    <div class="pontos">
                        <button class="btn-alterar" onclick="alterarPontos(<?= $tempo['id'] ?>, -1)">-</button>
                        <input type="text" class="input-pontos" id="input_pontos_<?= $tempo['id'] ?>" value="<?= htmlspecialchars($tempo['pontos']) ?>">
                        <button class="btn-alterar" onclick="alterarPontos(<?= $tempo['id'] ?>, 1)">+</button>
                        <form method="post" action="">
                            <input type="hidden" name="tempo_id" value="<?= htmlspecialchars($tempo['id']) ?>">
                            <input type="hidden" name="pontos" id="pontos_<?= $tempo['id'] ?>" value="<?= htmlspecialchars($tempo['pontos']) ?>">
                            <button type="submit" class="btn-salvar">Salvar</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-tempos">Nenhum tempo salvo até o momento.</p>
        <?php endif; ?>
    </div>

    <script>
        function alterarPontos(tempoId, incremento) {
            var input = document.getElementById(`input_pontos_${tempoId}`); // Seleciona o input correto
            var pontos = parseInt(input.value) || 0; // Pega o valor atual ou 0 se não for um número
            pontos += incremento; // Aplica o incremento
            input.value = Math.max(0, pontos); // Impede que o valor fique negativo
            document.getElementById(`pontos_${tempoId}`).value = input.value; // Atualiza o valor do campo oculto
        }
    </script>
</body>
</html>
