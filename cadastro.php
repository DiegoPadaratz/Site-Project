<?php
    include("conexao.php");

    if(count($_POST) > 0){

        $error = false;

        $name = formatName($_POST['name']);
        $surname = formatName($_POST['surname']);
        $email = $_POST['email'];
        $birth = $_POST['birth'];
        $gender = "";
        $password = $_POST['password'];
        $pass_confirm = $_POST['pass_confirm'];

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
            $query = $mysqli->prepare("SELECT * FROM users WHERE email=?") or die($mysqli->error);
            $query->bind_param("s", $email);
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

        //Validating Password
        if(empty($password)){
            $error = "<p class=\"error\">Coloque uma senha.</p>";
        }else if(strlen($password) < 8 || strlen($password) > 30){
            $error = "<p class=\"error\">Sua senha deve conter entre 8-30 caracteres.</p>";
        }else{
            if(empty($pass_confirm)){
                $error = "<p class=\"error\">Confirme sua senha.</p>";
            }else if($pass_confirm != $password){
                $error = "<p class=\"error\">A senhas não coincidem, confirme sua senha corretamente.</p>";
            }else{
                $password = password_hash($password, PASSWORD_DEFAULT);
            }
        }

        if($error){
            echo "<p>$error</p>";
        }else{
            $now = currentDate();

            $sql_code = "INSERT INTO users (name, email, birth, gender, password, date) VALUES (?, ?, ?, ?, ?, ?)";
            
            $query = $mysqli->prepare($sql_code) or die($mysqli->error);
            $query->bind_param("ssssss", $name, $email, $birth, $gender, $password, $now);

            if($query->execute()){
                header("Location: login.php");
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
    <title>Cadastro</title>
</head>
<body>
    <header>
        <h1 class="titulo">Cadastre-se</h1>
        <small>
            <p>Já possuí cadastro? Faça seu <a href="login.php">login</a>.</p>
        </small>
    </header>
    <main>
        <form action="" method="post">
            <p>
                <label for="name">Nome </label><input type="text" name="name" value="<?php if(isset($_POST['name'])) echo $_POST['name']; ?>"><span class="error"> *</span>
            </p>
            <p>
                <label for="surname">Sobrenome </label><input type="text" name="surname" value="<?php if(isset($_POST['surname'])) echo $_POST['surname']; ?>"><span class="error"> *</span>
            </p>
            <p>
                <label for="email">E-mail </label><input type="email" name="email" value="<?php if(isset($_POST['email'])) echo $_POST['email']; ?>"><span class="error"> *</span>
            </p>
            <p>
                <label for="birth">Data de nascimento </label><input type="date" name="birth" value="<?php if(isset($_POST['birth'])) echo $_POST['birth']; ?>"><span class="error"> *</span>
            </p>
            <p>
                <label for="gender">Gênero </label><span class="error"> *</span>
                <input type="radio" name="gender" value="Masculino" <?php if(isset($_POST['gender'])){ echo ($gender == "Masculino") ? 'checked' : ''; }?>> Masculino
                <input type="radio" name="gender" value="Feminino" <?php if(isset($_POST['gender'])){ echo ($gender == "Feminino") ? 'checked' : ''; }?>> Feminino
            </p>
            <p>
                <label for="password">Senha </label><input type="password" name="password" value="<?php if(isset($_POST['password'])) echo $_POST['password']; ?>"><span class="error"> *</span>
            </p>
            <p>
                <label for="pass_confirm">Confirmar senha </label><input type="password" name="pass_confirm" value="<?php if(isset($_POST['pass_confirm'])) echo $_POST['pass_confirm']; ?>"><span class="error"> *</span>
            </p>
            <p>
                <button type="submit">Cadastrar</button>
            </p>
        </form>
    </main>
</body>
</html>