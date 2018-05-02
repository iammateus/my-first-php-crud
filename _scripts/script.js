$(document).ready(function(){
    $("#cancelar").on("click",function(){
      $("#fundo").fadeOut(500);
    });
    $("#inserir").on("click",function(){
      $("#codigo").val("");
      $("#nome").val("");
      $("#descricao").val("");
      $("#confirmar").val("Inserir");
      $("h2").html("Inserir Task");
      $("#fundo").fadeIn(500);
    });
});
function editar(codigo_task,nome_task,descricao_task){
  $("#codigo").val(codigo_task);
  $("#nome").val(nome_task);
  $("#descricao").val(descricao_task);
  $("#confirmar").val("Alterar");
  $("h2").html("Alterar a task");
  $("#fundo").fadeIn(500);
}
function excluir(codigo){
  $("#excluir").val(codigo);
  $("#sair-excluir").submit();
}
