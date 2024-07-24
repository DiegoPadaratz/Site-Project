<?php
    include("conexao.php");

    if(count($_POST) > 0){

        $error = false;

        $email = $_POST['email'];
        $password = $_POST['password'];

        //Validating E-mail
        if(empty($email)){
            $error = "<p class=\"error\">Preencha seu E-mail.</p>";
        }else if(empty($password)){
            $error = "<p class=\"error\">Preencha sua senha</p>";
        }else{
            $query = $mysqli->prepare("SELECT * FROM users WHERE email=?");
            $query->bind_param("s", $email);
            $query->execute();
            $query->store_result();

            if($query->num_rows() == 0){
                $error = "<p class=\"error\">E-mail não cadastrado.</p>";
            }else{

                $query = $mysqli->prepare("SELECT * FROM users WHERE email=?");
                $query->bind_param("s", $email);
                $query->execute();
                $stmt = $query->get_result();

                $user = $stmt->fetch_assoc();

                if(!password_verify($password, $user['password'])){
                    $error = "<p class=\"error\">E-mail ou senha incorretos.</p>";
                }else{
                    if(!isset($_SESSION)){
                        session_start();

                        $_SESSION['id'] = $user['id'];

                        header("Location: site/index.php");
                    }
                }
            }
        }

        if($error){
            echo "<p>$error</p>";
        }else{

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
    <title>Login</title>
</head>
<body>
    <header>
        <h1>Faça seu login</h1>
        <small>
            <p>Não possuí cadastro? Faça seu <a href="cadastro.php">cadastro</a>.</p>
        </small>
    </header>
    <main>
        <form action="" method="post">
            <p>
                <label for="email">E-mail </label><input type="email" name="email" value="<?php ?>"><span class="error"> *</span>
            </p>
            <p>
                <label for="password">Senha </label><input type="password" name="password" value="<?php ?>"><span class="error"> *</span>
            </p>
            <p>
                <button type="submit">Cadastrar</button>
            </p>
        </form>
    </main>
</body>
</html>