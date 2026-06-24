USE db1241381;

INSERT INTO utilizadores
    (nome, email, palavra_passe, perfil, ativo)
SELECT
    'Técnico BioTrack',
    'tecnico@biotrack.pt',
    '$2y$12$OeaxVSh3Tc9vcPxkultv7.jLOWhasVLZ4GMKPCI2vz9GwRrn21px2',
    'tecnico',
    TRUE
WHERE NOT EXISTS (
    SELECT 1
    FROM utilizadores
    WHERE email = 'tecnico@biotrack.pt'
);
