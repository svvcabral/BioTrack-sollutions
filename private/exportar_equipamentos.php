<?php

require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/funcoes.php';

redirecionar_se_nao_autenticado();

$formato = strtolower(trim($_GET['formato'] ?? ''));
if (!in_array($formato, ['csv', 'json', 'pdf'], true)) {
    header('Location: equipamentos.php?erro=formato_exportacao');
    exit;
}

try {
    $ligacao = ligar_bd();
    $equipamentos = $ligacao->query(
        "SELECT e.codigo_interno AS codigo,
                e.designacao,
                c.nome AS categoria,
                e.marca,
                e.modelo,
                e.numero_serie,
                e.fabricante,
                e.data_aquisicao,
                e.ano_fabrico,
                e.custo_aquisicao,
                e.tipo_entrada,
                e.estado,
                e.criticidade,
                l.servico,
                l.edificio,
                l.piso,
                l.sala
         FROM equipamentos e
         INNER JOIN categorias c ON c.id_categoria = e.id_categoria
         INNER JOIN localizacoes l ON l.id_localizacao = e.id_localizacao
         WHERE e.ativo = 1
         ORDER BY e.codigo_interno"
    )->fetchAll();
} catch (PDOException $erro) {
    header('Location: equipamentos.php?erro=exportacao');
    exit;
}

$nome_base = 'equipamentos_' . date('Y-m-d_H-i-s');

if ($formato === 'csv') {
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $nome_base . '.csv"');
    echo "\xEF\xBB\xBF";
    $saida = fopen('php://output', 'w');
    if (!empty($equipamentos)) {
        fputcsv($saida, array_keys($equipamentos[0]), ';');
        foreach ($equipamentos as $equipamento) {
            fputcsv($saida, $equipamento, ';');
        }
    }
    fclose($saida);
    registar_log($ligacao, 'exportar_equipamentos', 'equipamentos', null, 'Formato CSV');
    exit;
}

if ($formato === 'json') {
    header('Content-Type: application/json; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $nome_base . '.json"');
    echo json_encode(
        ['gerado_em' => date(DATE_ATOM), 'total' => count($equipamentos), 'equipamentos' => $equipamentos],
        JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
    );
    registar_log($ligacao, 'exportar_equipamentos', 'equipamentos', null, 'Formato JSON');
    exit;
}

function texto_pdf(string $texto): string
{
    $texto = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $texto) ?: $texto;
    return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $texto);
}

function criar_pdf_equipamentos(array $equipamentos): string
{
    $linhas = [
        'BioTrack Solutions - Inventario de Equipamentos',
        'Gerado em: ' . date('d/m/Y H:i'),
        'Total de equipamentos ativos: ' . count($equipamentos),
        '',
    ];

    foreach ($equipamentos as $equipamento) {
        $linhas[] = $equipamento['codigo'] . ' | ' . $equipamento['designacao']
            . ' | ' . $equipamento['marca'] . ' ' . $equipamento['modelo']
            . ' | ' . $equipamento['servico']
            . ' | ' . str_replace('_', ' ', $equipamento['estado']);
    }

    $paginas = array_chunk($linhas, 45);
    $objetos = [];
    $objetos[1] = '<< /Type /Catalog /Pages 2 0 R >>';
    $ids_paginas = [];
    $proximo_id = 4;

    foreach ($paginas as $pagina) {
        $id_pagina = $proximo_id++;
        $id_conteudo = $proximo_id++;
        $ids_paginas[] = $id_pagina . ' 0 R';

        $stream = "BT\n/F1 10 Tf\n50 790 Td\n";
        foreach ($pagina as $indice => $linha) {
            if ($indice > 0) {
                $stream .= "0 -16 Td\n";
            }
            $stream .= '(' . texto_pdf((string) $linha) . ") Tj\n";
        }
        $stream .= "ET\n";

        $objetos[$id_pagina] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] '
            . '/Resources << /Font << /F1 3 0 R >> >> /Contents ' . $id_conteudo . ' 0 R >>';
        $objetos[$id_conteudo] = "<< /Length " . strlen($stream) . " >>\nstream\n" . $stream . "endstream";
    }

    $objetos[2] = '<< /Type /Pages /Kids [' . implode(' ', $ids_paginas) . '] /Count ' . count($ids_paginas) . ' >>';
    $objetos[3] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>';
    ksort($objetos);

    $pdf = "%PDF-1.4\n";
    $offsets = [0];
    foreach ($objetos as $id => $objeto) {
        $offsets[$id] = strlen($pdf);
        $pdf .= $id . " 0 obj\n" . $objeto . "\nendobj\n";
    }

    $xref = strlen($pdf);
    $max = max(array_keys($objetos));
    $pdf .= "xref\n0 " . ($max + 1) . "\n";
    $pdf .= "0000000000 65535 f \n";
    for ($id = 1; $id <= $max; $id++) {
        $pdf .= sprintf("%010d 00000 n \n", $offsets[$id] ?? 0);
    }
    $pdf .= "trailer\n<< /Size " . ($max + 1) . " /Root 1 0 R >>\n";
    $pdf .= "startxref\n" . $xref . "\n%%EOF";

    return $pdf;
}

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $nome_base . '.pdf"');
echo criar_pdf_equipamentos($equipamentos);
registar_log($ligacao, 'exportar_equipamentos', 'equipamentos', null, 'Formato PDF');
exit;
