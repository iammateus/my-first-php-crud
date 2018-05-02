<?php
  session_start();
  if(isset($_SESSION["email"]) && isset($_SESSION["senha"])){
      require_once("_php/conexao.php");
      $pagina = isset($_GET["pagina"]) && $_GET["pagina"] != "" && $_GET["pagina"] > 0 ?$_GET["pagina"]:1;
      if(isset($_POST['sair'])){
          session_destroy();
          header("Location:index.php");
      }

      if(isset($_POST['confirmar'])){

        $codigo = isset($_POST['codigo'])?$_POST['codigo']:0;
        $nome = isset($_POST['nome'])?$_POST['nome']:"";
        $descricao = isset($_POST['descricao'])?$_POST['descricao']:"";

        $caminho = "_files/".md5(time()).".".pathinfo($_FILES['arquivo']['name'], PATHINFO_EXTENSION);

        if($_POST['confirmar'] == "Inserir"){

          if(move_uploaded_file($_FILES['arquivo']['tmp_name'], $caminho)) {
              $inserir = $pdo->prepare("INSERT INTO tasks VALUES (NULL,:nome,:descricao,:arquivo)");
              $inserir->bindValue(":nome",$nome);
              $inserir->bindValue(":descricao",$descricao);
              $inserir->bindValue(":arquivo",$caminho);
              $inserir->execute();
          }else{
            $inserir = $pdo->prepare("INSERT INTO tasks VALUES (NULL,:nome,:descricao,:arquivo)");
            $inserir->bindValue(":nome",$nome);
            $inserir->bindValue(":descricao",$descricao);
            $inserir->bindValue(":arquivo","Sem download");
            $inserir->execute();
          }

        }else{

          $busca_arquivo = $pdo->prepare("SELECT arquivo FROM tasks WHERE codigo = :codigo");
          $busca_arquivo->bindValue(":codigo",$codigo);
          $busca_arquivo->execute();
          $busca_arquivo = $busca_arquivo->fetch();
          $busca_arquivo = $busca_arquivo["arquivo"];

          if(basename( $_FILES['arquivo']['name'] != "")){
            if($busca_arquivo != "Sem download"){
              unlink($busca_arquivo);
            }
            if(move_uploaded_file($_FILES['arquivo']['tmp_name'], $caminho)){
                $alterar = $pdo->prepare("UPDATE tasks SET nome = :nome,descricao = :descricao,arquivo = :arquivo WHERE codigo = :codigo");
                $alterar->bindValue(":nome",$nome);
                $alterar->bindValue(":descricao",$descricao);
                $alterar->bindValue(":arquivo",$caminho);
                $alterar->bindValue(":codigo",$codigo);
                $alterar->execute();
            }
          }else{
            $alterar = $pdo->prepare("UPDATE tasks SET nome = :nome,descricao = :descricao WHERE codigo = :codigo");
            $alterar->bindValue(":nome",$nome);
            $alterar->bindValue(":descricao",$descricao);
            $alterar->bindValue(":codigo",$codigo);
            $alterar->execute();
          }

        }
      }

      if(isset($_POST['excluir'])){
        $codigo = isset($_POST['excluir'])?$_POST['excluir']:0;
        $busca_arquivo = $pdo->prepare("SELECT arquivo FROM tasks WHERE codigo = :codigo");
        $busca_arquivo->bindValue(":codigo",$codigo);
        $busca_arquivo->execute();
        $busca_arquivo = $busca_arquivo->fetch();
        $busca_arquivo = $busca_arquivo["arquivo"];


        if($busca_arquivo != "Sem download"){
          unlink($busca_arquivo);
        }


        $excluir = $pdo->prepare("DELETE FROM tasks WHERE codigo = :codigo");
        $excluir->bindValue(":codigo",$codigo);
        $excluir->execute();
      }

}else{
      header("Location:index.php");
      exit;
  }
 ?>
 <!doctype html>
 <html lang="pt-br">
 <head>
     <meta charset="utf-8">
     <meta name="viewport" content="width=device-width,initial-scale=1">
     <meta name="author" content="Mateus Soares da Silva">
     <title>Teste Voxus | Gerenciar Tasks</title>
     <!--<link rel="shortcut icon" href="_imgs/logo5.png" type="image/x-icon" />-->
     <link href="_css/estilo.css" rel="stylesheet" type="text/css" />
     <script type="text/javascript" src="_scripts/jquery.js"></script>
     <script type="text/javascript" src="_scripts/script.js"></script>
 </head>
 <body>
   <div id="fundo">
     <div id="container_login">
         <h1>Voxus</h1>
         <h2>Inserir Task</h2>
         <form  enctype="multipart/form-data" id="inserir_editar" method="post">
              <input id="codigo" type="hidden" name="codigo"/>
              <input id="nome" placeholder="Nome:" type="text" name="nome"/>
              <textarea id="descricao" placeholder="Descrição:" name="descricao"></textarea>
              <input type="file" style="display:none;" id="arquivo" name="arquivo"/>
              <input  class="botoes" value="Anexo" type="button" onclick="document.getElementById('arquivo').click();"/>
              <input class="botoes" name="confirmar" id="confirmar" type="submit" value="Inserir"/>
              <input class="botoes" id="cancelar" type="button" value="Cancelar"/>
          </form>
    </div>
   </div>
   <form id="sair-excluir" method="post">
      <input id="sair" type="submit" name="sair" value="Sair">
      <input id="excluir" type="hidden" name="excluir" value="">
   </form>
    <?php
        $limite = 10;
        $comeco = $pagina * $limite - $limite;
        $busca_tasks = $pdo->prepare("SELECT COUNT(*) AS 'quantidade' FROM tasks");
        $busca_tasks->execute();
        $busca_tasks = $busca_tasks->fetch();
        $busca_tasks = $busca_tasks["quantidade"];
        $ultima_pagina = ceil($busca_tasks/$limite);
        $ultima_pagina = $ultima_pagina < 1?1:$ultima_pagina;
        if($pagina > $ultima_pagina){
          $pagina = $ultima_pagina;
        }
        if($busca_tasks == 0){
            echo "<h2>Não há tasks.</h2>";
        }else{
          echo "<table>
                        <tr id=cabecario>
                              <th>Código</th>
                              <th>Nome</th>
                              <th>Descrição</th>
                              <th>Anexo</th>
                              <th>Edição</th>
                              <th>Exclusão</th>
                        </tr>";
          $busca_tasks = $pdo->prepare("SELECT * FROM tasks ORDER BY codigo LIMIT :comeco, 10");
          $busca_tasks->bindValue(":comeco",(int)$comeco,PDO::PARAM_INT);
          $busca_tasks->execute();

          while($linha = $busca_tasks->fetch(PDO::FETCH_ASSOC)){
              $codigo_task = $linha["codigo"];
              $nome_task = $linha["nome"];
              $descricao_task = $linha["descricao"];
              $anexo = $linha["arquivo"];
              echo "<tr>
                        <th>$codigo_task</th>
                        <th>$nome_task</th>
                        <th>$descricao_task</th>";
                        if($anexo != "Sem download"){
                          echo "<th><a href='$anexo' download>Download</a></th>";
                        }else{
                          echo "<th>Não há anexo</th>";
                        }
                        ?>
                        <th  class='botoes_tabela' onClick="editar('<?php echo $codigo_task; ?>','<?php echo $nome_task; ?>','<?php echo $descricao_task; ?>')">Alterar</th>
                        <?php
                        echo"
                        <th class='botoes_tabela' onClick='excluir($codigo_task)'>Excluir<th>
                   </tr>";
          }
          echo "</table>
          ";


        }
        echo "<button id='inserir'>Inserir</button>";
        echo "<div id='container_paginacao'>
        Páginas: ";
        for($x = 1;$x <= $ultima_pagina;$x++){
          echo "<a class='paginacao' href='gerenciar.php?pagina=$x'>$x</a> ";
        }





        echo"</div>"
        ?>
</body>
</html>
