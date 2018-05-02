<?php
    session_start();
    if(isset($_SESSION["email"]) && isset($_SESSION["senha"])){
        header("Location:gerenciar.php");
        exit;
    }else{
        require_once("_php/conexao.php");
    }
    if(isset($_POST["entrar"])){
          $email = $_POST["email"];
          $senha = md5($_POST["senha"]);
          $busca_usuario = $pdo->prepare("SELECT COUNT(*) AS 'existe'FROM usuarios WHERE email = :email and senha = :senha");
          $busca_usuario->bindValue(":email",$email);
          $busca_usuario->bindValue(":senha",$senha);
          $busca_usuario->execute();
          $busca_usuario = $busca_usuario->fetch();
          $busca_usuario = $busca_usuario["existe"];

          if($busca_usuario > 0){
              $_SESSION["email"] = $email;
              $_SESSION["senha"] = $senha;
              header("Location:gerenciar.php");
          }
      }

 ?>

<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="author" content="Mateus Soares da Silva">
    <title>Teste Voxus | Login</title>
    <!--<link rel="shortcut icon" href="_imgs/logo5.png" type="image/x-icon" />-->
    <link href="_css/estilo.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="_scripts/jquery.js"></script>
    <script type="text/javascript" src="_scripts/script.js"></script>
</head>
<body>
  <div id="container_login">
      <h1>Voxus</h1>
      <h2>Entre ou Cadastre-se</h2>
      <form method="post">
          <input type="email" placeholder="Email:" name="email"/>
          <input type="password" placeholder="Senha:" name="senha"/>
          <input class="botoes" type="submit" value="Entrar" name="entrar"/>

          <?php
              if(isset($_POST["entrar"]) && isset($busca_usuario)){
                  echo "<p id='mensagem'>Email ou senha estão errados.</p>";
              }
          ?>
      </form>
      <a href="cadastrar.php"><button style="margin-bottom:30px;" class="botoes">Cadastrar</button></a>
  </div>
</body>
</html
