<?php
    if(!isset($_SESSION)){
        session_start();

        if(!isset($_SESSION['id'])){
            die("<p>Faça seu <a href=\"../login.php\">login</a>.</p>");
        }

        $id = $_SESSION['id'];

        include("../conexao.php");

        if(count($_POST) > 0){

            $error = false;

            $name = formatName($_POST['name']);
            $surname = formatName($_POST['surname']);
            $email = $_POST['email'];
            $birth = $_POST['birth'];
            $gender = "";

            //Validating Name
            if(empty($name)){
                $error = "<p class=\"error\">Preencha seu nome.</p>";
            }else if(strlen($name) <= 2 || strlen($name) > 50 || is_numeric($name)){
                $error = "<p class=\"error\">Seu nome deve conter entre 3-100 caracteres.</p>";
            }

            //Validating Surname
            if(empty($surname)){
                $error = "<p class=\"error\">Preencha seu sobrenome.</p>";
            }else if(strlen($surname) <= 2 || strlen($surname) > 50 || is_numeric($surname)){
                $error = "<p class=\"error\">Seu sobrenome deve conter entre 3-100 caracteres.</p>";
            }

            //Full Name
            $name = "$name " . $surname;

            //Validating E-mail
            if(empty($email)){
                $error = "<p class=\"error\">Preencha seu E-mail.</p>";
            }else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                $error = "<p class=\"error\">Preencha seu E-mail corretamente.</p>";
            }else{
                $query = $mysqli->prepare("SELECT * FROM users WHERE email=? AND id<>?") or die($mysqli->error);
                $query->bind_param("si", $email, $id);
                $query->execute();
                $query->store_result();

                if($query->num_rows() > 0){
                    $error = "<p class=\"error\">E-mail já está em uso.</p>";
                }
            }

            //Validating Birth
            if(empty($birth)){
                $error = "<p class=\"error\">Preencha sua data de nascimento.</p>";
            }else if(strlen($birth) != 10){
                $error = "<p class=\"error\">Preencha sua data de nascimento corretamente</p>";
            }else{
                $year = substr($birth, '0', '4');
                $month = substr($birth, '5', '2');
                $day = substr($birth, '8');

                if(!is_numeric($year) || !is_numeric($month) || !is_numeric($day)){
                    $error = "<p class=\"error\">Preencha sua data de nascimento corretamente</p>";
                }else{
                    $birth = explode("-", $birth);

                    if(count($birth) != 3){
                        $error = "<p class=\"error\">Preencha sua data de nascimento corretamente.</p>";
                    }else{
                        $birth = implode("-", $birth);
                    }
                }
            }

            //Validating Gender
            if(!empty($_POST['gender'])){
                $gender = $_POST['gender'];

                if($gender != "Masculino" && $gender != "Feminino"){
                    $error = "<p class=\"error\">Preencha seu gênero corretamente.</p>";
                }
            }else{
                $error = "<p class=\"error\">Preencha seu gênero.</p>";
            }

            if($error){
                echo "<p>$error</p>";
            }else{
                $now = currentDate();

                $sql_code = "UPDATE users SET name=?, email=?, birth=?, gender=? WHERE id=?";
                
                $query = $mysqli->prepare($sql_code) or die($mysqli->error);
                $query->bind_param("ssssi", $name, $email, $birth, $gender, $id);

                if($query->execute()){
                    echo "<p>Dados atualizados com sucesso! Volte ao <a href=\"index.php\">menu principal</a>.</p>";
                }
            }
        }
    }

    //Query for data user
    $query_user = $mysqli->prepare("SELECT * FROM users WHERE id=?") or die($mysqli->error);
    $query_user->bind_param("i", $id);
    $query_user->execute();
    $stmt = $query_user->get_result();
    $user = $stmt->fetch_assoc();

    $username = explode(" ", $user['name']);

    $name = $username[0];
    $surname = $username[1];
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
    <title>Cadastro</title>
</head>
<body>
    <header>
        <h1 class="titulo">Atualizar Dados</h1>
        <p>
            <nav><a href="index.php">Voltar</a></nav>
        </p>
    </header>
    <main>
        <form action="" method="post">
            <p>
                <label for="name">Nome </label><input type="text" name="name" value="<?=$name; ?>"><span class="error"> *</span>
            </p>
            <p>
                <label for="surname">Sobrenome </label><input type="text" name="surname" value="<?=$surname; ?>"><span class="error"> *</span>
            </p>
            <p>
                <label for="email">E-mail </label><input type="email" name="email" value="<?=$user['email']; ?>"><span class="error"> *</span>
            </p>
            <p>
                <label for="birth">Data de nascimento </label><input type="date" name="birth" value="<?=$user['birth']; ?>"><span class="error"> *</span>
            </p>
            <p>
                <label for="gender">Gênero </label><span class="error"> *</span>
                <input type="radio" name="gender" value="Masculino" <?php if(isset($user['gender'])){ echo ($user['gender'] == "Masculino") ? 'checked' : ''; }?>> Masculino
                <input type="radio" name="gender" value="Feminino" <?php if(isset($user['gender'])){ echo ($user['gender'] == "Feminino") ? 'checked' : ''; }?>> Feminino
            </p>
            <p>
                <button type="submit">Atualizar</button>
            </p>
        </form>
    </main>
</body>
</html>