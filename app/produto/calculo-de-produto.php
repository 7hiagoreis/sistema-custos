<?php

// Inicializa as variáveis
$preco_compra = 0;
$icms = $pis = $cofins = $ipi = $ii = $frete = $comissao = 0;
$tipo_frete = 'perc'; // perc ou real
$tipo_metragem = '';

$total_icms = $total_pis = $total_cofins = $total_ipi = $total_ii = 0;
$total_frete = $total_comissao = 0;
$preco_final = 0;
$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Valida e captura o preço de compra
    $preco_compra = filter_input(INPUT_POST, 'preco_compra', FILTER_VALIDATE_FLOAT);
    if ($preco_compra === false || $preco_compra <= 0) {
        $erros[] = 'Preço de compra inválido';
        $preco_compra = 0;
    }
    
    // Valida os percentuais
    $campos_percentuais = ['icms', 'pis', 'cofins', 'ipi', 'ii', 'comissao'];
    foreach ($campos_percentuais as $campo) {
        $$campo = filter_input(INPUT_POST, $campo, FILTER_VALIDATE_FLOAT);
        if ($$campo === false) {
            $$campo = 0;
        }
    }
    
    // Valida o frete
    $tipo_frete = $_POST['tipo_frete'] ?? 'perc';
    $frete_valor = filter_input(INPUT_POST, 'frete', FILTER_VALIDATE_FLOAT);
    if ($frete_valor === false) {
        $frete_valor = 0;
    }
    
    $tipo_metragem = $_POST['metragem'] ?? '';
    
    // Realiza os cálculos apenas se não houver erros
    if (empty($erros)) {
        // Cálculo dos impostos (sempre percentuais sobre o preço de compra)
        $total_icms = $preco_compra * $icms / 100;
        $total_pis = $preco_compra * $pis / 100;
        $total_cofins = $preco_compra * $cofins / 100;
        $total_ipi = $preco_compra * $ipi / 100;
        $total_ii = $preco_compra * $ii / 100;
        $total_comissao = $preco_compra * $comissao / 100;
        
        // Cálculo do frete (pode ser percentual ou valor fixo)
        if ($tipo_frete === 'perc') {
            $total_frete = $preco_compra * $frete_valor / 100;
        } else {
            $total_frete = $frete_valor;
        }
        
        // Preço final = compra + todos os impostos e despesas
        $total_impostos = $total_icms + $total_pis + $total_cofins + $total_ipi + $total_ii;
        $total_despesas = $total_frete + $total_comissao;
        $preco_final = $preco_compra + $total_impostos + $total_despesas;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Calculadora de Custos de Produtos</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .erro { color: red; margin: 10px 0; padding: 10px; border: 1px solid red; background: #ffeeee; }
        table { border-collapse: collapse; width: 100%; max-width: 900px; }
        td, th { padding: 8px; border: 1px solid #ddd; }
        input[type="text"] { width: 100%; padding: 5px; }
        input[readonly] { background-color: #f5f5f5; }
        .campo-resultado { background-color: #e8f5e9; font-weight: bold; }
        button { padding: 10px 20px; margin: 5px; cursor: pointer; }
    </style>
</head>
<body>

<h2>Cadastro de Matéria Prima - Cálculo de Custos</h2>

<?php if (!empty($erros)): ?>
    <div class="erro">
        <strong>Erros encontrados:</strong><br>
        <?php echo implode('<br>', $erros); ?>
    </div>
<?php endif; ?>

<form action="" method="POST" id="form">
    <table>
        <tr>
            <td width="200">Descrição:</td>
            <td colspan="2"><input type="text" name="desc_prod" id="desc_prod" value="<?php echo htmlspecialchars($_POST['desc_prod'] ?? ''); ?>" /></td>
        </tr>
        <tr>
            <td>Preço de compra:</td>
            <td width="300">
                <input type="text" name="preco_compra" id="preco_compra" 
                       value="<?php echo htmlspecialchars($preco_compra ?: ''); ?>" />
            </td>
            <td>
                <input type="radio" name="metragem" value="lt" id="metragem_lt" <?php echo ($tipo_metragem == 'lt') ? 'checked' : ''; ?> />
                <label for="metragem_lt">LT</label>
                <input type="radio" name="metragem" value="kg" id="metragem_kg" <?php echo ($tipo_metragem == 'kg') ? 'checked' : ''; ?> />
                <label for="metragem_kg">KG</label>
            </td>
        </tr>
        <tr>
            <td>ICMS (%):</td>
            <td><input type="text" name="icms" id="icms" value="<?php echo htmlspecialchars($icms ?: ''); ?>" /></td>
            <td><input type="text" name="per_icms" value="R$ <?php echo number_format($total_icms, 2); ?>" readonly /></td>
        </tr>
        <tr>
            <td>PIS (%):</td>
            <td><input type="text" name="pis" id="pis" value="<?php echo htmlspecialchars($pis ?: ''); ?>" /></td>
            <td><input type="text" name="per_pis" value="R$ <?php echo number_format($total_pis, 2); ?>" readonly /></td>
        </tr>
        <tr>
            <td>COFINS (%):</td>
            <td><input type="text" name="cofins" id="cofins" value="<?php echo htmlspecialchars($cofins ?: ''); ?>" /></td>
            <td><input type="text" name="per_cofins" value="R$ <?php echo number_format($total_cofins, 2); ?>" readonly /></td>
        </tr>
        <tr>
            <td>IPI (%):</td>
            <td><input type="text" name="ipi" id="ipi" value="<?php echo htmlspecialchars($ipi ?: ''); ?>" /></td>
            <td><input type="text" name="per_ipi" value="R$ <?php echo number_format($total_ipi, 2); ?>" readonly /></td>
        </tr>
        <tr>
            <td>II (%):</td>
            <td><input type="text" name="ii" id="ii" value="<?php echo htmlspecialchars($ii ?: ''); ?>" /></td>
            <td><input type="text" name="per_ii" value="R$ <?php echo number_format($total_ii, 2); ?>" readonly /></td>
        </tr>
        <tr>
            <td>Frete:</td>
            <td>
                <input type="text" name="frete" id="frete" value="<?php echo htmlspecialchars($frete_valor ?? ''); ?>" />
                <br>
                <input type="radio" name="tipo_frete" value="perc" id="frete_perc" <?php echo ($tipo_frete == 'perc') ? 'checked' : ''; ?> />
                <label for="frete_perc">%</label>
                <input type="radio" name="tipo_frete" value="real" id="frete_real" <?php echo ($tipo_frete == 'real') ? 'checked' : ''; ?> />
                <label for="frete_real">R$ Fixo</label>
            </td>
            <td><input type="text" name="per_frete" value="R$ <?php echo number_format($total_frete, 2); ?>" readonly /></td>
        </tr>
        <tr>
            <td>Comissão (%):</td>
            <td><input type="text" name="comissao" id="comissao" value="<?php echo htmlspecialchars($comissao ?: ''); ?>" /></td>
            <td><input type="text" name="per_comissao" value="R$ <?php echo number_format($total_comissao, 2); ?>" readonly /></td>
        </tr>
        <tr style="background-color: #e8f5e9; font-weight: bold;">
            <td>Preço Final:</td>
            <td colspan="2">
                <input type="text" name="preco_final" style="font-size: 18px; font-weight: bold; color: #2e7d32;" 
                       value="R$ <?php echo number_format($preco_final, 2); ?>" readonly />
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <button type="submit" name="bt_calcular">Calcular</button>
                <button type="reset" name="bt_limpar">Limpar</button>
            </td>
        </tr>
    </table>
</form>

</body>
</html>
