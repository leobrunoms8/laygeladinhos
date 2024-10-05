<?php

class CompraService{


    // Função para inserir uma nova compra e retornar o ID da compra
    public function inserirCompra($conn, $fornecedor, $valorCompra, $data) {
        try{
            $sql = "INSERT INTO compra (fornecedor, valor, data) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$fornecedor, $valorCompra, $data]); // Executa a query com os parâmetros fornecidos
            return $conn->lastInsertId(); // Retorna o ID da compra recém-inserida
        } catch (Exception $e) {
            // Em caso de erro, desfaz a transação
            $conn->rollBack();
            echo "Falha ao cadastrar a compra: " . $e->getMessage();
        }
    }
}
?>


