<?php

    include("conexao.php");

    $SISTEMA = $_POST['id_sistema'];

    $QUERY_M = mysqli_query($conexao, "SELECT id, valor, link FROM doacao_cartao_mensal WHERE id_sistema = $SISTEMA");
    $QTD = mysqli_num_rows($QUERY_M);

    if($QTD > 0)
    {
        echo ("<input type='hidden' name='cod_cartao' value=".$SISTEMA.">");
        echo("<table border='1px'>
                <tr>
                <th>VALOR</th>
                <th>LINK</th>
                </tr>");
            for($i = 0; $i < $QTD; $i++)
            {
                $RESULTADO = mysqli_fetch_row($QUERY_M);
                $id = $RESULTADO[0];
                $valor = $RESULTADO[1];
                $url = $RESULTADO[2];
                echo"<tr>";
                echo("<td><input type='number' name='valor[".$id."]' value=".$valor."></td>");
                echo("<td><input type='text' name='link[".$id."]' value=".$url."></td>");
                echo"</tr> ";
               
            }
        echo"</table>";
        
    } 
    else{
        echo"<span id='vazio_cartao_mensal'>Não há link para doação mensal pelo sistema selecionado</span>";
    }
   
?>