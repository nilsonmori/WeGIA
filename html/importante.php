<?php
    include("../memorando/conexao.php");
    $desp=$_GET["desp"];
    $comando1="select id_status_memorando from memorando where id_memorando=$desp";
    $query1=mysqli_query($conexao, $comando1);
    $linhas1=mysqli_num_rows($query1);
    $consulta=mysqli_fetch_row($query1);
    if($_GET["imp"]==1)
    {
    $comando="update memorando set id_status_memorando='9' where id_memorando=$desp";
    $query=mysqli_query($conexao, $comando);
    $linhas=mysqli_affected_rows($conexao);
    
    if($linhas==1)
    {
        header("Location: ../html/listar_memorandos_ativos.php");
    }
    else
    {
        echo "Não foi possível marcar o despacho como importante";
    }
    }

    else
    {
        $comando="update memorando set id_status_memorando='1' where id_memorando=$desp";
        $query=mysqli_query($conexao, $comando);
        $linhas=mysqli_affected_rows($conexao);
    
        if($linhas==1)
        {
            header("Location: ../html/listar_memorandos_ativos.php");
        }
        else
        {
            echo "Não foi possível marcar o despacho como importante";
        }
    }
?>