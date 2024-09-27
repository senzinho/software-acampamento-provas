<?php
// Dados da conexão
$host = 'localhost';
$user = 'root'; // Seu nome de usuário do MySQL
$pass = 'root'; // Sua senha do MySQL (deixe vazio se não houver)
$dbname = 'tarefas_db'; // Nome do seu banco de dados

session_start(); // Inicia a sessão

// Verifica se o usuário está logado
if (isset($_SESSION['user_id'])) {
    // Usuário já está logado, pode redirecionar para a página inicial ou dashboard
    echo "Bem-vindo, " . htmlspecialchars($_SESSION['username']) . "!";
    echo '<br><a href="logout.php" class="btn">Logoff</a>'; // Botão de logoff
    exit; // Para evitar que o restante do código seja executado
}

try {
    // Criação da conexão
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verifica se o formulário foi enviado
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Obtém os dados do formulário
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Prepara e executa a consulta
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verifica se o usuário existe e se a senha está correta
        if ($user && password_verify($password, $user['password'])) {
            // Armazena as informações do usuário na sessão
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            
            header("Location: ../index.php");
            exit;
        } else {
            echo "Nome de usuário ou senha incorretos.";
        }
    }
} catch (PDOException $e) {
    echo "Erro de conexão: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <form method="POST" action="">
        <label for="username">Usuário:</label>
        <input type="text" name="username" required>
        <label for="password">Senha:</label>
        <input type="password" name="password" required>
        <button type="submit">Login</button>
    </form>
</body>
</html>
