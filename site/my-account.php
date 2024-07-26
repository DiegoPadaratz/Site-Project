<?php 
    include("../conexao.php");

    if(!isset($_SESSION)){

        session_start();

        if(!isset($_SESSION['id'])){
            die("<p>Faça seu <a href=\"../login.php\">login</a>.</p>");
        }

        $id = $_SESSION['id'];

        //Query user by id
        $query_user = $mysqli->prepare("SELECT * FROM users WHERE id=?") or die($mysqli->error);
        $query_user->bind_param("i", $id);
        $query_user->execute();
        $stmt = $query_user->get_result();
        $user = $stmt->fetch_assoc();
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seus Dados</title>
</head>
<body>
    <header>
        <h1>Seus Dados</h1>
        <p><nav><a href="index.php">Voltar</a></nav></p>
    </header>
    <main>
        <ul>
            <li><strong>Nome:</strong> <?=$user['name']; ?></li><br>
            <li><strong>E-mail:</strong> <?=$user['email']; ?></li><br>
            <li><strong>Data de Nascimento:</strong> <?=formatBirth($user['birth']); ?></li><br>
            <li><strong>Gênero:</strong> <?=$user['gender']; ?></li><br>
            <li><strong>Data de Cadastro:</strong> <?=formatDate($user['date']); ?></li><br>
            <li><a href="edit.php">Editar</a></li><br>
            <li><a href="new-password.php">Alterar senha</a></li><br>
        </ul>
    </main>
</body>
</html>