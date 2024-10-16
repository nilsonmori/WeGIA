<?php
require_once '../model/ContribuicaoLogCollection.php';
require_once '../model/ContribuicaoLog.php';
require_once '../helper/Util.php';
require_once 'ApiCarneServiceInterface.php';
class PagarMeCarneService implements ApiCarneServiceInterface
{
    public function gerarCarne(ContribuicaoLogCollection $contribuicaoLogCollection)
    {
        //definir constantes que serão usadas em todas as parcelas

        $cpfSemMascara = Util::limpaCpf($contribuicaoLogCollection->getIterator()->current()->getSocio()->getDocumento());//Ignorar erro do VSCode para método não definida em ->current() caso esteja utilizando intelephense

        //Tipo do boleto
        $type = 'DM';

        //Validar regras

        //Buscar Url da API e token no BD
        try {
            $gatewayPagamentoDao = new GatewayPagamentoDAO();
            $gatewayPagamento = $gatewayPagamentoDao->buscarPorId(1); //Pegar valor do id dinamicamente

            //print_r($gatewayPagamento);
        } catch (PDOException $e) {
            //Implementar tratamento de erro
            echo 'Erro: ' . $e->getMessage();
        }

        //Buscar mensagem de agradecimento no BD
        $msg = 'Agradecimento';
        //Configurar cabeçalho da requisição
        $headers = [
            'Authorization: Basic ' . base64_encode($gatewayPagamento['token'] . ':'),
            'Content-Type: application/json;charset=utf-8',
        ];

        //Montar array de parcelas
        $parcelas = [];

        foreach ($contribuicaoLogCollection as $contribuicaoLog) {
            //gerar um número para o documento
            $numeroDocumento = Util::gerarNumeroDocumento(16);
            $boleto = [
                "items" => [
                    [
                        "amount" => $contribuicaoLog->getValor() * 100,
                        "description" => "Donation",
                        "quantity" => 1,
                        "code" => $contribuicaoLog->getCodigo()
                    ]
                ],
                "customer" => [
                    "name" => $contribuicaoLog->getSocio()->getNome(),
                    "email" => $contribuicaoLog->getSocio()->getEmail(),
                    "document_type" => "CPF",
                    "document" => $cpfSemMascara,
                    "type" => "Individual",
                    "address" => [
                        "line_1" => $contribuicaoLog->getSocio()->getLogradouro() . ", n°" . $contribuicaoLog->getSocio()->getNumeroEndereco() . ", " . $contribuicaoLog->getSocio()->getBairro(),
                        "line_2" => $contribuicaoLog->getSocio()->getComplemento(),
                        "zip_code" => $contribuicaoLog->getSocio()->getCep(),
                        "city" => $contribuicaoLog->getSocio()->getCidade(),
                        "state" => $contribuicaoLog->getSocio()->getEstado(),
                        "country" => "BR"
                    ],
                ],
                "payments" => [
                    [
                        "payment_method" => "boleto",
                        "boleto" => [
                            "instructions" => $msg,
                            "document_number" => $numeroDocumento,
                            "due_at" => $contribuicaoLog->getDataVencimento(),
                            "type" => $type
                        ]
                    ]
                ]
            ];

            // Transformar o boleto em JSON e inserir no array de parcelas
            $parcelas[] = json_encode($boleto);
        }

        //print_r($parcelas);

        //Implementar requisição para API
        $pdf_links = [];

        // Iniciar a requisição cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $gatewayPagamento['endPoint']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        foreach ($parcelas as $boleto_json) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $boleto_json);

            // Executar a requisição cURL
            $response = curl_exec($ch);

            // Lidar com a resposta da API (mesmo código de tratamento que você já possui)

            // Verifica por erros no cURL
            if (curl_errno($ch)) {
                echo 'Erro na requisição: ' . curl_error($ch);
                curl_close($ch);
                return false;
            }

            // Obtém o código de status HTTP
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            // Fecha a conexão cURL
            curl_close($ch);

            // Verifica o código de status HTTP
            if ($httpCode === 200 || $httpCode === 201) {
                $responseData = json_decode($response, true);
                $pdf_links []= $responseData['charges'][0]['last_transaction']['pdf'];
            } else {
                echo json_encode(['Erro' => 'A API retornou o código de status HTTP ' . $httpCode]);
                return false;
                // Verifica se há mensagens de erro na resposta JSON
                $responseData = json_decode($response, true);
                if (isset($responseData['errors'])) {
                    //echo 'Detalhes do erro:';
                    foreach ($responseData['errors'] as $error) {
                        //echo '<br> ' . htmlspecialchars($error['message']);
                    }
                }
            }
        }

        //print_r($pdf_links);

        //Juntar pdfs em um único documento

        //guardar segunda via
        return true;
    }

    public function guardarSegundaVia() {}
}
