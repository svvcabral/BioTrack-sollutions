<?php

function validar_data_iso(string $data): bool
{
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) {
        return false;
    }

    [$ano, $mes, $dia] = array_map('intval', explode('-', $data));

    return checkdate($mes, $dia, $ano);
}

function validar_texto_obrigatorio(string $valor, string $campo): array
{
    return trim($valor) === '' ? ['O campo ' . $campo . ' é obrigatório.'] : [];
}

function validar_nome_sem_numeros(string $nome, string $campo): array
{
    $erros = validar_texto_obrigatorio($nome, $campo);

    if (empty($erros) && preg_match('/\d/', $nome)) {
        $erros[] = 'O campo ' . $campo . ' não pode conter números.';
    }

    return $erros;
}

function validar_equipamento(array $dados): array
{
    $erros = [];
    $erros = array_merge($erros, validar_texto_obrigatorio($dados['codigo_interno'] ?? '', 'Código interno'));
    $erros = array_merge($erros, validar_texto_obrigatorio($dados['designacao'] ?? '', 'Designação'));
    $erros = array_merge($erros, validar_texto_obrigatorio($dados['marca'] ?? '', 'Marca'));
    $erros = array_merge($erros, validar_texto_obrigatorio($dados['modelo'] ?? '', 'Modelo'));
    $erros = array_merge($erros, validar_texto_obrigatorio($dados['numero_serie'] ?? '', 'Número de série'));
    $erros = array_merge($erros, validar_texto_obrigatorio($dados['fabricante'] ?? '', 'Fabricante'));

    if (($dados['id_categoria'] ?? '') === '' || !ctype_digit((string) $dados['id_categoria'])) {
        $erros[] = 'A Categoria é obrigatória.';
    }

    $data = (string) ($dados['data_aquisicao'] ?? '');
    if ($data !== '' && !validar_data_iso($data)) {
        $erros[] = 'A Data de aquisição deve ser uma data válida.';
    } elseif ($data !== '' && $data > date('Y-m-d')) {
        $erros[] = 'A Data de aquisição não pode ser posterior à data atual.';
    }

    $ano = (string) ($dados['ano_fabrico'] ?? '');
    if ($ano !== '' && (!ctype_digit($ano) || (int) $ano < 1980 || (int) $ano > (int) date('Y'))) {
        $erros[] = 'O Ano de fabrico deve estar entre 1980 e o ano atual.';
    }

    $custo = str_replace(',', '.', (string) ($dados['custo_aquisicao'] ?? ''));
    if ($custo !== '' && !is_numeric($custo)) {
        $erros[] = 'O Custo de aquisição deve ser numérico.';
    }

    if (!in_array($dados['tipo_entrada'] ?? '', ['compra', 'doacao', 'aluguer', 'emprestimo'], true)) {
        $erros[] = 'O Tipo de entrada não é válido.';
    }
    if (!in_array($dados['estado'] ?? '', ['ativo', 'em_manutencao', 'inativo', 'em_calibracao', 'em_quarentena', 'abatido'], true)) {
        $erros[] = 'O Estado não é válido.';
    }
    if (!in_array($dados['criticidade'] ?? '', ['baixa', 'media', 'alta', 'suporte_de_vida'], true)) {
        $erros[] = 'A Criticidade não é válida.';
    }
    if (($dados['id_localizacao'] ?? '') === '' || !ctype_digit((string) $dados['id_localizacao'])) {
        $erros[] = 'A Localização é obrigatória.';
    }

    return $erros;
}

function validar_fornecedor(array $dados): array
{
    $erros = validar_nome_sem_numeros((string) ($dados['nome'] ?? ''), 'Nome da empresa');

    if (!preg_match('/^\d{9}$/', (string) ($dados['nif'] ?? ''))) {
        $erros[] = 'O NIF deve ter exatamente 9 dígitos.';
    }
    if (!filter_var($dados['email'] ?? '', FILTER_VALIDATE_EMAIL)) {
        $erros[] = 'O email não é válido.';
    }
    foreach (['telefone' => 'telefone', 'telefone_contacto' => 'telefone de contacto'] as $campo => $rotulo) {
        $valor = (string) ($dados[$campo] ?? '');
        if ($valor !== '' && !preg_match('/^[29]\d{8}$/', $valor)) {
            $erros[] = 'O ' . $rotulo . ' deve ter 9 dígitos e começar por 2 ou 9.';
        }
    }
    $website = (string) ($dados['website'] ?? '');
    if ($website !== '' && !filter_var($website, FILTER_VALIDATE_URL)) {
        $erros[] = 'O website não é válido.';
    }
    if (!in_array($dados['tipo'] ?? '', ['fabricante', 'distribuidor', 'assistencia_tecnica', 'consumiveis'], true)) {
        $erros[] = 'O tipo de fornecedor não é válido.';
    }

    return $erros;
}

function validar_localizacao(array $dados): array
{
    $erros = [];
    $erros = array_merge($erros, validar_texto_obrigatorio($dados['servico'] ?? '', 'Serviço/Departamento'));
    $erros = array_merge($erros, validar_texto_obrigatorio($dados['edificio'] ?? '', 'Edifício'));
    $erros = array_merge($erros, validar_texto_obrigatorio($dados['piso'] ?? '', 'Piso'));

    return $erros;
}
