USE db1241381;

-- Consulta 1: juncao de tabelas
-- Lista os equipamentos ativos com categoria e localizacao atual.
SELECT
    e.codigo_interno,
    e.designacao,
    c.nome AS categoria,
    e.estado,
    e.criticidade,
    l.servico,
    l.edificio,
    l.piso,
    l.sala
FROM equipamentos e
INNER JOIN categorias c
    ON c.id_categoria = e.id_categoria
INNER JOIN localizacoes l
    ON l.id_localizacao = e.id_localizacao
WHERE e.ativo = 1
ORDER BY l.servico, e.designacao;

-- Consulta 2: subconsulta
-- Identifica equipamentos ativos sem qualquer documento associado.
SELECT
    e.codigo_interno,
    e.designacao,
    e.marca,
    e.modelo
FROM equipamentos e
WHERE e.ativo = 1
  AND NOT EXISTS (
      SELECT 1
      FROM documentos d
      WHERE d.id_equipamento = e.id_equipamento
  )
ORDER BY e.designacao;
