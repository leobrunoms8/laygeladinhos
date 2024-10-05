<?php
    class ListaComprasService{
        // Função para inserir um item na tabela 'lista_compras'
        public function inserirItemListaCompras($conn, $id_compra, $id_item, $quantidade, $valor) {
            $sql = "INSERT INTO lista_compras (id_compra, item_compra, qtd, valor) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$id_compra, $id_item, $quantidade, $valor]); // Executa a query com os parâmetros fornecidos
        }
    }
?>