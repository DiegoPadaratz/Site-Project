<?php 
    include("../conexao.php");

    if(!isset($_SESSION)){
        session_start();

        if(!isset($_SESSION['id'])){
            die("<p>Faça seu <a href=\"../login.php\">login</a>.</p>");
        }

        $id = $_SESSION['id'];

        $query_user = $mysqli->prepare("SELECT * FROM users WHERE id=?") or die($mysqli->error);
        $query_user->bind_param("i", $id);
        $query_user->execute();
        $stmt = $query_user->get_result();
        $user = $stmt->fetch_assoc();

        $user_password = $user['password'];

        if(count($_POST) > 0){

            $error = false;

            $old = $_POST['old_password'];
            $new = $_POST['new_password'];
            $confirm = $_POST['pass_confirm'];

            //Validating old password
            if(empty($old)){
                $error = "<p class=\"error\">Preencha a senha atual.</p>";
            }else if(!password_verify($old, $user_password)){
                $error = "<p class=\"error\">A senha atual está incorreta.</p>";
            }

            //Validating new password
            if(empty($new)){
                $error = "<p class=\"error\">Coloque uma nova senha.</p>";
            }else if(strlen($new) < 8 || strlen($new) > 30){
                $error = "<p class=\"error\">A nova senha deve conter entre 8-30 caracteres.</p>";
            }else if($new == $old){
                $error = "<p class=\"error\">A nova senha não pode ser igual à antiga.</p>";
            }else if(empty($confirm)){
                $error = "<p class=\"error\">Confirme a nova senha.</p>";
            }else if($new != $confirm){
                $error = "<p class=\"error\">Confirme a nova senha corretamente.</p>";
            }else{
                $password = password_hash($new, PASSWORD_DEFAULT);
            }

            if($error){
                echo "<p>$error</p>";
            }else{
                $query = $mysqli->prepare("UPDATE users SET password=? WHERE id=?") or die($mysqli->error);
                $query->bind_param("si", $password, $id);
                
                if($query->execute()){
                    echo "<p>Senha atualizada com sucesso! Volte ao <a href=\"index.php\">menu principal</a>.</p>";
                    unset($_POST);
                }
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .error{
            color: red;
        }
    </style>
    <title>Alterar Senha</title>
</head>
<body>
    <header>
        <h1>Altere sua senha</h1>
        <p><nav><a href="index.php">Voltar</a></nav></p>
    </header>
    <main>
        <form action="" method="post">
        <p>
            <label for="old_password">Digite a senha atual </label><input type="password" name="old_password" value="<?php if(isset($_POST['old_password'])) echo $_POST['old_password']; ?>">
        </p>
        <p>
            <label for="new_password">Digite a nova senha </label><input type="password" name="new_password" value="<?php if(isset($_POST['new_password'])) echo $_POST['new_password']; ?>">
        </p>
        <p>
            <label for="pass_confirm">Confirme a nova senha </label><input type="password" name="pass_confirm" value="<?php if(isset($_POST['pass_confirm'])) echo $_POST['pass_confirm']; ?>">
        </p>
        <p>
            <button type="submit">Alterar</button>
        </p>
        </form>
    </main>
</body>
</html>