<?php

	ini_set('display_errors',1);
	ini_set('display_startup_erros',1);
	error_reporting(E_ALL);

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
		$resultado = mysqli_query($conexao, "SELECT * FROM permissao p JOIN acao a ON(p.id_acao=a.id_acao) JOIN recurso r ON(p.id_recurso=r.id_recurso) WHERE id_cargo=$id_cargo AND a.descricao = 'LER, GRAVAR E EXECUTAR' AND r.descricao='Módulo Saúde'");
		if(!is_bool($resultado) and mysqli_num_rows($resultado)){
			$permissao = mysqli_fetch_array($resultado);
			if($permissao['id_acao'] < 5){
        $msg = "Você não tem as permissões necessárias para essa página.";
        header("Location: ../home.php?msg_c=$msg");
			}
			$permissao = $permissao['id_acao'];
		}else{
        	$permissao = 1;
          $msg = "Você não tem as permissões necessárias para essa página.";
          header("Location: ../home.php?msg_c=$msg");
		}	
	}else{
		$permissao = 1;
		$msg = "Você não tem as permissões necessárias para essa página.";
		header("Location: ../../home.php?msg_c=$msg");
	}	

	// Adiciona a Função display_campo($nome_campo, $tipo_campo)
	require_once "../personalizacao_display.php";
	require_once ROOT."/controle/pet/PetControle.php";

?>


<!doctype html>
<html class="fixed">
<head>

	<!-- Basic -->
	<meta charset="UTF-8">

	<title>Informações Pets</title>

	<!-- Mobile Metas -->
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

	<!-- Vendor CSS -->
	<link rel="stylesheet" href="../../assets/vendor/bootstrap/css/bootstrap.css" />
	<link rel="stylesheet" href="../../assets/vendor/font-awesome/css/font-awesome.css" />
	<link rel="stylesheet" href="../../assets/vendor/magnific-popup/magnific-popup.css" />
	<link rel="stylesheet" href="../../assets/vendor/bootstrap-datepicker/css/datepicker3.css" />
	<link rel="icon" href="<?php display_campo("Logo",'file');?>" type="image/x-icon" id="logo-icon">

	<!-- Specific Page Vendor CSS -->
	<link rel="stylesheet" href="../../assets/vendor/select2/select2.css" />
	<link rel="stylesheet" href="../../assets/vendor/jquery-datatables-bs3/assets/css/datatables.css" />

	<!-- Theme CSS -->
	<link rel="stylesheet" href="../../assets/stylesheets/theme.css" />

	<!-- Skin CSS -->
	<link rel="stylesheet" href="../../assets/stylesheets/skins/default.css" />

	<!-- Theme Custom CSS -->
	<link rel="stylesheet" href="../../assets/stylesheets/theme-custom.css" />

	<!-- Head Libs -->
	<script src="../../assets/vendor/modernizr/modernizr.js"></script>
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.1.1/css/all.css">
		
	<!-- Vendor -->
	<script src="../../assets/vendor/jquery/jquery.min.js"></script>
	<script src="../../assets/vendor/jquery-browser-mobile/jquery.browser.mobile.js"></script>
	<script src="../../assets/vendor/bootstrap/js/bootstrap.js"></script>
	<script src="../../assets/vendor/nanoscroller/nanoscroller.js"></script>
	<script src="../../assets/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
	<script src="../../assets/vendor/magnific-popup/magnific-popup.js"></script>
	<script src="../../assets/vendor/jquery-placeholder/jquery.placeholder.js"></script>
		
	<!-- Specific Page Vendor -->
	<script src="../../assets/vendor/jquery-autosize/jquery.autosize.js"></script>
		
	<!-- Theme Base, Components and Settings -->
	<script src="../../assets/javascripts/theme.js"></script>
		
	<!-- Theme Custom -->
	<script src="../../assets/javascripts/theme.custom.js"></script>
		
	<!-- Theme Initialization Files -->
	<script src="../../assets/javascripts/theme.init.js"></script>

	<!-- javascript functions -->
	<script src="../../Functions/onlyNumbers.js"></script>
	<script src="../../Functions/onlyChars.js"></script>
	<script src="../../Functions/enviar_dados.js"></script>
	<script src="../../Functions/mascara.js"></script>
	<!-- jquery functions -->
	<script>
		// $(function() {
		// var pet =<?php
		// 	$response = new PetControle;
		// 	$response->listarTodos();
		// 	echo $_SESSION['pets'];?>;
		// $.each(pet, function(i, item) {
		// 	$("#tabela")
		// 		.append($("<tr onclick=irPg('"+item.id+"') id='"+item.id+"' class='tabble-row'>")
		// 			.append($("<td>")
		// 				.text(item.nome))
		// 			.append($("<td>")
		// 				.text(item.raca))
		// 			.append($("<td>")
		// 				.text(item.cor))
		// 			.append($("<td/>")
		// 			.html('<i class="glyphicon glyphicon-pencil"></i>')));
		// 	});
		// });		

		// function irPg(idPet){
		// 	localStorage.setItem('id_pet',idPet);
		// 	window.location.href = "./profile_pet.php?id_pet="+idPet;
		// }
		
		$(function () {
	      $("#header").load("../header.php");
	      $(".menuu").load("../menu.php");
	    });

		
	</script>
</head>
<body>
	<section class="body">
		<!-- start: header -->
		<div id="header"></div>
        <!-- end: header -->
        <div class="inner-wrapper">
          <!-- start: sidebar -->
          <aside id="sidebar-left" class="sidebar-left menuu"></aside>

			<!-- end: sidebar -->
			<section role="main" class="content-body">
				<header class="page-header">
					<h2>Informações</h2>

					<div class="right-wrapper pull-right">
						<ol class="breadcrumbs">
							<li><a href="../index.php"> <i class="fa fa-home"></i>
							</a></li>
							<li><span>Informações Pets</span></li>
						</ol>
						<a class="sidebar-right-toggle"><i class="fa fa-chevron-left"></i></a>
					</div>
				</header>

				<!-- start: page -->

				</header>

				<!-- start: page -->
				<section class="panel">
					<header class="panel-heading">
						<div class="panel-actions">
							<a href="#" class="fa fa-caret-down"></a>
						</div>
						<h2 class="panel-title">Atendimento do Pet</h2>
					</header>
                    <div class="mb-xlg">
                        <ul style="list-style:none" id="dado_atendimento">
                        </ul>
                    </div>
					<div class="panel-body">
						<table class="table table-bordered table-striped mb-none"
							>
							<thead>
								<tr>
									<th>Nome do medicamento</th>
									<th>Data da Aplicação</th>
									<th>Ação</th>
								</tr>
							</thead>
							<tbody id="tabela">

							</tbody>
						</table>
					</div>
					<br>
				</section>
                <!-- end: page -->

				<!-- Vendor -->
        
        <script>
            //Pedro===
            let tabela = document.querySelector("#tabela");
            let atendimento = document.querySelector("#dado_atendimento");

            window.addEventListener("load", ()=>{
                let id_historico = window.location+'';
                id_historico = id_historico.split("=");
                fetch("../../controle/pet/ControleHistorico.php",{
                    method: 'POST',
                    body: JSON.stringify({
                    'metodo': "getAtendimentoPet",
                    'id_historico': id_historico[1] 
                    })
                }).then(
                    resp=>{
                    return resp.json();
                    }
                ).then(
                    resp=>{
                        let descricao = resp[0].descricao.replace("<p>",'');
                        descricao = descricao.replace("</p>",'');
                        let data = resp[0].data_atendimento.split("-");
                        data = `${data[2]}/${data[1]}/${data[0]}`;

                        atendimento.innerHTML = `
                            <li>Data Atendimento: ${data}</li>
                            <li>Descrição: ${descricao}</li>
                        `;

						
                        let tabMedic = resp[1];
                        let data_medicacao;// = 'A aplicar';
                        let ids = [];

                        tabMedic.forEach( (valor,i) => {
							console.log(!valor.data_medicacao);
                            
                            if( !valor.data_medicacao === true){
                        		data_medicacao = 'A aplicar';
							}else{
                                data_medicacao = valor.data_medicacao;
								data_medicacao = data_medicacao.split("-");
								data_medicacao = `${data_medicacao[2]}-${data_medicacao[1]}-${data_medicacao[0]}`;
                            }

							tabela.innerHTML += `
                                <tr class="tabble-row odd" role="row">
                                    <td class="sorting_1">${valor.nome_medicamento}</td>
                                    <td class="sorting_1"><p id=data${i}>${data_medicacao}</p></td>
                                    <td id="editar_data" class="sorting_1">
                                        <i class="glyphicon glyphicon-pencil"></i>
                                    </td>
                                </tr>
                            `;
                            ids[i] = valor.id_medicacao;
                        })

                        let editDate = document.querySelectorAll("#editar_data");
                        editDate.forEach( (valor,i) => {
                            valor.addEventListener("click", ()=>{
                                document.querySelector(`#data${i}`).innerHTML = `
									<input type="date" placeholder="dd/mm/aaaa" maxlength="10" class="form-control" id="dataMed">
                                `;

                                dataMed.onchange = ()=>{
                                    dataMod = dataMed.value.split("-");
                                    dataMod = `${dataMod[2]}-${dataMod[1]}-${dataMod[0]}`;

                                    fetch("../../controle/pet/ControleHistorico.php",{
                                        method:"POST",
                                        body: JSON.stringify({
                                            "metodo":"dataAplicacao",
                                            "dados":dataMed.value+"|"+ids[i]
                                        })
                                    }).then(
                                        resp => {
                                            return resp.json();
                                        }
                                    ).then(
                                        resp => {
                                            console.log(resp);
                                        }
                                    )

                                    //========
                                    document.querySelector(`#data${i}`).innerHTML = `
                                        ${dataMod}
                                    `;
                                }
                                
                            })
                        })
                    }
                )                
            })

        </script>
		<script src="../../assets/vendor/select2/select2.js"></script>
		<script src="../../assets/vendor/jquery-datatables/media/js/jquery.dataTables.js"></script>
		<script src="../../assets/vendor/jquery-datatables/extras/TableTools/js/dataTables.tableTools.min.js"></script>
		<script src="../../assets/vendor/jquery-datatables-bs3/assets/js/datatables.js"></script>
		
		<!-- Theme Base, Components and Settings -->
		<script src="../../assets/javascripts/theme.js"></script>
		
		<!-- Theme Custom -->
		<script src="../../assets/javascripts/theme.custom.js"></script>
		
		<!-- Theme Initialization Files -->
		<script src="../../assets/javascripts/theme.init.js"></script>


		<!-- Examples -->
		<script src="../../assets/javascripts/tables/examples.datatables.default.js"></script>
		<script src="../../assets/javascripts/tables/examples.datatables.row.with.details.js"></script>
		<script src="../../assets/javascripts/tables/examples.datatables.tabletools.js"></script>
		
	<div align="right">
	<iframe src="https://www.wegia.org/software/footer/saude.html" width="200" height="60" style="border:none;"></iframe>
	</div>
	</body>
</html>

