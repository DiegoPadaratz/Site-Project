<?php 
    include("../conexao.php");

    if(!isset($_SESSION)){
        session_start();

        if(!isset($_SESSION['id'])){
            die("<p>Faça seu <a href=\"../login.php\">login</a>.</p>");
        }

        $id = $_SESSION['id'];

        if(isset($_POST['yes'])){

            $query_user = $mysqli->prepare("SELECT * FROM users WHERE id=?") or die($mysqli->error);
            $query_user->bind_param("i", $id);
            $query_user->execute();
            $stmt = $query_user->get_result();
            $user = $stmt->fetch_assoc();

            $email = $user['email'];

            $query_files = $mysqli->prepare("SELECT * FROM files WHERE email=?") or die($mysqli->error);
            $query_files->bind_param("s", $email);
            $query_files->execute();
            $stmt = $query_files->get_result();
            
            while($file = $stmt->fetch_assoc()){
                unlink($file['path']);
            }

            $delete_files = $mysqli->prepare("DELETE FROM files WHERE email=?") or die($mysqli->error);
            $delete_files->bind_param("s", $email);

            if($delete_files->execute()){

                $delete_account = $mysqli->prepare("DELETE FROM users WHERE id=?") or die($mysqli->error);
                $delete_account->bind_param("i", $id);
                
                if($delete_account->execute()){
?>
                    <h1>Conta excluída com êxito, por favor, faça um novo login ou cadastre uma nova conta.</h1>
                    <nav><a href="../cadastro.php">Nova conta</a> | <a href="../login.php">Fazer login</a></nav>
<?php               
                    session_destroy();
                    die();
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
    <title>Excluir Conta</title>
</head>
<body>
    <header>
        <h1>Tem certeza que deseja excluir sua conta?</h1>
    </header>
    <main>
        <form action="" method="post">
            <p><label for="yes"><button type="submit" name="yes" value="1">Sim</button></label></p>
            <p><label for="no"><button><a href="index.php">Não</a></button></label></p>
        </form>
    </main>
</body>
</html>