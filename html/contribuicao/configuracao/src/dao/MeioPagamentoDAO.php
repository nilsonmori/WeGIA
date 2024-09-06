<?php

//requisitar arquivo de conexão

$config_path = "config.php";
if (file_exists($config_path)) {
    require_once($config_path);
} else {
    while (true) {
        $config_path = "../" . $config_path;
        if (file_exists($config_path)) break;
    }
    require_once($config_path);
}

require_once ROOT . '/html/contribuicao/php/conexao.php';

class MeioPagamentoDAO{
    private $pdo;

    public function __construct()
    {
        $conexao = new Conexao();
        $this->pdo = $conexao->pdo;
    }

    /**
     * Inseri um meio de pagamento no banco de dados da aplicação
     */
    public function cadastrar($descricao, $gatewayId){
        /*Lógica da aplicação */
        //definir consulta SQL
        $sqlCadastrar = "INSERT INTO contribuicao_meioPagamento (meio, id_plataforma) 
        VALUES (:descricao, :gatewayId)";
        //utilizar prepared statements
        $stmt = $this->pdo->prepare($sqlCadastrar);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':gatewayId', $gatewayId);
        //executar
        $stmt->execute();
    }

    /**
     * Retorna todos os meios de pagamentos registrados no banco de dados da aplicação
     */
    public function buscaTodos(){
        //definir consulta sql
        $sqlBuscaTodos = "SELECT cmp.id, cmp.meio, cmp.id_plataforma, cgp.plataforma, cgp.endpoint 
        FROM contribuicao_meioPagamento cmp
        JOIN contribuicao_gatewayPagamento cgp ON (cgp.id=cmp.id_plataforma)";
        //executar
        $resultado = $this->pdo->query($sqlBuscaTodos)->fetchAll(PDO::FETCH_ASSOC);
        //retornar resultado
        return $resultado;
    }

    /**
     * Remover o meio de pagamento que possuí id equivalente no banco de dados da aplicação
     */
    public function excluirPorId($id){
        //definir consulta sql
        $sqlExcluirPorId = "DELETE FROM contribuicao_meioPagamento WHERE id=:id";
        //utilizar prepared statements
        $stmt = $this->pdo->prepare($sqlExcluirPorId);
        $stmt->bindParam(':id', $id);
        //executar
        $stmt->execute();

        //verificar se algum elemento foi de fato excluído
        $meioPagamentoExcluido = $stmt->rowCount();

        if($meioPagamentoExcluido < 1){
            throw new Exception();
        }
    }

    /**
     * Edita o meio de pagamento que possuí id equivalente no banco de dados da aplicação
     */
    public function editarPorId($id, $descricao, $gatewayId){
         //definir consulta sql
         $sqlEditarPorId = "UPDATE contribuicao_meioPagamento SET meio =:descricao, id_plataforma =:gatewayId WHERE id=:id";
         //utilizar prepared statements
         $stmt = $this->pdo->prepare($sqlEditarPorId);
         $stmt->bindParam(':descricao', $descricao);
         $stmt->bindParam(':gatewayId', $gatewayId);
         $stmt->bindParam(':id', $id);
         //executar
         $stmt->execute();
 
         //verificar se algum elemento foi de fato alterado
         $meioPagamentoExcluido = $stmt->rowCount();
 
         if($meioPagamentoExcluido < 1){
             throw new Exception();
         }
    }
}