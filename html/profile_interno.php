<?php 
   include_once '../classes/Cache.php';
   	session_start();
   	if(!isset($_SESSION['usuario'])){
   		header ("Location: ../index.php");
   	}
      $config_path = "config.php";
      if(file_exists($config_path)){
         require_once($config_path);
      }else{
         while(true){
            $config_path = "../" . $config_path;
            if(file_exists($config_path)) break;
         }
         require_once($config_path);
      }
      $conexao = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
      $id_pessoa = $_SESSION['id_pessoa'];
      $resultado = mysqli_query($conexao, "SELECT * FROM funcionario WHERE id_pessoa=$id_pessoa");
      if(!is_null($resultado)){
         $id_cargo = mysqli_fetch_array($resultado);
         if(!is_null($id_cargo)){
            $id_cargo = $id_cargo['id_cargo'];
         }
         $resultado = mysqli_query($conexao, "SELECT * FROM permissao WHERE id_cargo=$id_cargo and id_recurso=12");
         if(!is_bool($resultado) and mysqli_num_rows($resultado)){
            $permissao = mysqli_fetch_array($resultado);
            if($permissao['id_acao'] < 7){
           $msg = "Você não tem as permissões necessárias para essa página.";
           header("Location: ./home.php?msg_c=$msg");
            }
            $permissao = $permissao['id_acao'];
         }else{
              $permissao = 1;
             $msg = "Você não tem as permissões necessárias para essa página.";
             header("Location: ./home.php?msg_c=$msg");
         }	
      }else{
         $permissao = 1;
       $msg = "Você não tem as permissões necessárias para essa página.";
       header("Location: ./home.php?msg_c=$msg");
      }	
      
	// Adiciona a Função display_campo($nome_campo, $tipo_campo)
	require_once "personalizacao_display.php";
  require_once ROOT."/controle/FuncionarioControle.php";
  $cpf = new FuncionarioControle;
  $cpf->listarCPF();

  require_once ROOT."/controle/InternoControle.php";
  $cpf1 = new InternoControle;
  $cpf1->listarCPF();

  require_once ROOT."/controle/EnderecoControle.php";
  $endereco = new EnderecoControle;
  $endereco->listarInstituicao();
   
   $id=$_GET['id'];
   $cache = new Cache();
   $teste = $cache->read($id);
   
   if (!isset($teste)) {
   		
   		header('Location: ../controle/control.php?metodo=listarUm&nomeClasse=InternoControle&nextPage=../html/profile_interno.php?id='.$id.'&id='.$id);
      }
   
   ?>
<!doctype html>
<html class="fixed">
   <head>
      <!-- Basic -->
      <meta charset="UTF-8">
      <title>Perfil interno</title>
      <!-- Mobile Metas -->
      <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
      <!-- Web Fonts  -->
      <link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800|Shadows+Into+Light" rel="stylesheet" type="text/css">
      <!-- Vendor CSS -->
      <link rel="stylesheet" href="../assets/vendor/bootstrap/css/bootstrap.css" />
      <link rel="stylesheet" href="../assets/vendor/font-awesome/css/font-awesome.css" />
      <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">
      <link rel="stylesheet" href="../assets/vendor/magnific-popup/magnific-popup.css" />
      <link rel="stylesheet" href="../assets/vendor/bootstrap-datepicker/css/datepicker3.css" />
      <link rel="icon" href="<?php display_campo("Logo",'file');?>" type="image/x-icon" id="logo-icon">
      <link rel="stylesheet" type="text/css" href="../css/profile-theme.css">
      <script src="../assets/vendor/jquery/jquery.min.js"></script>
      <script src="../assets/vendor/jquery-browser-mobile/jquery.browser.mobile.js"></script>
      <script src="../assets/vendor/bootstrap/js/bootstrap.js"></script>
      <script src="../assets/vendor/nanoscroller/nanoscroller.js"></script>
      <script src="../assets/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
      <script src="../assets/vendor/magnific-popup/magnific-popup.js"></script>
      <script src="../assets/vendor/jquery-placeholder/jquery.placeholder.js"></script>
      <!-- Theme CSS -->
      <link rel="stylesheet" href="../assets/stylesheets/theme.css" />
      <!-- Skin CSS -->
      <link rel="stylesheet" href="../assets/stylesheets/skins/default.css" />
      <!-- Theme Custom CSS -->
      <link rel="stylesheet" href="../assets/stylesheets/theme-custom.css">
      <!-- Head Libs -->
      <script src="../assets/vendor/modernizr/modernizr.js"></script>
      <script src="../Functions/lista.js"></script>

      <!-- JavaScript Functions -->
	   <script src="../Functions/enviar_dados.js"></script>
      <script>
         function alterardate(data)
         {
         	var date=data.split("-");
         	return date[2]+"/"+date[1]+"/"+date[0];
         }
         function excluirimg(id)
            {
               $("#excluirimg").modal('show');
               $('input[name="id_documento"]').val(id);
            }
         function editimg(id,descricao)
            {
               $('#teste').val(descricao).prop('selected', true);
               $('input[name="id_documento"]').val(id);
               $("#editimg").modal('show');
            }
         $(function(){
         	var interno=<?php echo $infInterno = $cache->read($id);?>;
            var endereco=<?php echo $_SESSION['endereco']; ?>;
            console.log(interno);
            $.each(endereco,function(i,item){
               $("#cep").text("CEP: "+item.cep);
               $("#cidade").text("Cidade: "+item.cidade);
               $("#bairro").text("Bairro: "+item.bairro);
               $("#logradouro").text("Logradouro: "+item.logradouro);
               $("#numero").text("Numero: "+item.numero_endereco);
               $("#complemento").text("Complemento: "+item.complemento);
            });
         	$.each(interno,function(i,item){
         		if(i=1)
         		{
                  $("#formulario").append($("<input type='hidden' name='idInterno' value='"+item.idInterno+"'>"));
         			var cpf=item.cpf;
         			$("#nome").text("Nome: "+item.nome+' '+item.sobrenome);
         			$("#nomeform").val(item.nome);
                  $("#sobrenomeform").val(item.sobrenome);

         			if(item.imagem!=""){
                     $("#imagem").attr("src","data:image/gif;base64,"+item.imagem);
                  }else{
                     $("#imagem").attr("src","../img/semfoto.png");
                  }
         			if(item.sexo=="m")
         			{
         				$("#sexo").html("Sexo: <i class='fa fa-male'></i>  Masculino");
         				$("#radio1").prop('checked',true);
         			}
         			else if(item.sexo=="f")
         			{
         				$("#sexo").html("Sexo: <i class='fa fa-female'>  Feminino");
         				$("#radio2").prop('checked',true);
         			}
         			$("#pai").text("Nome do pai: "+item.nome_pai);
         			$("#paiform").val(item.nome_pai);
         
         			$("#mae").text("Nome da mãe: "+item.nome_mae);
         			$("#maeform").val(item.nome_mae);
         
         			$("#contato_urgente").text("Nome contato urgente: "+item.nome_contato_urgente);
         			$("#nomeContatoform").val(item.nome_contato_urgente);
         
         			$("#telefone1").text("Telefone contato urgente 1: "+item.telefone_contato_urgente_1);
         			$("#telefone1form").val(item.telefone_contato_urgente_1);
         
         			$("#telefone2").text("Telefone contato urgente 2: "+item.telefone_contato_urgente_2);
         			$("#telefone2form").val(item.telefone_contato_urgente_2);
         
         			$("#telefone3").text("Telefone contato urgente 3: "+item.telefone_contato_urgente_3);
         			$("#telefone3form").val(item.telefone_contato_urgente_3);
         
         			$("#sangue").text("Sangue: "+item.tipo_sanguineo);
         			$("#sangueform").val(item.tipo_sanguineo);
         			
         			$("#nascimento").text("Data de nascimento: "+alterardate(item.data_nascimento));
         			$("#nascimentoform").val(item.data_nascimento);
         
         			$("#rg").text("Registro geral: "+item.registro_geral);
         			$("#rgform").val(item.registro_geral);
                  
                  if(item.data_expedicao=="0000-00-00")
                  {
                     $("#data_expedicao").text("Data de expedição: Não informado");
                  }
                  else{
                     $("#data_expedicao").text("Data de expedição: "+item.data_expedicao);     
                  }
                  $("#expedicaoform").val(item.data_expedicao);
         
         			$('#orgao').text("Orgão emissor: "+item.orgao_emissor);
         			$("#orgaoform").val(item.orgao_emissor);
                  if(item.cpf.indexOf("ni")!=-1)
                  {
                     $("#cpf").text("Não informado");
                     $("#cpfform").val("Não informado");
                  }
                  else
                  {
         			$("#cpf").text(item.cpf);
                  $("#cpfform").val(item.cpf);
                  }
         
         			$("#inss").text("INSS: "+item.inss);
         
         			$("#loas").text("LOAS: "+item.loas);
         
         			$("#funrural").text("FUNRURAL: "+item.funrural);
         
         			$("#certidao").text("Certidão de nascimento: "+item.certidao);
         
         			$("#casamento").text("Certidão de Casamento: "+item.casamento);
         
         			$("#curatela").text("Curatela: "+item.curatela);
         
         			$("#saf").text("SAF: "+item.saf);
         
         			$("#sus").text("SUS: "+item.sus);
         
         			$("#bpc").text("BPC: "+item.bpc);
         
         			$("#ctps").text("CTPS: "+item.ctps);
         
         			$("#titulo").text("Titulo de eleitor: "+item.titulo);
                  
                  $("#observacao").text("Observações: "+item.observacao);
                  $("#observacaoform").val(item.observacao);
         		}
               if(item.imgdoc==null)
               {
                  $('#docs').append($("<strong >").append($("<p >").text("Não foi possível encontrar nenhuma imagem referente a esse interno!")));
               }
               else{
                  b64 = item.imgdoc;
                  b64 = b64.replace("data:image/pdf;base64,", "");
                  b64 = b64.replace("data:image/png;base64,", "");
                  b64 = b64.replace("data:image/jpg;base64,", "");
                  b64 = b64.replace("data:image/jpeg;base64,", "");
                  console.log(b64);
               if(b64.charAt(0) == "/" || b64.charAt(0) == "i"){
                  $('#docs').append($("<strong >").append($("<p >").text(item.descricao).attr("class","col-md-8"))).append($("<a >").attr("onclick","excluirimg("+item.id_documento+")").attr("class","link").append($("<i >").attr("class","fa fa-trash col-md-1 pull-right icones"))).append($("<a >").attr("onclick","editimg("+item.id_documento+",'"+item.descricao+"')").attr("class","link").append($("<i >").attr("class","fa fa-edit col-md-1 pull-right icones"))).append($("<div>").append($("<img />").attr("src", item.imgdoc).addClass("lazyload").attr("max-height","50px"))).append($("<form method='get' action='"+ item.imgdoc+"'><button type='submit'>Download</button></form>"));
               }else{
                  $('#docs').append($("<strong >").append($("<p >").text(item.descricao).attr("class","col-md-8"))).append($("<a >").attr("onclick","excluirimg("+item.id_documento+")").attr("class","link").append($("<i >").attr("class","fa fa-trash col-md-1 pull-right icones"))).append($("<a >").attr("onclick","editimg("+item.id_documento+",'"+item.descricao+"')").attr("class","link").append($("<i >").attr("class","fa fa-edit col-md-1 pull-right icones"))).append($("<div>").append($( `<a href="data:application/pdf;base64,${b64}" download="${item.descricao}.pdf"><button type='submit'>Download</button></a>`)));
               }
            }
         	})
         });
         $(function () {
            $("#header").load("header.php");
            $(".menuu").load("menu.php");
         });
      </script>
      <script type="text/javascript">

      function editar_informacoes_pessoais() {
         $("#nomeForm").prop('disabled', false);
         $("#sobrenomeForm").prop('disabled', false);
         $("#radioM").prop('disabled', false);
         $("#radioF").prop('disabled', false);
         $("#telefone1form").prop('disabled', false);
         $("#telefone2form").prop('disabled', false);
         $("#telefone2form").prop('disabled', false);
         $("#nascimento").prop('disabled', false);
         $("#pai").prop('disabled', false);
         $("#mae").prop('disabled', false);
         $("#sangue").prop('disabled', false);

         $("#botaoEditarIP").html('Cancelar');
         $("#botaoSalvarIP").prop('disabled', false);
         $("#botaoEditarIP").removeAttr('onclick');
         $("#botaoEditarIP").attr('onclick', "return cancelar_informacoes_pessoais()");

    }

      $(function () {
         $("#header").load("header.php");
         $(".menuu").load("menu.php");

          $("#cep").prop('disabled', true);
          $("#uf").prop('disabled', true);
          $("#cidade").prop('disabled', true);
          $("#bairro").prop('disabled', true);
          $("#rua").prop('disabled', true);
          $("#numero_residencia").prop('disabled', true);
          $("#complemento").prop('disabled', true);
          $("#ibge").prop('disabled', true);
         var endereco = <?php echo $_SESSION['endereco'];?> ;
         if(endereco=="")
         {
            $("#metodo").val("incluirEndereco");
         }
         else
         {
            $("#metodo").val("alterarEndereco");
         }
         $.each(endereco,function(i,item){   
            console.log(endereco);
              $("#nome").val(item.nome).prop('disabled', true);
              $("#cep").val(item.cep).prop('disabled', true);
              $("#uf").val(item.estado).prop('disabled', true);
              $("#cidade").val(item.cidade).prop('disabled', true);
              $("#bairro").val(item.bairro).prop('disabled', true);
              $("#rua").val(item.logradouro).prop('disabled', true);
              $("#numero_residencia").val(item.numero_endereco).prop('disabled', true);
              $("#complemento").val(item.complemento).prop('disabled', true);
              $("#ibge").val(item.ibge).prop('disabled', true);
              if (item.numero_endereco=='Sem número' || item.numero_endereco==null ) {
                $("#numResidencial").prop('checked',true);
              }
              });
       });  
       function editar_endereco(){
         
            $("#nome").prop('disabled', false);
            $("#cep").prop('disabled', false);
            $("#uf").prop('disabled', false);
            $("#cidade").prop('disabled', false);
            $("#bairro").prop('disabled', false);
            $("#rua").prop('disabled', false);
            $("#complemento").prop('disabled', false);
            $("#ibge").prop('disabled', false);         
            $("#numResidencial").prop('disabled', false);
            $("#numero_residencia").prop('disabled', false)
            $("#botaoEditarEndereco").html('Cancelar');
            $("#botaoSalvarEndereco").prop('disabled', false);
            $("#botaoEditarEndereco").removeAttr('onclick');
            $("#botaoEditarEndereco").attr('onclick', "return cancelar_endereco()");
        }
        function numero_residencial()
        {
         if($("#numResidencial").prop('checked'))
         {
            document.getElementById("numero_residencia").readOnly=true;
         }
         else
         {
            document.getElementById("numero_residencia").readOnly=false;
         }
        }
        function cancelar_endereco(){
            $("#cep").prop('disabled', true);
            $("#uf").prop('disabled', true);
            $("#cidade").prop('disabled', true);
            $("#bairro").prop('disabled', true);
            $("#rua").prop('disabled', true);
            $("#complemento").prop('disabled', true);
            $("#ibge").prop('disabled', true);
            $("#numResidencial").prop('disabled', true);
            $("#numero_residencia").prop('disabled', true);
         
            $("#botaoEditarEndereco").html('Editar');
            $("#botaoSalvarEndereco").prop('disabled', true);
            $("#botaoEditarEndereco").removeAttr('onclick');
            $("#botaoEditarEndereco").attr('onclick', "return editar_endereco()");
         
          }
        function limpa_formulário_cep() {
            //Limpa valores do formulário de cep.
            document.getElementById('rua').value=("");
            document.getElementById('bairro').value=("");
            document.getElementById('cidade').value=("");
            document.getElementById('uf').value=("");
            document.getElementById('ibge').value=("");
          }

        function meu_callback(conteudo) {
            if (!("erro" in conteudo)) {
                //Atualiza os campos com os valores.
                document.getElementById('rua').value=(conteudo.logradouro);
                document.getElementById('bairro').value=(conteudo.bairro);
                document.getElementById('cidade').value=(conteudo.localidade);
                document.getElementById('uf').value=(conteudo.uf);
                document.getElementById('ibge').value=(conteudo.ibge);
            }
            else {
                //CEP não Encontrado.
                limpa_formulário_cep();
                alert("CEP não encontrado.");
            }
          }

        function pesquisacep(valor) {
            //Nova variável "cep" somente com dígitos.
            var cep = valor.replace(/\D/g, '');
       
            //Verifica se campo cep possui valor informado.
            if (cep != "") {
       
              //Expressão regular para validar o CEP.
              var validacep = /^[0-9]{8}$/;
     
              //Valida o formato do CEP.
              if(validacep.test(cep)) {
     
                //Preenche os campos com "..." enquanto consulta webservice.
                document.getElementById('rua').value="...";
                document.getElementById('bairro').value="...";
                document.getElementById('cidade').value="...";
                document.getElementById('uf').value="...";
                document.getElementById('ibge').value="...";
   
                //Cria um elemento javascript.
                var script = document.createElement('script');
   
                //Sincroniza com o callback.
                script.src = 'https://viacep.com.br/ws/'+ cep + '/json/?callback=meu_callback';
   
                //Insere script no documento e carrega o conteúdo.
                document.body.appendChild(script);
     
              } //end if.
              else {
                  //cep é inválido.
                  limpa_formulário_cep();
                  alert("Formato de CEP inválido.");
              }
            } //end if.
            else {
                //cep sem valor, limpa formulário.
                limpa_formulário_cep();
            }
         
          };
          function gerarDocFuncional() {
      url = './funcionario/documento_listar.php';
      $.ajax({
        data: '',
        type: "POST",
        url: url,
        async: true,
        success: function(response) {
          var documento = response;
          $('#tipoDocumento').empty();
          $('#tipoDocumento').append('<option selected disabled>Selecionar...</option>');
          $.each(documento, function(i, item) {
            $('#tipoDocumento').append('<option value="' + item.id_docfuncional + '">' + item.nome_docfuncional + '</option>');
          });
        },
        dataType: 'json'
      });
    }

    function adicionarDocFuncional() {
      url = './funcionario/documento_adicionar.php';
      var nome_docfuncional = window.prompt("Cadastre um novo tipo de Documento:");
      if (!nome_docfuncional) {
        return
      }
      nome_docfuncional = nome_docfuncional.trim();
      if (nome_docfuncional == '') {
        return
      }

      data = 'nome_docfuncional=' + nome_docfuncional;

      $.ajax({
        type: "POST",
        url: url,
        data: data,
        success: function(response) {
          gerarDocFuncional();
        },
        dataType: 'text'
      })
    }
               function listarFunDocs(docfuncional){
               $("#doc-tab").empty();
               $.each(docfuncional, function(i, item) {
                 $("#doc-tab")
                   .append($("<tr>")
                     .append($("<td>").text(item.nome_docfuncional))
                     .append($("<td>").text(item.data))
                     .append($("<td style='display: flex; justify-content: space-evenly;'>")
                       .append($("<a href='./funcionario/documento_download.php?id_doc=" + item.id_fundocs + "' title='Visualizar ou Baixar'><button class='btn btn-primary'><i class='fas fa-download'></i></button></a>"))
                       .append($("<a onclick='removerFuncionarioDocs("+item.id_fundocs+")' href='#' title='Excluir'><button class='btn btn-danger'><i class='fas fa-trash-alt'></i></button></a>"))
                     )
                   )
               });
             }

             $(function() {
               $('#datatable-docfuncional').DataTable({
                 "order": [
                   [0, "asc"]
                 ]
               });
             });

    </script>
    
   </head>
   <body>
      <section class="body">
         <div id="header"></div>
            <!-- end: header -->
            <div class="inner-wrapper">
               <!-- start: sidebar -->
               <aside id="sidebar-left" class="sidebar-left menuu"></aside>
         <!-- end: sidebar -->
         <section role="main" class="content-body">
            <header class="page-header">
               <h2>Perfil</h2>
               <div class="right-wrapper pull-right">
                  <ol class="breadcrumbs">
                     <li>
                        <a href="index.html">
                        <i class="fa fa-home"></i>
                        </a>
                     </li>
                     <li><span>Páginas</span></li>
                     <li><span>Perfil</span></li>
                  </ol>
                  <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
               </div>
            </header>
            <!-- start: page -->
            <div class="row">
            <div class="col-md-4 col-lg-3">
               <section class="panel">
                        <div class="panel-body">
                           <?php
                              $enderecoArray = (array) $_SESSION['endereco'];
                              if($enderecoArray[0] == "[]")
                              {
                           ?>
                                 <div class="alert alert-warning" style="font-size: 15px;"><i class="fas fa-check mr-md"></i>O endereço da instituição não está cadastrado no sistema<br><a href=<?php echo WWW."html/personalizacao.php"; ?>>Cadastrar endereço da instituição</a></div>
                           <?php
                              }
                           ?>
                           <div class="thumb-info mb-md">
                              <?php
                                 if($_SERVER['REQUEST_METHOD'] == 'POST')
                                 {
                                   if(isset($_FILES['imgperfil']))
                                   {
                                     $image = file_get_contents ($_FILES['imgperfil']['tmp_name']);
                                     //session_start();
                                     $_SESSION['imagem']=$image;
                                                 echo '<img src="data:image/gif;base64,'.base64_encode($image).'" class="rounded img-responsive" alt="John Doe">';
                                   } 
                                 }
                                 else
                                 {
                                 ?>
                              <img id="imagem" alt="John Doe">
                              <?php 
                                 }
                                 ?>
                              <i class="fas fa-camera-retro btn btn-info btn-lg" data-toggle="modal" data-target="#myModal"></i>
                              <div class="container">
                                 <div class="modal fade" id="myModal" role="dialog">
                                    <div class="modal-dialog">
                                       <!-- Modal content-->
                                       <div class="modal-content">
                                          <div class="modal-header">
                                             <button type="button" class="close" data-dismiss="modal">&times;</button>
                                             <h4 class="modal-title">Adicionar uma Foto</h4>
                                          </div>
                                          <div class="modal-body">
                                             <form class="form-horizontal" method="POST" action="../controle/control.php" enctype="multipart/form-data">
                                                <input type="hidden" name="nomeClasse" value="InternoControle">
                                                <input type="hidden" name="metodo" value="alterarImagem">
                                                <div class="form-group">
                                                   <label class="col-md-4 control-label" for="imgperfil">Carregue nova imagem de perfil:</label>
                                                   <div class="col-md-8">
                                                      <input type="file" name="imgperfil" size="60" id="imgform" class="form-control">
                                                   </div>
                                                </div>
                                          </div>
                                          <div class="modal-footer">
                                          <input type="hidden" name="id_interno" value=<?php echo $_GET['id'] ?> >
                                          <input type="submit" id="formsubmit" value="Alterar imagem">
                                          </div>
                                       </div>
                                       </form>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="widget-toggle-expand mb-md">
                              <div class="widget-header">
                                 <div class="widget-content-expanded">
                                    <ul class="simple-todo-list">
                                    </ul>
                                 </div>
                              </div>
                           </div>
                        </div>
               </section>
            </div>
            <div class="col-md-8 col-lg-6">
            <div class="tabs">
            <ul class="nav nav-tabs tabs-primary">
               <li class="active">
                  <a href="#overview" data-toggle="tab">Visão Geral</a>
               </li>
               <li>
                  <a href="#endereco" data-toggle="tab">Endereço</a>
                </li>
               <li>
                  <a href="#arquivo" data-toggle="tab">Documentação</a>
               </li>
            </ul>
            <div class="tab-content">
            <div id="overview" class="tab-pane active">
               <h4 class="mb-xlg">Informações Pessoais</h4>
               <form id="formulario" action="../controle/control.php" enctype="multipart/form-data" method="POST">
                  <fieldset>
                     <div class="form-group">
                        <label class="col-md-3 control-label">Nome</label>
                        <div class="col-md-8">
                           <input type="text" class="form-control" name="nome" id="nomeform" id="profileFirstName" onkeypress="return Onlychars(event)" >
                        </div>
                     </div>
                     <div class="form-group">
                        <label class="col-md-3 control-label">Sobrenome</label>
                        <div class="col-md-8">
                           <input type="text" class="form-control" name="sobrenome" id="sobrenomeform" id="profileLastName" onkeypress="return Onlychars(event)" >
                        </div>
                     </div>
                     <div class="form-group">
                        <label class="col-md-3 control-label">Sexo</label>
                        <div class="col-md-8">
                           <input type="radio" name="sexo" id="radio1" value="m" style="margin-top: 10px margin-left: 15px;" required><i class="fa fa-male" style="font-size: 20px;" required></i>
                           <input type="radio" name="sexo" id="radio2"  value="f" style="margin-top: 10px; margin-left: 15px;"><i class="fa fa-female" style="font-size: 20px;"></i> 
                        </div>
                     </div>
                     <div class="form-group">
                        <label class="col-md-3 control-label" >Nome Contato</label>
                        <div class="col-md-8">
                           <input type="text" class="form-control" name="nomeContato" id="nomeContatoform" id="profileFirstName" onkeypress="return Onlychars(event)">
                        </div>
                     </div>
                     <div class="form-group">
                        <label class="col-md-3 control-label" for="telefone1">Telefone contato 1</label>
                        <div class="col-md-8">
                           <input type="text" class="form-control" maxlength="14" minlength="14" name="telefone1" id="telefone1form" placeholder="Ex: (22)99999-9999" onkeypress="return Onlynumbers(event)" onkeyup="mascara('(##)#####-####',this,event)" required>
                        </div>
                     </div>
                     <div class="form-group">
                        <label class="col-md-3 control-label" for="telefone2">Telefone contato 2</label>
                        <div class="col-md-8">
                           <input type="text" class="form-control" maxlength="14" minlength="14" name="telefone2" id="telefone2form" placeholder="Ex: (22)99999-9999" onkeypress="return Onlynumbers(event)" onkeyup="mascara('(##)#####-####',this,event)" required>
                        </div>
                     </div>
                     <div class="form-group">
                        <label class="col-md-3 control-label" for="telefone3">Telefone contato 3</label>
                        <div class="col-md-8">
                           <input type="text" class="form-control" maxlength="14" minlength="14" name="telefone3" id="telefone3form" placeholder="Ex: (22)99999-9999" onkeypress="return Onlynumbers(event)" onkeyup="mascara('(##)#####-####',this,event)" required>
                        </div>
                     </div>
                     <div class="form-group">
                        <label class="col-md-3 control-label">Nascimento</label>
                        <div class="col-md-8">
                            <input type="date" placeholder="dd/mm/aaaa" maxlength="10" class="form-control" name="nascimento" id="nascimentoform" max=<?php echo date('Y-m-d'); ?>>
                        </div>
                     </div>
                     <div class="form-group">
                        <label class="col-md-3 control-label">Nome do Pai</label>
                        <div class="col-md-8">
                           <input type="text" name="pai" class="form-control"  onkeypress="return Onlychars(event)" id="paiform" >
                        </div>
                     </div>
                     <div class="form-group">
                        <label class="col-md-3 control-label">Nome da Mãe</label>
                        <div class="col-md-8">
                           <input type="text" name="nomeMae" class="form-control" id="maeform" id="profileFirstNameform" onkeypress="return Onlychars(event)" >
                        </div>
                     </div>
                     <div class="form-group">
                        <label class="col-md-3 control-label">Tipo Sanguíneo</label>
                        <div class="col-md-6">
                           <select name="sangue" id="sangueform" class="form-control input-lg mb-md">
                              <option selected disabled value="blank">Selecionar</option>
                              <option value="A+">A+</option>
                              <option value="A-">A-</option>
                              <option value="B+">B+</option>
                              <option value="B-">B-</option>
                              <option value="O+">O+</option>
                              <option value="O-">O-</option>
                              <option value="AB+">AB+</option>
                              <option value="AB-">AB-</option>
                           </select>
                        </div>
                     </div>
                     <br/>
                     <hr class="dotted short">
                     <h4 class="mb-xlg doch4">Documentação</h4>
                     <div class="form-group">
                        <label class="col-md-3 control-label">Número do RG</label>
                        <div class="col-md-6">
                           <input type="text" class="form-control" name="rg" id="rgform" onkeypress="return Onlynumbers(event)" placeholder="Ex: 22.222.222-2" >
                        </div>
                     </div>
                     <div class="form-group">
                        <label class="col-md-3 control-label" >Órgão Emissor</label>
                        <div class="col-md-6">
                           <input type="text" name="orgaoEmissor" class="form-control" id="orgaoform" onkeypress="return Onlychars(event)">
                        </div>
                     </div>
                     <div class="form-group">
                        <label class="col-md-3 control-label" for="dataExpedicao">Data de Expedição</label>
                        <div class="col-md-6">
                            <input type="date" class="form-control" maxlength="10" placeholder="dd/mm/aaaa" name="dataExpedicao" id="expedicaoform" max=<?php echo date('Y-m-d'); ?>>
                        </div>
                     </div>
                     <div class="form-group">
                        <label class="col-md-3 control-label">Número do CPF</label>
                        <div class="col-md-6">
                             <input type="text" class="form-control" id="cpfform" name="numeroCPF" placeholder="Ex: 222.222.222-22" maxlength="14" onblur="validarCPF(this.value)" onkeypress="return Onlynumbers(event)" onkeyup="mascara('###.###.###-##',this,event)">
                        </div>
                     </div>
                     <div class="form-group">
                        <label class="col-md-3 control-label"></label>
                        <div class="col-md-6">
                           <p id="cpfInvalido" style="display: none; color: #b30000">CPF INVÁLIDO!</p>
                        </div>
                     </div>
               <div id="endereco" class="tab-pane">       
                  <section class="panel">
                    <header class="panel-heading">
                      <div class="panel-actions">
                        <a href="#" class="fa fa-caret-down"></a>
                      </div>
                      <h2 class="panel-title">Endereço</h2>
                    </header>
                  <div class="panel-body">
                    <!--Endereço-->
                    <hr class="dotted short">
                    <form id="endereco" class="form-horizontal" method="post" action="../controle/control.php">
                      <input type="hidden" name="nomeClasse" value="EnderecoControle">
                      <input type="hidden" name="metodo" value="alterarEndereco">
                      <div class="form-group">
                        <label class="col-md-3 control-label" for="cep">CEP</label>
                                    <div class="col-md-8">
                                                <input type="text" name="cep" value="" size="10" onblur="pesquisacep(this.value);" class="form-control" id="cep" maxlength="9" placeholder="Ex: 22222-222" onkeydown="return Onlynumbers(event)" onkeyup="mascara('#####-###',this,event)">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label" for="uf">Estado</label>
                                            <div class="col-md-8">
                                                <input type="text" name="uf" size="60" class="form-control" id="uf">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label" for="cidade">Cidade</label>
                                            <div class="col-md-8">
                                                <input type="text" size="40" class="form-control" name="cidade" id="cidade">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label" for="bairro">Bairro</label>
                                            <div class="col-md-8">
                                                <input type="text" name="bairro" size="40" class="form-control" id="bairro">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label" for="rua">Logradouro</label>
                                            <div class="col-md-8">
                                                <input type="text" name="rua" size="2" class="form-control" id="rua">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label" for="profileCompany">Número residencial</label>
                                            <div class="col-md-4">
                                                <input type="number" min="0" oninput="this.value = Math.abs(this.value)" class="form-control" name="numero_residencia" id="numero_residencia">
                                            </div>
                                            <div class="col-md-3">
                                                <label>Não possuo número
                                                    <input type="checkbox" id="numResidencial" name="naoPossuiNumeroResidencial" style="margin-left: 4px" onclick="return numero_residencial()">
                                                </label>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label" for="profileCompany">Complemento</label>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control" name="complemento" id="complemento" id="profileCompany">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label" for="ibge">IBGE</label>
                                            <div class="col-md-8">
                                                <input type="text" size="8" name="ibge" class="form-control" id="ibge">
                              </form>
                              </div>
                           </div>
                        
                     </div>
                  </section>
               </div>
                           
                  <div class="panel-footer">
                     <div class="row">
                        <div class="col-md-9 col-md-offset-3">
                           <input type="submit" class="btn btn-primary" value="Alterar" onclick="funcao1()"></button>
               </form>
               <button id="excluir" type="button" class="btn btn-danger" data-toggle="modal" data-target="#exclusao">Excluir</button>
               <div class="modal fade" id="exclusao" role="dialog">
               <div class="modal-dialog">
               <!-- Modal content-->
               <div class="modal-content">
               <div class="modal-header">
	               <button type="button" class="close" data-dismiss="modal">×</button>
	               <h3>Excluir um Interno</h3>
               </div>
               <div class="modal-body">
               		<p> Tem certeza que deseja excluir esse interno? Essa ação não poderá ser desfeita e todas as informações referentes a esse interno serão perdidas!</p>
               		<a href="../controle/control.php?metodo=excluir&nomeClasse=InternoControle&id=<?php echo $_GET['id']; ?>"><button button type="button" class="btn btn-success">Confirmar</button></a>
               		<button button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
               </div>
               </div>
               </div>
               </div>
               </div>
               </div>
               </div>
            </div>

            <!-- Aba de Documentação -->

            <div id="arquivo" class="tab-pane">
                  <section class="panel">
                    <header class="panel-heading">
                      <div class="panel-actions">
                        <a href="#" class="fa fa-caret-down"></a>
                      </div>
                      <h2 class="panel-title">Arquivos</h2>
                    </header>
                    <div class="panel-body">
                      <table class="table table-bordered table-striped mb-none" id="datatable-docfuncional">
                        <thead>
                          <tr>
                            <th>Arquivo</th>
                            <th>Data</th>
                            <th>Ação</th>
                          </tr>
                        </thead>
                        <tbody id="doc-tab">

                        </tbody>
                      </table>
                      <br>
                      <!-- Button trigger modal -->
                      <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#docFormModal">
                        Adicionar
                      </button>

                      <!-- Modal Form Documentos -->
                      <div class="modal fade" id="docFormModal" tabindex="-1" role="dialog" aria-labelledby="docFormModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                          <div class="modal-content">
                            <div class="modal-header" style="display: flex;justify-content: space-between;">
                              <h5 class="modal-title" id="exampleModalLabel">Adicionar Arquivo</h5>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                              </button>
                            </div>
                            <form action='./funcionario/documento_upload.php' method='post' enctype='multipart/form-data' id='funcionarioDocForm'>
                              <div class="modal-body" style="padding: 15px 40px">
                                <div class="form-group" style="display: grid;">
                                  <label class="my-1 mr-2" for="tipoDocumento">Tipo de Arquivo</label><br>
                                  <div style="display: flex;">
                                    <select name="id_docfuncional" class="custom-select my-1 mr-sm-2" id="tipoDocumento" required>
                                      <option selected disabled>Selecionar...</option>
                                      <option value="Certidão de Nascimento">Certidão de Nascimento</option>
                                       <option value="Certidão de Casamento">Certidão de Casamento</option>
                                       <option value="Curatela">Curatela</option>
                                       <option value="INSS">INSS</option>
                                       <option value="LOAS">LOAS</option>
                                       <option value="FUNRURAL">FUNRURAL</option>
                                       <option value="Título de Eleitor">Título de Eleitor</option>
                                       <option value="CTPS">CTPS</option>
                                       <option value="SAF">SAF</option>
                                       <option value="SUS">SUS</option>
                                       <option value="BPC">BPC</option> 
                                       <option value="CPF">CPF</option>
                                       <option value="Registro Geral">RG</option>
                                      
                                    
                                    </select>
                                   <!-- <a onclick="adicionarDocFuncional()" style="margin: 0 20px;"><i class="fas fa-plus w3-xlarge" style="margin-top: 0.75vw"></i></a> -->
                                  </div>
                                </div>
                                <div class="form-group">
                                  <label for="arquivoDocumento">Arquivo</label>
                                  <input name="arquivo" type="file" class="form-control-file" id="id_documento" accept="png;jpeg;jpg;pdf;docx;doc;odp" required>
                                </div>

                                <input type="number" name="id_interno" value="<?= $_GET['id']; ?>" style='display: none;'>

                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                <input type="submit" value="Enviar" class="btn btn-primary">
                              </div>
                            </form>
                            </div>
                          </div>
                        </div>
                    </section>
                  </div>





           <!-- <div id="docs" class="tab-pane">
                  <section class="panel">
                    <header class="panel-heading">
                      <div class="panel-actions">
                        <a href="#" class="fa fa-caret-down"></a>
                      </div>
                      <h2 class="panel-title">Arquivos</h2>
                    </header>
                    <div class="panel-body">
                      <table class="table table-bordered table-striped mb-none" id="datatable-docfuncional">
                        <thead>
                          <tr>
                            <th>Arquivo</th>
                            <th>Data</th>
                            <th>Ação</th>
                          </tr>
                        </thead>
                        <tbody id="doc-tab">

                        </tbody>
                      </table>
                      <br> -->
                      <!-- Button trigger modal -->
                     <!-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#docFormModal">
                        Adicionar
                      </button> -->

                      <!-- Modal Form Documentos -->
                     <!-- <div class="modal fade" id="docFormModal" tabindex="-1" role="dialog" aria-labelledby="docFormModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                          <div class="modal-content">
                            <div class="modal-header" style="display: flex;justify-content: space-between;">
                              <h5 class="modal-title" id="exampleModalLabel">Adicionar Arquivo</h5>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                              </button>
                            </div>
                          <form action='./funcionario/documento_upload.php' method='post' enctype='multipart/form-data' id='funcionarioDocForm'>
                           <select name="descricao" id="teste">
                              <option value="Certidão de Nascimento">Certidão de Nascimento</option>
                              <option value="Certidão de Casamento">Certidão de Casamento</option>
                              <option value="Curatela">Curatela</option>
                              <option value="INSS">INSS</option>
                              <option value="LOAS">LOAS</option>
                              <option value="FUNRURAL">FUNRURAL</option>
                              <option value="Título de Eleitor">Título de Eleitor</option>
                              <option value="CTPS">CTPS</option>
                              <option value="SAF">SAF</option>
                              <option value="SUS">SUS</option>
                              <option value="BPC">BPC</option> 
                              <option value="CPF">CPF</option>
                              <option value="Registro Geral">RG</option>
                           </select><br/>
            
                              <p> Selecione a nova imagem</p>
                              <div class="col-md-12">
                                 <input type="file" name="doc" size="60"  class="form-control" > 
                              </div><br/>
                              <input type="hidden" name="id_documento" id="id_documento">
                              <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
                              <input type="hidden" name="nomeClasse" value="DocumentoControle">
                              <input type="hidden" name="metodo" value="alterar">
                              <input type="submit" value="Confirmar" class="btn btn-success">
                              <button button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                           </form>
                           </div>
                           </div>
                           </div>
                           </div>

                                      
                                    </select>
                                    <a onclick="adicionarDocFuncional()" style="margin: 0 20px;"><i class="fas fa-plus w3-xlarge" style="margin-top: 0.75vw"></i></a>
                                  </div>
                                </div>
                                <div class="form-group">
                                  <label for="arquivoDocumento">Arquivo</label>
                                  <input name="arquivo" type="file" class="form-control-file" id="arquivoDocumento" accept="png;jpeg;jpg;pdf;docx;doc;odp" required>
                                </div>

                                <input type="number" name="id_funcionario" value="<?= $_GET['id_funcionario']; ?>" style='display: none;'>

                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                <input type="submit" value="Enviar" class="btn btn-primary">
                              </div>
                            </form>
                            </div>
                          </div>
                        </div>
                    </section>
                  </div> -->

                 
                   


                 
                                      
                       


            <!-- end: page -->
         </section>
         <aside id="sidebar-right" class="sidebar-right">
            <div class="nano">
               <div class="nano-content">
                  <a href="#" class="mobile-close visible-xs">
                  Collapse <i class="fa fa-chevron-right"></i>
                  </a>
                  <div class="sidebar-right-wrapper">
                     <div class="sidebar-widget widget-calendar">
                        <h6>Upcoming Tasks</h6>
                        <div data-plugin-datepicker data-plugin-skin="dark" ></div>
                        <ul>
                           <li>
                              <time datetime="2014-04-19T00:00+00:00">04/19/2014</time>
                              <span>Company Meeting</span>
                           </li>
                        </ul>
                     </div>
                     <div class="sidebar-widget widget-friends">
                        <h6>Friends</h6>
                        <ul>
                           <li class="status-online">
                              <figure class="profile-picture">
                                 <img src="../img/semfoto.png" alt="Joseph Doe" class="img-circle">
                              </figure>
                              <div class="profile-info">
                                 <span class="name">Joseph Doe Junior</span>
                                 <span class="title">Hey, how are you?</span>
                              </div>
                           </li>
                           <li class="status-online">
                              <figure class="profile-picture">
                                 <img src="../img/semfoto.png" alt="Joseph Doe" class="img-circle">
                              </figure>
                              <div class="profile-info">
                                 <span class="name">Joseph Doe Junior</span>
                                 <span class="title">Hey, how are you?</span>
                              </div>
                           </li>
                           <li class="status-offline">
                              <figure class="profile-picture">
                                 <img src="../img/semfoto.png" alt="Joseph Doe" class="img-circle">
                              </figure>
                              <div class="profile-info">
                                 <span class="name">Joseph Doe Junior</span>
                                 <span class="title">Hey, how are you?</span>
                              </div>
                           </li>
                           <li class="status-offline">
                              <figure class="profile-picture">
                                 <img src="../img/semfoto.png" alt="Joseph Doe" class="img-circle">
                              </figure>
                              <div class="profile-info">
                                 <span class="name">Joseph Doe Junior</span>
                                 <span class="title">Hey, how are you?</span>
                              </div>
                           </li>
                        </ul>
                     </div>
                  </div>
               </div>
            </div>
         </aside>
      </section>
		<!-- Vendor -->
		<script src="../assets/vendor/select2/select2.js"></script>
        <script src="../assets/vendor/jquery-datatables/media/js/jquery.dataTables.js"></script>
        <script src="../assets/vendor/jquery-datatables/extras/TableTools/js/dataTables.tableTools.min.js"></script>
        <script src="../assets/vendor/jquery-datatables-bs3/assets/js/datatables.js"></script>

        <!-- Theme Base, Components and Settings -->
        <script src="../assets/javascripts/theme.js"></script>

        <!-- Theme Custom -->
        <script src="../assets/javascripts/theme.custom.js"></script>

        <!-- Theme Initialization Files -->
        <script src="../assets/javascripts/theme.init.js"></script>


        <!-- Examples -->
        <script src="../assets/javascripts/tables/examples.datatables.default.js"></script>
        <script src="../assets/javascripts/tables/examples.datatables.row.with.details.js"></script>
        <script src="../assets/javascripts/tables/examples.datatables.tabletools.js"></script>
         <div class="modal fade" id="excluirimg" role="dialog">
         <div class="modal-dialog">
         <!-- Modal content-->
         <div class="modal-content">
         <div class="modal-header">
	      <button type="button" class="close" data-dismiss="modal">×</button>
	      <h3>Excluir um Documento</h3>
         </div>
         <div class="modal-body">
         <p> Tem certeza que deseja excluir a imagem desse documento? Essa ação não poderá ser desfeita! </p>
         <form action="../controle/control.php" method="GET">
            <input type="hidden" name="id_documento" id="excluirdoc">
            <input type="hidden" name="nomeClasse" value="DocumentoControle">
            <input type="hidden" name="metodo" value="excluir">
            <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
            <input type="submit" value="Confirmar" class="btn btn-success">
            <button button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
         </form>
         </div>
         </div>
         </div>
         </div>
         <iv class="modal fade" id="editimg" role="dialog">
         <div class="modal-dialog">
         <!-- Modal content-->
         <div class="modal-content">
         <div class="modal-header">
         <button type="button" class="close" data-dismiss="modal">×</button>
         <h3>Alterar um Documento</h3>
         </div>
         <div class="modal-body">
         <p> Selecione o benefício referente a nova imagem</p>
         <form action="../controle/control.php" method="POST" enctype="multipart/form-data">
            <select name="descricao" id="teste">
               <option value="Certidão de Nascimento">Certidão de Nascimento</option>
               <option value="Certidão de Casamento">Certidão de Casamento</option>
               <option value="Curatela">Curatela</option>
               <option value="INSS">INSS</option>
               <option value="LOAS">LOAS</option>
               <option value="FUNRURAL">FUNRURAL</option>
               <option value="Título de Eleitor">Título de Eleitor</option>
               <option value="CTPS">CTPS</option>
               <option value="SAF">SAF</option>
               <option value="SUS">SUS</option>
               <option value="BPC">BPC</option> 
               <option value="CPF">CPF</option>
               <option value="Registro Geral">RG</option>
            </select><br/>
            
            <p> Selecione a nova imagem</p>
            <div class="col-md-12">
               <input type="file" name="doc" size="60"  class="form-control" > 
            </div><br/>
            <input type="hidden" name="id_documento" id="id_documento">
            <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
            <input type="hidden" name="nomeClasse" value="DocumentoControle">
            <input type="hidden" name="metodo" value="alterar">
            <input type="submit" value="Confirmar" class="btn btn-success">
            <button button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
         </form>
         </div>
         </div>
         </div>
         </div>
         <script>
   function funcao1(){
        var cpfs = <?php echo $_SESSION['cpf_funcionario'];?> ;
        var cpf_funcionario = $("#cpf").val();
        var cpf_funcionario_correto = cpf_funcionario.replace(".", "");
        var cpf_funcionario_correto1 = cpf_funcionario_correto.replace(".", "");
        var cpf_funcionario_correto2 = cpf_funcionario_correto1.replace(".", "");
        var cpf_funcionario_correto3 = cpf_funcionario_correto2.replace("-", "");
        var apoio = 0;
        var cpfs1 = <?php echo $_SESSION['cpf_interno'];?> ;
        $.each(cpfs,function(i,item){
          if(item.cpf==cpf_funcionario_correto3)
          {
            alert("Alteração não realizada! O CPF informado já está cadastrado no sistema");
            apoio = 1;
          }
        });
        $.each(cpfs1,function(i,item){
          if(item.cpf==cpf_funcionario_correto3)
          { 
            alert("Cadastro não realizado! O CPF informado já está cadastrado no sistema");
            apoio = 1;
          }
        });
        if(apoio == 0)
        {
          alert("Cadastrado com sucesso!")
        }
      }
   </script>
    </body>
</html>
