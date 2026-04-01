// js/scripts.js

// Foco no campo busca ao carregar
window.onload = function() {
    var campoBusca = document.getElementById('busca');
    if (campoBusca) {
        campoBusca.focus();
    }
};

// Máscara de moeda
function aplicarMascaraMoeda(elemento) {
    let valor = elemento.value;
    valor = valor.replace(/\D/g, ""); // remove tudo que não é número
    valor = (valor / 100).toFixed(2) + "";
    valor = valor.replace(".", ",");
    valor = valor.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
    elemento.value = valor;
}

// Converte string com máscara para número
function converterParaNumero(valor) {
    if (!valor) return 0;
    valor = valor.toString();
    valor = valor.replace(/\./g, "").replace(",", ".");
    return parseFloat(valor) || 0;
}

// Calcula o valor de imposto/percentual
function calcularValorPercentual(preco, percentual) {
    return (preco * percentual) / 100;
}

// Função principal para atualizar todos os cálculos
function atualizarCalculos(linha) {
    let precoCompra = converterParaNumero(linha.querySelector('.preco_compra').value);
    
    // Percentuais
    let icms = parseFloat(linha.querySelector('.icms').value) || 0;
    let pis = parseFloat(linha.querySelector('.pis').value) || 0;
    let cofins = parseFloat(linha.querySelector('.cofins').value) || 0;
    let ipi = parseFloat(linha.querySelector('.ipi').value) || 0;
    let ii = parseFloat(linha.querySelector('.ii').value) || 0;
    let comissao = parseFloat(linha.querySelector('.comissao').value) || 0;
    
    // Calcula valores
    let totalIcms = calcularValorPercentual(precoCompra, icms);
    let totalPis = calcularValorPercentual(precoCompra, pis);
    let totalCofins = calcularValorPercentual(precoCompra, cofins);
    let totalIpi = calcularValorPercentual(precoCompra, ipi);
    let totalIi = calcularValorPercentual(precoCompra, ii);
    let totalComissao = calcularValorPercentual(precoCompra, comissao);
    
    // Cálculo do frete (pode ser percentual ou valor fixo)
    let tipoFrete = linha.querySelector('.tipo_frete:checked').value;
    let valorFrete = converterParaNumero(linha.querySelector('.frete_valor').value);
    let totalFrete = tipoFrete === 'perc' 
        ? calcularValorPercentual(precoCompra, valorFrete)
        : valorFrete;
    
    // Atualiza os campos de resultado
    linha.querySelector('.per_icms').value = formatarMoeda(totalIcms);
    linha.querySelector('.per_pis').value = formatarMoeda(totalPis);
    linha.querySelector('.per_cofins').value = formatarMoeda(totalCofins);
    linha.querySelector('.per_ipi').value = formatarMoeda(totalIpi);
    linha.querySelector('.per_ii').value = formatarMoeda(totalIi);
    linha.querySelector('.per_frete').value = formatarMoeda(totalFrete);
    linha.querySelector('.per_comissao').value = formatarMoeda(totalComissao);
    
    // Preço final = compra + todos os impostos e despesas
    let totalImpostos = totalIcms + totalPis + totalCofins + totalIpi + totalIi;
    let totalDespesas = totalFrete + totalComissao;
    let precoFinal = precoCompra + totalImpostos + totalDespesas;
    
    linha.querySelector('.preco_final').value = formatarMoeda(precoFinal);
}

// Formata o número para moeda brasileira (real)
function formatarMoeda(valor) {
    return 'R$ ' + valor.toLocaleString('pt-BR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

// Eventos para atualização em tempo real
function inicializarEventos(linha) {
    let campos = linha.querySelectorAll('.preco_compra, .icms, .pis, .cofins, .ipi, .ii, .comissao, .frete_valor');
    campos.forEach(campo => {
        campo.addEventListener('input', () => atualizarCalculos(linha));
    });
    
    let radiosFrete = linha.querySelectorAll('.tipo_frete');
    radiosFrete.forEach(radio => {
        radio.addEventListener('change', () => atualizarCalculos(linha));
    });
}

// Para o formulário de novo cadastro
function inicializarNovoCadastro() {
    let linha = document.getElementById('form-cadastro');
    if (linha) {
        inicializarEventos(linha);
    }
}
