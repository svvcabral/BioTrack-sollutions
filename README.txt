BIOTRACK SOLUTIONS

Estudante: Sofia Vieira Velho Cabral
Numero de estudante: 1241381
Curso: Licenciatura em Engenharia Biomedica - ISEP

1. DESCRICAO

O BioTrack Solutions e uma aplicacao web para gestao do inventario hospitalar
de equipamentos medicos. Inclui uma area publica institucional e uma area
privada com autenticacao, dashboard, equipamentos, localizacoes, fornecedores,
documentacao, garantias e contratos de manutencao.

2. TECNOLOGIAS

- HTML5, CSS3, JavaScript e Bootstrap
- PHP 8
- MySQL
- Chart.js e Font Awesome

3. INSTALACAO

1. Extrair a pasta "biotrack-solutions" para:
   /Applications/MAMP/htdocs/sibdas/1241381/
2. Iniciar os servidores Apache e MySQL no MAMP.
3. Importar o ficheiro database/biotrack_completo.sql na base de dados
   db1241381, utilizando o DBeaver.
4. Confirmar em config/config.php os dados de acesso ao servidor de base de
   dados disponibilizado nas aulas.
5. Abrir no navegador:
   http://127.0.0.1/sibdas/1241381/biotrack-solutions/

4. CREDENCIAIS

Administrador:
- Email: admin@biotrack.pt
- Palavra-passe: BioTrack2026!

Tecnico:
- Email: tecnico@biotrack.pt
- Palavra-passe: Tecnico2026!

5. TESTES PRINCIPAIS

Autenticacao:
- Tentar abrir /private/dashboard.php sem login e confirmar o redirecionamento.
- Entrar com cada perfil e confirmar as opcoes disponiveis.
- Terminar sessao e confirmar que a area privada deixa de estar acessivel.

Equipamentos:
- Registar um equipamento com dados validos.
- Testar campos obrigatorios, data futura, ano invalido e custo nao numerico.
- Pesquisar e combinar filtros por servico, estado, fornecedor, categoria e
  criticidade.
- Abrir a ficha detalhada, editar e arquivar um equipamento.
- Associar fornecedor, garantia, contrato e documento.

Fornecedores e localizacoes:
- Inserir, pesquisar, filtrar, editar e arquivar registos.
- Confirmar que uma localizacao com equipamentos ativos nao pode ser arquivada.

Dashboard:
- Comparar o total de equipamentos com a base de dados.
- Confirmar as metricas e os graficos por criticidade e localizacao.

Portal publico:
- Alterar textos e contactos em "Portal Publico".
- Abrir o site publico e confirmar que as alteracoes ficaram guardadas.

Seguranca:
- Confirmar que os identificadores apresentados nos URLs estao protegidos.
- Testar a alteracao da palavra-passe atual.
- Confirmar as restricoes do perfil Tecnico.

6. ESTRUTURA

- public/: paginas acessiveis sem autenticacao
- private/: paginas protegidas e funcionalidades de gestao
- assets/: CSS, JavaScript, imagens e bibliotecas locais
- config/: configuracao da aplicacao e da base de dados
- database/: DBML e scripts SQL

7. INFORMACAO ADICIONAL

A remocao de equipamentos, fornecedores e localizacoes e realizada por arquivo
logico, preservando o historico dos registos.
