<?php
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		// Recupera o array JSON do campo oculto
		$dadosArray = $_POST['dadosArray'];

		// Decodifica o JSON para um array associativo PHP
		//$dados = json_decode($dadosArray, true);
	}
?>


<html>
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>LayGeladinhos</title>

		<link rel="stylesheet" href="css/estilo.css">
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">

		<script>
			let idCounter = 1; // Inicializa o contador de IDs

			// Função para adicionar uma nova linha <li> com ID único
			function adicionarLinha() {
				let linha = document.createElement('li');
				linha.classList.add('d-flex', 'form-group');
				linha.id = `item_${idCounter++}`;

				let inputDescricao = document.createElement('input');
				inputDescricao.type = "text";
				inputDescricao.className = "form-control";
				inputDescricao.name = "descricao";
				inputDescricao.placeholder = "Descrição";

				let inputQuantidade = document.createElement('input');
				inputQuantidade.type = "number";
				inputQuantidade.className = "form-control";
				inputQuantidade.name = "quantidade";
				inputQuantidade.placeholder = "Quantidade";
				inputQuantidade.setAttribute("oninput", "calcularTotal();"); // Adiciona o evento oninput

				let inputUnidade = document.createElement('input');
				inputUnidade.type = "text";
				inputUnidade.className = "form-control";
				inputUnidade.name = "unidade";
				inputUnidade.placeholder = "Unidade";

				let inputValor = document.createElement('input');
				inputValor.type = "text";
				inputValor.className = "form-control";
				inputValor.name = "valor";
				inputValor.placeholder = "Valor";
				inputValor.setAttribute("oninput", "calcularTotal();"); // Adiciona o evento oninput

				linha.appendChild(inputDescricao);
				linha.appendChild(inputQuantidade);
				linha.appendChild(inputUnidade);
				linha.appendChild(inputValor);

				let lista = document.getElementById('lista_de_compras');
				lista.appendChild(linha);
			}

			// Função para coletar os dados dos inputs e atribuir ao campo oculto
			function coletarDados() {
				let lista = document.querySelectorAll('#lista_de_compras li');
				let dados = [];

				lista.forEach(linha => {
					let descricao = linha.querySelector('input[name="descricao"]').value;
					let quantidade = linha.querySelector('input[name="quantidade"]').value;
					let unidade = linha.querySelector('input[name="unidade"]').value;
					let valor = linha.querySelector('input[name="valor"]').value;

					dados.push({
						descricao: descricao,
						quantidade: quantidade,
						unidade: unidade,
						valor: valor
					});
				});

				// Converte o array de objetos para JSON e atribui ao campo oculto
				let dadosJSON = JSON.stringify(dados);
				document.getElementById('dadosArray').value = dadosJSON;

				// Calcula o valor total e atribui ao campo oculto
				let valorTotal = calcularTotal(); // Chama a função e pega o valor total
				document.getElementById('valorCompra').value = valorTotal; // Armazena o valor total no campo oculto
			}

			// Função para calcular o valor total da lista de compras
			function calcularTotal() {
				let total = 0;
				let lista = document.querySelectorAll('#lista_de_compras li');

				lista.forEach(linha => {
					let quantidade = parseFloat(linha.querySelector('input[name="quantidade"]').value) || 0;
					let valor = parseFloat(linha.querySelector('input[name="valor"]').value.replace(',', '.') || 0); // Para aceitar o formato brasileiro

					total += quantidade * valor;
				});

				// Atualiza o valor total na página
				document.getElementById('valorTotal').innerText = `R$ ${total.toFixed(2).replace('.', ',')}`; // Formata o valor total com duas casas decimais
        		return total.toFixed(2); // Retorna o valor total com duas casas decimais
			}
		</script>
	</head>

	<body>
		<nav class="navbar navbar-light bg-light">
			<div class="container">
				<a class="navbar-brand" href="#">
					<img src="img/logo.png" width="30" height="30" class="d-inline-block align-top" alt="">
					LayGeladinhos
				</a>
			</div>
		</nav>
		<?php if(isset($_GET['inclusao']) && $_GET['inclusao'] == 1){ ?>	
		
			<div class="bg-success pt-2 text-white d-flex justify-content-center">
				<h5>Tarefa inserfida com sucesso!</h5>
			</div>
		
		<?php }	?>

		<div class="container app">
			<div class="row">
				<div class="col-md-3 menu">
					<ul class="list-group">
						<li class="list-group-item active"><a href="index.php">Entregas pendentes</a></li>
						<li class="list-group-item"><a href="nova_tarefa.php">Nova venda</a></li>
						<li class="list-group-item"><a href="todas_tarefas.php">Todas vendas</a></li>
						<li class="list-group-item"><a href="cadastrar_compras.php">Cadastrar Compra</a></li>
						<li class="list-group-item"><a href="#">Estoque</a></li>
						<li class="list-group-item"><a href="#">Cadastrar Receita</a></li>
						
					</ul>
				</div>

				<div class="col-md-9">
					<div class="container pagina">
						<div class="row">
							<div class="col"> 
								<h4>Nova compra</h4>
								<hr />

								<form method="post" action="tarefa_controller.php?acao=cadastrarCompra" onsubmit="coletarDados();">
									<div class="form-group">
										<label>Lista de Compras:</label>
										<ul id="lista_de_compras">
											<li class="d-flex form-group" id="item_0">
												<input type="text" class="form-control" name="descricao" placeholder="Descrição">
												<input type="number" class="form-control" name="quantidade" placeholder="Quantidade" oninput="calcularTotal();">
												<input type="text" class="form-control" name="unidade" placeholder="Unidade">
												<input type="text" class="form-control" name="valor" placeholder="Valor" oninput="calcularTotal();">
											</li>
										</ul>

										<!-- Botão para adicionar uma nova linha -->
										<button class="btn btn-info mt-2" type="button" onclick="adicionarLinha();">+</button>

										<!-- Campo oculto para armazenar o array em JSON -->
										<input type="hidden" id="dadosArray" name="dadosArray">
										<input type="hidden" id="valorCompra" name="valorCompra"> <!-- Campo oculto para o valor total -->

										<!-- Input para o Fornecedor -->
										<input type="text" class="form-control mt-2" placeholder="Fornecedor" name="fornecedor">

										<!-- Input para o Data -->
										<input type="date" class="form-control mt-2" placeholder="Data da compra" name="dataCompra">
										
										<!-- Label para o valor total -->
										<label class="mt-2">Valor Total: </label>
										<span id="valorTotal" class="font-weight-bold">R$ 0,00</span>
									</div>

									<!-- Botão para cadastrar e submeter o formulário -->
									<button class="btn btn-success" type="submit">Cadastrar</button>
								</form>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</body>
</html>