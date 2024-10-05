<?php

class ItemService{

    // Função para verificar se o item já existe na tabela 'itens'
    public function verificarOuInserirItem($conn, $descricao, $unidade) {
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
}

?>