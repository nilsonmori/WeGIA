<?php
    require("../conexao.php");
    if(!isset($_POST) or empty($_POST)){
        $data = file_get_contents( "php://input" );
        $data = json_decode( $data, true );
        $_POST = $data;
    }else if(is_string($_POST)){
        $_POST = json_decode($_POST, true);
    }
    $cadastrado =  false;

    extract($_REQUEST);
    if(!isset($data_nasc) or ($data_nasc == null) or ($data_nasc == "") or empty($data_nasc) or ($data_nasc == "imp")){
        $data_nasc = "null";
    }else $data_nasc = "'$data_nasc'";

    if(!isset($contribuinte)){
        $contribuinte = null;
    }
    // si = sem informação
    if($resultado = mysqli_query($conexao, "INSERT INTO `pessoa`(`cpf`, `nome`, `telefone`, `data_nascimento`, `cep`, `estado`, `cidade`, `bairro`, `logradouro`, `numero_endereco`, `complemento`) VALUES ('$cpf_cnpj', '$socio_nome',  '$telefone', $data_nasc, '$cep', '$estado', '$cidade', '$bairro', '$rua', '$numero', '$complemento' )")){
        $id_pessoa = mysqli_insert_id($conexao);
        echo("teste");
        switch($pessoa){
            case "juridica": 
            if($contribuinte == "mensal"){
                $id_sociotipo = 3;
            }else if($contribuinte == "casual"){
                $id_sociotipo = 1;
            }else if($contribuinte == "bimestral"){
                $id_sociotipo = 7;
            }else if($contribuinte == "trimestral"){
                $id_sociotipo = 9;
            }else if($contribuinte == "semestral"){
                $id_sociotipo = 11;
            }
            
            if($contribuinte == null || $contribuinte == "si" || $contribuinte == ""){
                $id_sociotipo = 5;
            }  break;

            case "fisica": 
            if($contribuinte == "mensal"){
                $id_sociotipo = 2;
            }else if($contribuinte == "casual"){
                $id_sociotipo = 0;
            }else if($contribuinte == "bimestral"){
                $id_sociotipo = 6;
            }else if($contribuinte == "trimestral"){
                $id_sociotipo = 8;
            }else if($contribuinte == "semestral"){
                $id_sociotipo = 10;
            }
            
            
            if($contribuinte == null || $contribuinte == "si" || $contribuinte == ""){
                $id_sociotipo = 4;
            }  break;
        }

        $resultado = mysqli_query($conexao, "INSERT INTO `socio`(`id_pessoa`, `id_sociostatus`, `id_sociotipo`, `email`) VALUES ($id_pessoa, $status, $id_sociotipo, '$email')");
        if(mysqli_affected_rows($conexao)) $cadastrado = true;

    }

    echo json_encode($cadastrado);
?>