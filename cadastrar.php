<?php
    session_start();
    if(isset($_SESSION["email"]) && isset($_SESSION["senha"])){
        header("Location:gerenciar.php");
        exit;
    }else{
        require_once("_php/conexao.php");
    }

    if(isset($_POST["cadastrar"])){
          $nome = $_POST["nome"];
          $email = $_POST["email"];
          $senha = md5($_POST["senha"]);
          $busca_usuario = $pdo->prepare("SELECT COUNT(*) AS 'existe'FROM usuarios WHERE email = :email");
          $busca_usuario->bindValue(":email",$email);
          $busca_usuario->execute();
          $busca_usuario = $busca_usuario->fetch();
          $busca_usuario = $busca_usuario["existe"];

          if($busca_usuario < 1){
              $insert_usuario = $pdo->prepare("INSERT INTO usuarios VALUE (NULL,:nome,:email,:senha)");
              $insert_usuario->bindValue(":nome",$nome);
              $insert_usuario->bindValue(":email",$email);
              $insert_usuario->bindValue(":senha",$senha);
              if($insert_usuario->execute()){
                $_SESSION["email"] = $email;
                $_SESSION["senha"] = $senha;
                header("Location:gerenciar.php");
              }
          }
      }

 ?>

<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="author" content="Mateus Soares da Silva">
    <title>Teste Voxus | Cadastrar</title>
    <!--<link rel="shortcut icon" href="_imgs/logo5.png" type="image/x-icon" />-->
    <link href="_css/estilo.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="_scripts/jquery.js"></script>
    <script type="text/javascript" src="_scripts/script.js"></script>
</head>
<body>
  <div id="container_login">
      <h1>Voxus</h1>
      <h2>Cadastre-se</h2>
      <form method="post">
        <input type="text" placeholder="Nome:" name="nome"/>
          <input type="email" placeholder="Email:" name="email"/>
          <input type="password" placeholder="Senha:" name="senha"/>
          <input class="botoes" type="submit" value="cadastrar" name="cadastrar"/>
          <?php
              if(isset($busca_usuario) && $busca_usuario >0){
                  echo "<p id='mensagem'>Email já está cadastrado.</p>";
              }
          ?>
      </form>
  </div>
</body>
</html
