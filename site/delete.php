<?php 
    include("../conexao.php");

    if(!isset($_SESSION)){
        session_start();
        
        if(!isset($_SESSION['id'])){
            die("<p>Faça seu <a href=\"../login.php\">login</a>.</p>");
        }

        $id = $_SESSION['id'];

        if(isset($_POST['yes'])){

            //Query for users
            $query = $mysqli->prepare("SELECT * FROM users WHERE id=?") or die($mysqli->error);
            $query->bind_param("i", $id);
            $query->execute();
            $stmt = $query->get_result();
            $client = $stmt->fetch_assoc();

            //Name and E-mail from User
            $name = $client['name'];
            $email = $client['email'];

            //Query for User files
            $query_files = $mysqli->prepare("SELECT * FROM files WHERE user=? AND email=?") or die($mysqli->error);
            $query_files->bind_param("ss", $name, $email);
            $query_files->execute();
            $stmt = $query_files->get_result();

            //While fetch_assoc() delete each file path
            while($files = $stmt->fetch_assoc()){
                unlink($files['path']);
            }

            //Deleting every file from this user
            $deleting_file = $mysqli->prepare("DELETE FROM files WHERE user=? AND email=?") or die($mysqli->error);
            $deleting_file->bind_param("ss", $name, $email);
            $deleting_file->execute();

            if($deleting_file){
?>
                <h1>Arquivo deletado com sucesso!</h1>
                <nav>Clique <a href="index.php">aqui</a> para voltar.</nav>
<?php 
                die();
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Tudo</title>
</head>
<body>
    <header>
        <h1>Deseja mesmo excluir todos os arquivos?</h1>
    </header>
    <main>
        <form action="" method="post">
            <p>
                <label for="yes"><button type="submit" name="yes" value="1">Sim</button></label>
            </p>
            <p>
                <label for="no"><a href="index.php">Não</a></label>
            </p>
        </form>
    </main>
</body>
</html>