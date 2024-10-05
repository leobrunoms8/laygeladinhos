
<?php
// Conexão com o banco de dados usando PDO
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "seu_banco_de_dados";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Define o modo de erro do PDO como exceção
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Falha na conexão: " . $e->getMessage());
}

// Função para verificar se o item já existe na tabela 'itens'
function verificarOuInserirItem($conn, $descricao, $unidade) {
    // Verifica se o item já existe pela descrição e unidade de medida
    $sql = "SELECT id FROM itens WHERE descricao = ? AND unidade_de_medida = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$descricao, $unidade]);

    if ($stmt->rowCount() > 0) {
        // O item já existe, retorna o ID
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['id'];
    } else {
        // O item não existe, insere um novo item e retorna o ID
        $sqlInsert = "INSERT INTO itens (descricao, unidade_de_medida) VALUES (?, ?)";
        $stmtInsert = $conn->prepare($sqlInsert);
        $stmtInsert->execute([$descricao, $unidade]);
        return $conn->lastInsertId(); // Retorna o ID do item recém-inserido
    }
}

// Função para inserir uma nova compra e retornar o ID da compra
function inserirCompra($conn, $fornecedor, $valorCompra) {
    $sql = "INSERT INTO compra (fornecedor, valor) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$fornecedor, $valorCompra]); // Executa a query com os parâmetros fornecidos
    return $conn->lastInsertId(); // Retorna o ID da compra recém-inserida
}

// Função para inserir um item na tabela 'lista_compras'
function inserirItemListaCompras($conn, $id_compra, $id_item, $quantidade, $valor) {
    $sql = "INSERT INTO lista_compras (id_compra, item_compra, qtd, valor) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id_compra, $id_item, $quantidade, $valor]); // Executa a query com os parâmetros fornecidos
}

// Recebe os dados do formulário enviado via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fornecedor = $_POST['fornecedor'];
    $valorCompra = $_POST['valorCompra'];
    $dadosArray = json_decode($_POST['dadosArray'], true); // Converte o JSON para um array PHP

    // Inicia uma transação para garantir integridade dos dados
    $conn->beginTransaction();

    try {
        // Insere a compra e obtém o ID
        $idCompra = inserirCompra($conn, $fornecedor, $valorCompra);

        // Percorre os itens do array e insere ou atualiza os dados
        foreach ($dadosArray as $item) {
            $descricao = $item['descricao'];
            $quantidade = (int) $item['quantidade'];
            $unidade = $item['unidade'];
            $valor = (float) str_replace(',', '.', $item['valor']); // Converte o valor no formato brasileiro para o padrão

            // Verifica se o item existe ou insere um novo
            $idItem = verificarOuInserirItem($conn, $descricao, $unidade);

            // Insere o item na tabela 'lista_compras'
            inserirItemListaCompras($conn, $idCompra, $idItem, $quantidade, $valor);
        }

        // Confirma a transação
        $conn->commit();

        // Redireciona ou exibe uma mensagem de sucesso
        echo "Compra cadastrada com sucesso! ID da compra: " . $idCompra;

    } catch (Exception $e) {
        // Em caso de erro, desfaz a transação
        $conn->rollBack();
        echo "Falha ao cadastrar a compra: " . $e->getMessage();
    }
} else {
    echo "Método inválido!";
}

// Fecha a conexão com o banco de dados
$conn = null;
?>
