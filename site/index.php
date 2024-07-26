<?php 
    include("../conexao.php");

    //Validation Session
    if(!isset($_SESSION)){
        session_start();

        if(!isset($_SESSION['id'])){
            die("<p>Faça seu <a href=\"../login.php\">login</a>.</p>");
        }
    }

        $id = $_SESSION['id'];

        $query = $mysqli->prepare("SELECT * FROM users WHERE id=?") or die($mysqli->error);
        $query->bind_param("i", $id);
        $query->execute();
        $stmt = $query->get_result();

        $user = $stmt->fetch_assoc();

        function sendFile($error, $size, $name, $tmp_name, $id){

            include("../connection.php");

            $query = $mysqli->prepare("SELECT * FROM users WHERE id=?") or die($mysqli->error);
            $query->bind_param("i", $id);
            $query->execute();
            $stmt = $query->get_result();
    
            $user = $stmt->fetch_assoc();

            //If occur any error
            if($error){
                die("<p>Erro ao enviar arquivo. <a href=\"index.php\">Tente novamente</a>.</p>");
            }

            //If file is too big
            if($size > 2097152){
                die("<p>O arquivo excede 2MB, <a href=\"index.php\">escolha um outro arquivo</a>.</p>");
            }

            //Folder where files will be saved
            $folder = "files/";

            $original_name = $name;
            $random_name = uniqid();

            $extension = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));

            if($extension != "jpg" && $extension != "png" && $extension != "jpeg" && $extension != "zip" && $extension != "pdf"){
                die("<p>Extensão de arquivo não permitida.</p>");
            }

            $path = $folder . $random_name . "." . $extension;

            $tmp = move_uploaded_file($tmp_name, $path);

            if($tmp){
                $client = $user['name'];
                $email = $user['email'];
                $now = currentDate();
                $query = $mysqli->prepare("INSERT INTO files (original_name, random_name, path, user, email, date) VALUES (?, ?, ?, ?, ?, ?)") or die($mysqli->error);
                $query->bind_param("ssssss", $original_name, $random_name, $path, $client, $email, $now);
                $query->execute();

                if($query){
                    return true;
                }
            }
        }

    if(isset($_FILES['file'])){
        
        $file = $_FILES['file'];

        $itswork = true;

        foreach($file['name'] as $index => $arq){
            $everything_ok = sendFile($file['error'][$index], $file['size'][$index], $file['name'][$index], $file['tmp_name'][$index], $id);
            if(!$everything_ok){
                $itswork = false;
            }
        }
        if($everything_ok){
            echo "<p>Arquivos enviados com sucesso!</p>";
        }else{
            echo "<p>Falha ao enviar arquivos.</p>";
        }
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site</title>
</head>
<body>
    <header>
        <h1>Bem-vindo <?=$user['name'];?></h1>
        <nav><a href="logout.php">Finalizar Sessão</a> | <a href="delete-account.php">Excluir conta</a> | <a href="my-account.php">Conta</a></nav><hr><br>
    </header>
    <main>
        <h2>Envie um novo arquivo</h2>
        <form enctype="multipart/form-data" action="" method="post">
            <p>
                <label for="file"><input multiple type="file" name="file[]"></label>
            </p>
            <p>
                <button type="submit">Enviar</button>
            </p>
        </form>
        <?php 
            $client = $user['name'];
            $email = $user['email'];
            $query = $mysqli->prepare("SELECT * FROM files WHERE user=? AND email=?") or die($mysqli->error);
            $query->bind_param("ss", $client, $email);
            $query->execute();
            $stmt = $query->get_result();
        ?>
        <h2>Arquivos Enviados por você</h2>
        <table cellpadding="5" border="1">
            <thead>
                <tr>
                    <td><strong>Nome</strong></td>
                    <td><strong>Data de Envio</strong></td>
                    <td><strong><button><a href="delete.php">Excluir Tudo</a></button></strong></td>
                </tr>
            </thead>
            <tbody>
                <?php 
                    while($file = $stmt->fetch_assoc()){
                ?>
                <tr>
                    <td><?=$file['original_name']; ?></td>
                    <td><?=formatDate($file['date']); ?></td>
                    <td>
                        <p>
                            <button><a href="<?=$file['path']; ?>" download="<?=$file['random_name']; ?>">Baixar</a></button>
                            <button><a href="deleteit.php?id=<?=$file['id']; ?>">Excluir</a></button><br>
                        </p>
                    </td>
                </tr>
                <?php 
                    }
                ?>
            </tbody>
        </table>
    </main>
</body>
</html>