
<?php
// Dados da conexão
$host = 'localhost';
$user = 'root'; // Seu nome de usuário do MySQL
$pass = 'root'; // Sua senha do MySQL (deixe vazio se não houver)
$dbname = 'tarefas_db'; // Nome do seu banco de dados

try {
    // Criação da conexão
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verifica se o formulário foi enviado
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Obtém os dados do formulário
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Verifica se o nome de usuário já existe
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE username = :username");
        $stmt->execute(['username' => $username]);

        if ($stmt->rowCount() > 0) {
            echo "Este nome de usuário já está em uso.";
        } else {
            // Cria o hash da senha
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Insere o novo usuário no banco de dados
            $stmt = $pdo->prepare("INSERT INTO usuarios (username, password) VALUES (:username, :password)");
            $stmt->execute(['username' => $username, 'password' => $hashedPassword]);

            echo "Usuário registrado com sucesso!";

            header("Location: login.php");
            exit(); // Sempre use exit após header() para evitar que o script continue a execução
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuário</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Registro de Usuário</h2>
        <form method="post" action="">
            <label for="username">Nome de Usuário:</label>
            <input type="text" name="username" required>
            <label for="password">Senha:</label>
            <input type="password" name="password" required>
            <button type="submit">Registrar</button>
        </form>

        <br>
        <div class="container-not">
            <a href="http://localhost/aplicativo_acampamento_controle_de_provas/login/login.php"><p>Já tem Cadastro ? Click aqui Geração</p></a>
        </div>
    </div>

    
</body>
</html>
