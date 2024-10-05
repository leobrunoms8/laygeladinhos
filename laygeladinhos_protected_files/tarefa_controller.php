<?php

    require "../../laygeladinhos_protected_files/tarefa.model.php";
    require "../../laygeladinhos_protected_files/tarefa.service.php";
    require "../../laygeladinhos_protected_files/conexao.php";
    require "../../laygeladinhos_protected_files/compra.service.php";
    require "../../laygeladinhos_protected_files/item.service.php";
    require "../../laygeladinhos_protected_files/lista_compras.service.php";

    $acao = isset($_GET['acao']) ? $_GET['acao'] : $acao;

    if($acao == 'inserir'){
        $tarefa = new Tarefa();
        $tarefa->__set('tarefa', $_POST['tarefa']);
    
        $conexao = new Conexao();
    
        $tarefaService = new TarefaService($conexao, $tarefa);
        $tarefaService->inserir();
    
        header('Location: nova_tarefa.php?inclusao=1');

    } else if ($acao == 'recuperar') {
        $tarefa = new Tarefa();
        $conexao = new Conexao();

        $tarefaService = new TarefaService($conexao, $tarefa);
        $tarefas = $tarefaService->recuperar();
    } else if ($acao == 'atualizar') {
        $tarefa = new Tarefa();
        $tarefa->__set('id', $_POST['id']);        
        $tarefa->__set('tarefa', $_POST['tarefa']);
        $conexao = new Conexao();

        $tarefaService = new TarefaService($conexao, $tarefa);
        if($tarefas = $tarefaService->atualizar()) {
            if(isset($_GET['pag']) && $_GET['pag'] == 'index'){
                header('Location: index.php');
            }else{
                header('Location: todas_tarefas.php');
            }
        }
        
    } else if ($acao == 'remover') {
        $tarefa = new Tarefa();        
        $tarefa->__set('id', $_GET['id']);        

        $conexao = new Conexao();

        $tarefaService = new TarefaService($conexao, $tarefa);
        
        if($tarefaService->remover()) {
            if(isset($_GET['pag']) && $_GET['pag'] == 'index'){
                header('Location: index.php');
            }else{
                header('Location: todas_tarefas.php');
            }
        }
    } else if ($acao == 'marcarRealizada') {
        $tarefa = new Tarefa();
        $tarefa->__set('id', $_GET['id']);
        $tarefa->__set('id_status', 2);

        $conexao = new Conexao();

        $tarefaService = new TarefaService($conexao, $tarefa);
        if($tarefaService->marcarRealizada()){
            if(isset($_GET['pag']) && $_GET['pag'] == 'index'){
                header('Location: index.php');
            }else{
                header('Location: todas_tarefas.php');
            }
        }
    } else if ($acao == 'cadastrarCompra'){
        // Recebe os dados do formulário enviado via POST
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $fornecedor = $_POST['fornecedor'];
            $valorCompra = $_POST['valorCompra'];
            $dataDaCompra = $_POST['dataCompra'];
            $dadosArray = json_decode($_POST['dadosArray'], true); // Converte o JSON para um array PHP

            echo '<pre>';
            print_r($_POST);
            echo '<br>';
            echo $fornecedor;
            echo '<br>';
            echo $valorCompra;
            echo '<br>';
            echo $dataDaCompra;
            echo '<br>';
            print_r($dadosArray);
            echo '</pre>';
            echo '<hr>'; 

            // Inicia uma transação para garantir integridade dos dados
            $conexao = new Conexao();
            $conexao = $conexao->conectar();

            
            try {
                // Cria uma instância de CompraService
                $compraService = new CompraService();
                // Insere a compra e obtém o ID
                $idCompra = $compraService->inserirCompra($conexao, $fornecedor, $valorCompra, $dataDaCompra);
                echo $idCompra;
                echo '<br>';
                

                // Percorre os itens do array e insere ou atualiza os dados
                foreach ($dadosArray as $item) {
                    $descricao = $item['descricao'];
                    $quantidade = (int) $item['quantidade'];
                    $unidade = $item['unidade'];
                    $valor = (float) str_replace(',', '.', $item['valor']); // Converte o valor no formato brasileiro para o padrão
                    
                    $item = new ItemService();
                    // Verifica se o item existe ou insere um novo
                    $idItem = $item->verificarOuInserirItem($conexao, $descricao, $unidade);

                    // Insere o item na tabela 'lista_compras'
                    $listaDeCompras = new ListaComprasService();
                    $listaDeCompras->inserirItemListaCompras($conexao, $idCompra, $idItem, $quantidade, $valor);
                    

                    echo $descricao;
                    echo '<br>';
                    echo$quantidade;
                    echo '<br>';
                    echo$unidade;
                    echo '<br>';
                    echo$valor;
                    echo '<hr>';
                }

                // Confirma a transação
                //$conexao->commit();

                // Redireciona ou exibe uma mensagem de sucesso
                echo "Compra cadastrada com sucesso! ID da compra: " . $idCompra;

            } catch (Exception $e) {
                echo "Falha ao cadastrar a compra: " . $e->getMessage();
            }
        } else {
            // echo "Método inválido!";
        }

        // Fecha a conexão com o banco de dados
        //$conn = null;
        
    }
  
?>