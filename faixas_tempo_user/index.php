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
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php
// Incluindo o menu
include '../menu.php';
?>
    <div class="container">
        <h1>Tempos Salvos</h1>

        <!-- Mostrar o tempo total -->
        <p class="total"><strong>Tempo Total de todas as Provas:</strong> <?= htmlspecialchars($tempoTotal) ?></p>

        <?php if (count($tempos) > 0): ?>
            <?php foreach ($tempos as $tempo): ?>
                <div class="faixa">
                    <span><strong>Equipe:</strong> <?= htmlspecialchars($username) ?></span>
                    <span><strong>Tempo:</strong> <?= htmlspecialchars($tempo['tempo_formatado']) ?></span>
                    <span><strong>Prova Selecionada:</strong> <?= htmlspecialchars($tempo['prova_selecionada']) ?></span>
                    <span><strong>Data e Hora:</strong> <?= htmlspecialchars($tempo['data_hora']) ?></span>

                    <!-- Campo para inserir a quantidade de pontos -->
                    <div class="pontos">
                        <label for="pontos_<?= $tempo['id'] ?>"><strong>Pontos:</strong></label>
                        <input type="number" id="pontos_<?= $tempo['id'] ?>" name="pontos" value="<?= htmlspecialchars($tempo['pontos']) ?>" class="input-pontos" disabled>
                        <button class="btn-editar" onclick="habilitarEdicao(<?= $tempo['id'] ?>)">Editar</button>
                        <button class="btn-salvar" onclick="salvarPontos(<?= $tempo['id'] ?>)">Salvar</button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-tempos">Nenhum tempo salvo encontrado.</p>
        <?php endif; ?>
    </div>

    <script>
        function habilitarEdicao(id) {
            document.getElementById('pontos_' + id).disabled = false;
        }

        function salvarPontos(id) {
            var pontos = document.getElementById('pontos_' + id).value;

            // Fazer requisição AJAX para salvar os pontos no banco de dados
            fetch('salvar_pontos.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id=${id}&pontos=${pontos}` // Enviar dados via POST
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Pontos salvos com sucesso!');
                    document.getElementById('pontos_' + id).disabled = true;
                } else {
                    alert('Erro ao salvar pontos.');
                }
            })
            .catch(error => console.error('Erro:', error));
        }

    </script>
</body>
</html>
