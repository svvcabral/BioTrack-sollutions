USE db1241381;

INSERT INTO utilizadores
    (nome, email, palavra_passe, perfil, ativo)
VALUES
    (
        'Maria Loureiro',
        'admin@biotrack.pt',
        '$2y$12$ZMr2b.P3Tb/liEYe3gcZ4OkyETkGFIpZczgwiSBNgtyBOB4H7p.fO',
        'administrador',
        TRUE
    ),
    (
        'Técnico BioTrack',
        'tecnico@biotrack.pt',
        '$2y$12$OeaxVSh3Tc9vcPxkultv7.jLOWhasVLZ4GMKPCI2vz9GwRrn21px2',
        'tecnico',
        TRUE
    );

INSERT INTO categorias (nome, descricao) VALUES
    ('Monitorização', 'Equipamentos de monitorização de parâmetros fisiológicos'),
    ('Suporte de vida', 'Equipamentos essenciais para manter funções vitais'),
    ('Terapia', 'Equipamentos utilizados em tratamentos clínicos'),
    ('Diagnóstico', 'Equipamentos utilizados na avaliação clínica'),
    ('Laboratório', 'Equipamentos utilizados em análises clínicas');

INSERT INTO localizacoes (edificio, piso, servico, sala) VALUES
    ('Edifício Principal', 'Piso 3', 'Unidade de Cuidados Intensivos', 'UCI-01'),
    ('Edifício Sul', 'Piso 0', 'Urgência', 'URG-02'),
    ('Edifício Principal', 'Piso 2', 'Bloco Operatório', 'BO-03'),
    ('Edifício Norte', 'Piso -1', 'Imagiologia', 'IMG-01'),
    ('Edifício Principal', 'Piso 4', 'Medicina Interna', 'MED-12');

    INSERT INTO fornecedores
    (
        nome,
        nif,
        telefone,
        email,
        morada,
        website,
        pessoa_contacto,
        telefone_contacto,
        tipo,
        observacoes
    )
VALUES
    (
        'Philips Portuguesa, S.A.',
        '500069283',
        '214164200',
        'healthcare.portugal@philips.com',
        'Lagoas Park, Edifício 14, 2740-262 Porto Salvo',
        'https://www.philips.pt/healthcare',
        'Departamento de Suporte Técnico',
        '214164200',
        'fabricante',
        'Fabricante de equipamentos de monitorização e diagnóstico.'
    ),
    (
        'Dräger Portugal, Lda.',
        '503208123',
        '214241750',
        'info.portugal@draeger.com',
        'Av. do Forte, 6-6A, 2790-072 Carnaxide',
        'https://www.draeger.com/pt_pt',
        'Assistência Técnica',
        '214241750',
        'assistencia_tecnica',
        'Assistência técnica de ventiladores e equipamentos de suporte de vida.'
    ),
    (
        'B. Braun Medical, Lda.',
        '501506543',
        '214368200',
        'geral.pt@bbraun.com',
        'Est. Consiglieri Pedroso, 80, 2730-053 Barcarena',
        'https://www.bbraun.pt',
        'Apoio ao Cliente',
        '214368200',
        'distribuidor',
        'Distribuidor de bombas de infusão e consumíveis hospitalares.'
    );

    INSERT INTO equipamentos
    (
        codigo_interno,
        designacao,
        id_categoria,
        marca,
        modelo,
        numero_serie,
        fabricante,
        data_aquisicao,
        ano_fabrico,
        custo_aquisicao,
        tipo_entrada,
        estado,
        criticidade,
        observacoes,
        id_localizacao
    )
VALUES
    (
        '04.002.00',
        'Monitor Multiparamétrico',
        1,
        'Philips',
        'IntelliVue MP5',
        'MP5-2022-45873',
        'Philips',
        '2022-04-18',
        2022,
        7850.00,
        'compra',
        'ativo',
        'alta',
        'Monitor utilizado na vigilância contínua de doentes.',
        1
    ),
    (
        '05.001.00',
        'Ventilador Pulmonar',
        2,
        'Dräger',
        'Evita V500',
        'EV500-2021-9934',
        'Dräger',
        '2021-09-07',
        2021,
        28500.00,
        'compra',
        'ativo',
        'suporte_de_vida',
        'Equipamento destinado ao suporte ventilatório invasivo.',
        1
    ),
    (
        '06.014.00',
        'Bomba de Infusão',
        3,
        'B. Braun',
        'Infusomat Space',
        'INF-2020-88321',
        'B. Braun',
        '2020-02-12',
        2020,
        2450.00,
        'compra',
        'em_calibracao',
        'media',
        'Bomba de infusão volumétrica para administração de terapêutica.',
        5
    ),
    (
        '05.008.00',
        'Desfibrilhador',
        2,
        'Zoll',
        'R Series',
        'ZR-2021-7712',
        'Zoll',
        '2021-06-22',
        2021,
        16750.00,
        'compra',
        'em_manutencao',
        'suporte_de_vida',
        'Desfibrilhador utilizado no serviço de urgência.',
        2
    );

    INSERT INTO equipamentos_fornecedores
    (id_equipamento, id_fornecedor, funcao, data_inicio)
VALUES
    (1, 1, 'fabricante', '2022-04-18'),
    (2, 2, 'fabricante', '2021-09-07'),
    (2, 2, 'assistencia_tecnica', '2021-09-07'),
    (3, 3, 'distribuidor', '2020-02-12');

INSERT INTO garantias
    (id_equipamento, data_inicio, data_fim, entidade_responsavel, observacoes)
VALUES
    (1, '2022-04-18', '2025-04-17', 'Philips Portuguesa, S.A.', 'Garantia geral de três anos.'),
    (2, '2021-09-07', '2024-09-06', 'Dräger Portugal, Lda.', 'Garantia do fabricante expirada.'),
    (3, '2020-02-12', '2022-02-11', 'B. Braun Medical, Lda.', 'Garantia comercial expirada.');

INSERT INTO contratos_manutencao
    (
        id_equipamento,
        id_fornecedor,
        tipo_contrato,
        data_inicio,
        data_fim,
        periodicidade,
        observacoes
    )
VALUES
    (
        2,
        2,
        'Manutenção preventiva e corretiva',
        '2024-09-07',
        '2027-09-06',
        'Semestral',
        'Inclui deslocação, peças e mão de obra.'
    );

INSERT INTO documentos
    (
        tipo,
        nome,
        data_documento,
        data_validade,
        caminho_ficheiro,
        id_equipamento,
        id_fornecedor
    )
VALUES
    (
        'manual_utilizador',
        'Manual de Utilizador IntelliVue MP5',
        '2022-04-18',
        NULL,
        'uploads/manual_intellivue_mp5.pdf',
        1,
        1
    ),
    (
        'certificado_calibracao',
        'Certificado de Calibração 2025',
        '2025-03-12',
        '2026-03-12',
        'uploads/certificado_calibracao_mp5_2025.pdf',
        1,
        1
    ),
    (
        'contrato_manutencao',
        'Contrato de Manutenção Evita V500',
        '2024-09-07',
        '2027-09-06',
        'uploads/contrato_manutencao_evita_v500.pdf',
        2,
        2
    );

    INSERT INTO conteudos_publicos
    (chave, titulo, conteudo, atualizado_por)
VALUES
    (
        'hero',
        'A nova era da gestão de Tecnologia Médica',
        'Mapeamento em tempo real, gestão de ciclo de vida e mitigação de falhas para dispositivos médicos de suporte crítico.',
        1
    ),
    (
        'visao',
        'Da Engenharia Biomédica para a Prática Clínica',
        'O BioTrack organiza informação crítica sobre equipamentos, documentação, fornecedores e localizações hospitalares.',
        1
    ),
    (
        'contacto_email',
        'Email de Suporte',
        'suporte@biotrack.pt',
        1
    ),
    (
        'contacto_telefone',
        'Telefone Geral',
        '+351 228 340 500',
        1
    ),
    (
        'contacto_morada',
        'Morada',
        'Rua Dr. António Bernardino de Almeida, 4200-072 Porto',
        1
    );

INSERT INTO logs
    (id_utilizador, evento, entidade, detalhes, endereco_ip)
VALUES
    (
        1,
        'sistema_inicializado',
        'sistema',
        'Dados iniciais da aplicação BioTrack Solutions carregados.',
        '127.0.0.1'
    );
