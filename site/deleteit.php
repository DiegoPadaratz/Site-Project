<?php 
    include("../conexao.php");

    if(!isset($_SESSION)){
        session_start();
        
        if(!isset($_SESSION['id'])){
            die("<p>Faça seu <a href=\"../login.php\">login</a>.</p>");
        }

        $id = intval($_GET['id']);

        //Query from files
        $query = $mysqli->prepare("SELECT * FROM files WHERE id=?") or die($mysqli->error);
        $query->bind_param("i", $id);
        $query->execute();
        $stmt = $query->get_result();
        $file = $stmt->fetch_assoc();

        if(isset($_POST['yes'])){

            //Deleting file path in database by id
            $deleting_file = $mysqli->prepare("DELETE FROM files WHERE id=?") or die($mysqli->error);
            $deleting_file->bind_param("i", $id);
            $deleting_file->execute();

            //Deleting file from server
            unlink($file['path']);

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
    <title>Excluir Arquivo</title>
</head>
<body>
    <header>
        <h1>Quer mesmo excluir o arquivo "<?=$file['original_name']; ?>"?</h1>
    </header>
    <main>
        <form action="" method="post">
            <p><label for="yes"><button type="submit" name="yes" value="1">Sim</button></label></p>
            <p><label for="no"><button><a href="index.php">Não</a></button></label></p>
        </form>
    </main>
</body>
</html>