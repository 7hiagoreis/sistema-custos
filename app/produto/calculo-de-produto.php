<?php
// app/produto/calculo-de-produto.php
require_once '../config/conexao.php';

$mensagem = '';
$tipo_mensagem = '';

// Processa o salvamento
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['salvar'])) {
    $desc_materia_prima = $conexao->real_escape_string($_POST['desc_materia_prima']);
    $preco_compra_mp = str_replace(',', '.', str_replace('.', '', $_POST['preco_compra_mp']));
    $icms_mp = floatval($_POST['icms_mp']);
    $pis_mp = floatval($_POST['pis_mp']);
    $cofins_mp = floatval($_POST['cofins_mp']);
    $ipi_mp = floatval($_POST['ipi_mp']);
    $ii_mp = floatval($_POST['ii_mp']);
    $frete_mp = floatval($_POST['frete_mp']);
    $tipo_frete = $_POST['tipo_frete'];
    $comissao_mp = floatval($_POST['comissao_mp']);
    $preco_final_mp = str_replace(',', '.', str_replace('.', '', $_POST['preco_final_mp']));
    
    // Calcula o preço final novamente para garantir
    $total_icms = $preco_compra_mp * $icms_mp / 100;
    $total_pis = $preco_compra_mp * $pis_mp / 100;
    $total_cofins = $preco_compra_mp * $cofins_mp / 100;
    $total_ipi = $preco_compra_mp * $ipi_mp / 100;
    $total_ii = $preco_compra_mp * $ii_mp / 100;
    $total_comissao = $preco_compra_mp * $comissao_mp / 100;
    
    if ($tipo_frete == 'perc') {
        $total_frete = $preco_compra_mp * $frete_mp / 100;
    } else {
        $total_frete = $frete_mp;
    }
    
    $preco_final_calculado = $preco_compra_mp + $total_icms + $total_pis + $total_cofins + $total_ipi + $total_ii + $total_frete + $total_comissao;
    
    $sql = "INSERT INTO cadastro_materia_prima 
            (desc_materia_prima, preco_compra_mp, icms_mp, pis_mp, cofins_mp, ipi_mp, ii_mp, frete_mp, tipo_frete, comissao_mp, preco_final_mp) 
            VALUES 
            ('$desc_materia_prima', '$preco_compra_mp', '$icms_mp', '$pis_mp', '$cofins_mp', '$ipi_mp', '$ii_mp', '$frete_mp', '$tipo_frete', '$comissao_mp', '$preco_final_calculado')";
    
    if ($conexao->query($sql)) {
        $mensagem = "Produto cadastrado com sucesso!";
        $tipo_mensagem = "sucesso";
    } else {
        $mensagem = "Erro ao cadastrar: " . $conexao->error;
        $tipo_mensagem = "erro";
    }
}

// Busca os produtos cadastrados
$sql = "SELECT * FROM cadastro_materia_prima ORDER BY desc_materia_prima ASC";
$resultado = $conexao->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Calculadora de Custos - Matéria Prima</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>

<div class="container">
    <div class="cabecalho">
        <h2>Cadastro e Cálculo de Matéria Prima</h2>
    </div>
    
    <div style="padding: 20px;">
        
        <?php if ($mensagem): ?>
            <div class="msg-<?php echo $tipo_mensagem; ?>">
                <?php echo $mensagem; ?>
            </div>
        <?php endif; ?>
        
        <!-- Formulário de Cadastro -->
        <form action="" method="POST" id="form-cadastro">
            <table class="tabela-cadastro">
                <tr>
                    <td width="200"><strong>Descrição:</strong></td>
                    <td colspan="3">
                        <input type="text" name="desc_materia_prima" id="desc_materia_prima" 
                               style="width: 100%;" required>
                    </td>
                </tr>
                <tr>
                    <td><strong>Preço de compra:</strong></td>
                    <td width="300">
                        <input type="text" name="preco_compra_mp" id="preco_compra_mp" 
                               class="preco_compra" style="width: 150px;" 
                               onkeyup="aplicarMascaraMoeda(this)" value="0,00">
                    </td>
                    <td width="200"><strong>Unidade:</strong></td>
                    <td>
                        <div class="radio-group">
                            <label><input type="radio" name="metragem" value="LT" checked> LT</label>
                            <label><input type="radio" name="metragem" value="KG"> KG</label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><strong>ICMS (%):</strong></td>
                    <td><input type="text" name="icms_mp" class="icms" value="0"></td>
                    <td><strong>Valor ICMS:</strong></td>
                    <td><input type="text" class="per_icms" readonly></td>
                </tr>
                <tr>
                    <td><strong>PIS (%):</strong></td>
                    <td><input type="text" name="pis_mp" class="pis" value="0"></td>
                    <td><strong>Valor PIS:</strong></td>
                    <td><input type="text" class="per_pis" readonly></td>
                </tr>
                <tr>
                    <td><strong>COFINS (%):</strong></td>
                    <td><input type="text" name="cofins_mp" class="cofins" value="0"></td>
                    <td><strong>Valor COFINS:</strong></td>
                    <td><input type="text" class="per_cofins" readonly></td>
                </tr>
                <tr>
                    <td><strong>IPI (%):</strong></td>
                    <td><input type="text" name="ipi_mp" class="ipi" value="0"></td>
                    <td><strong>Valor IPI:</strong></td>
                    <td><input type="text" class="per_ipi" readonly></td>
                </tr>
                <tr>
                    <td><strong>II (%):</strong></td>
                    <td><input type="text" name="ii_mp" class="ii" value="0"></td>
                    <td><strong>Valor II:</strong></td>
                    <td><input type="text" class="per_ii" readonly></td>
                </tr>
                <tr>
                    <td><strong>Frete:</strong></td>
                    <td>
                        <input type="text" name="frete_mp" class="frete_valor" style="width: 100px;" value="0">
                        <div class="radio-group" style="margin-top: 5px;">
                            <label><input type="radio" name="tipo_frete" class="tipo_frete" value="perc" checked> %</label>
                            <label><input type="radio" name="tipo_frete" class="tipo_frete" value="real"> R$</label>
                        </div>
                    </td>
                    <td><strong>Valor Frete:</strong></td>
                    <td><input type="text" class="per_frete" readonly></td>
                </tr>
                <tr>
                    <td><strong>Comissão (%):</strong></td>
                    <td><input type="text" name="comissao_mp" class="comissao" value="0"></td>
                    <td><strong>Valor Comissão:</strong></td>
                    <td><input type="text" class="per_comissao" readonly></td>
                </tr>
                <tr class="campo-resultado">
                    <td><strong>Preço Final:</strong></td>
                    <td colspan="3">
                        <input type="text" name="preco_final_mp" class="preco_final" 
                               style="font-size: 18px; font-weight: bold; width: 200px;" readonly>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align: center;">
                        <button type="submit" name="salvar" class="botao">Salvar Produto</button>
                        <button type="reset" class="botao botao-limpar" onclick="setTimeout(inicializarNovoCadastro, 10)">Limpar</button>
                    </td>
                </tr>
            </table>
        </form>
        
        <!-- Lista de Produtos Cadastrados -->
        <h3 style="margin-top: 30px; margin-bottom: 10px;">📋 Produtos Cadastrados</h3>
        
        <table class="tabela-lista">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Descrição</th>
                    <th>Preço Compra</th>
                    <th>ICMS</th>
                    <th>PIS</th>
                    <th>COFINS</th>
                    <th>IPI</th>
                    <th>II</th>
                    <th>Frete</th>
                    <th>Comissão</th>
                    <th>Preço Final</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($resultado && $resultado->num_rows > 0): ?>
                    <?php while ($row = $resultado->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id_materia_prima']; ?></td>
                            <td><?php echo htmlspecialchars($row['desc_materia_prima']); ?></td>
                            <td>R$ <?php echo number_format($row['preco_compra_mp'], 2, ',', '.'); ?></td>
                            <td><?php echo $row['icms_mp']; ?>%</td>
                            <td><?php echo $row['pis_mp']; ?>%</td>
                            <td><?php echo $row['cofins_mp']; ?>%</td>
                            <td><?php echo $row['ipi_mp']; ?>%</td>
                            <td><?php echo $row['ii_mp']; ?>%</td>
                            <td><?php echo $row['tipo_frete'] == 'perc' ? $row['frete_mp'] . '%' : 'R$ ' . number_format($row['frete_mp'], 2, ',', '.'); ?></td>
                            <td><?php echo $row['comissao_mp']; ?>%</td>
                            <td><strong>R$ <?php echo number_format($row['preco_final_mp'], 2, ',', '.'); ?></strong></td>
                            <td>
                                <a href="editar.php?id=<?php echo $row['id_materia_prima']; ?>" style="color: #003399;">Editar</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="12" style="text-align: center;">Nenhum produto cadastrado</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="../js/scripts.js"></script>
<script>
    // Inicializa os eventos do formulário
    inicializarNovoCadastro();
</script>

</body>
</html>
