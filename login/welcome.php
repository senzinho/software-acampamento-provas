<?php
session_start();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Página de Boas-Vindas</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Bem-vindo, <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Usuário'; ?>!</h1>



    <a href="logout.php" class="btn">Logoff</a>
</body>
</html>
