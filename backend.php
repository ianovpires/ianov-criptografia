require_once('vendor/autoload.php');
use Algorand\Algorand;
use Algorand\AlgorandException;
use Algorand\Transaction\PayTransaction;

// Cria um objeto Algorand com as configurações da rede
$algod_endpoint = "https://testnet-algorand.api.purestake.io/ps2";
$algod_token = "";
$algod_headers = [
    "X-API-Key: " . $algod_token,
];
$algod_client = new Algorand($algod_token, $algod_endpoint);

// Define o endereço da conta pagadora e sua chave privada
$payer_address = "YOUR_PAYER_ADDRESS";
$payer_sk = "YOUR_PAYER_PRIVATE_KEY";

// Define o endereço da conta beneficiária
$beneficiary_address = $_POST["beneficiary_address"];

// Define o valor a ser transferido
$amount = $_POST["amount"];

try {
    // Cria uma transação de pagamento
    $pay_txn = new PayTransaction(
        $payer_address,
        $beneficiary_address,
        $amount
    );

    // Define o último bloco confirmado e o tempo de expiração da transação
    $params = $algod_client->getParams();
    $last_round = $params["lastRound"];
    $fee_per_byte = 10;
    $first_valid_round = $last_round;
    $last_valid_round = $last_round + 1000;
    $note = "Payment from Algorand Wallet";
    $genesis_id = $params["genesisID"];
    $genesis_hash = $params["genesisHash"];

    // Assina a transação com a chave privada da conta pagadora
    $signed_txn = $pay_txn->sign($payer_sk);

    // Envia a transação assinada para a rede Algorand
    $txid = $algod_client->sendTransaction($signed_txn);
    
    // Exibe o ID da transação para o usuário
    echo "Transaction ID: " . $txid;

} catch (AlgorandException $e) {
    // Exibe a mensagem de erro para o usuário
    echo "Error: " . $e->getMessage();
}
