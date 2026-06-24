-- MySQL dump 10.13  Distrib 8.0.44, for macos12.7 (arm64)
--
-- Host: vsgate-s1.dei.isep.ipp.pt    Database: db1241381
-- ------------------------------------------------------
-- Server version	8.0.45

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `categorias`
--

DROP TABLE IF EXISTS `categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categorias` (
  `id_categoria` int unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8mb4_bin NOT NULL,
  `descricao` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  PRIMARY KEY (`id_categoria`),
  UNIQUE KEY `nome` (`nome`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias`
--

LOCK TABLES `categorias` WRITE;
/*!40000 ALTER TABLE `categorias` DISABLE KEYS */;
INSERT INTO `categorias` VALUES (1,'Monitorização','Equipamentos de monitorização de parâmetros fisiológicos'),(2,'Suporte de vida','Equipamentos essenciais para manter funções vitais'),(3,'Terapia','Equipamentos utilizados em tratamentos clínicos'),(4,'Diagnóstico','Equipamentos utilizados na avaliação clínica'),(5,'Laboratório','Equipamentos utilizados em análises clínicas');
/*!40000 ALTER TABLE `categorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `conteudos_publicos`
--

DROP TABLE IF EXISTS `conteudos_publicos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `conteudos_publicos` (
  `id_conteudo` int unsigned NOT NULL AUTO_INCREMENT,
  `chave` varchar(100) COLLATE utf8mb4_bin NOT NULL,
  `titulo` varchar(200) COLLATE utf8mb4_bin DEFAULT NULL,
  `conteudo` text COLLATE utf8mb4_bin NOT NULL,
  `atualizado_em` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `atualizado_por` int unsigned NOT NULL,
  PRIMARY KEY (`id_conteudo`),
  UNIQUE KEY `chave` (`chave`),
  KEY `fk_conteudo_utilizador` (`atualizado_por`),
  CONSTRAINT `fk_conteudo_utilizador` FOREIGN KEY (`atualizado_por`) REFERENCES `utilizadores` (`id_utilizador`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `conteudos_publicos`
--

LOCK TABLES `conteudos_publicos` WRITE;
/*!40000 ALTER TABLE `conteudos_publicos` DISABLE KEYS */;
INSERT INTO `conteudos_publicos` VALUES (1,'hero','A nova era da gestão de Tecnologia Médica','Mapeamento em tempo real, gestão de ciclo de vida e mitigação de falhas para dispositivos médicos de suporte crítico.','2026-06-23 15:59:38',1),(2,'visao','Da Engenharia Biomédica para a Prática Clínica','O BioTrack organiza informação crítica sobre equipamentos, documentação, fornecedores e localizações hospitalares.','2026-06-23 15:59:38',1),(3,'contacto_email','Email de Suporte','suporte@biotrack.pt','2026-06-23 15:59:38',1),(4,'contacto_telefone','Telefone Geral','+351 228 340 500','2026-06-23 15:59:38',1),(5,'contacto_morada','Morada','Rua Dr. António Bernardino de Almeida, 4200-072 Porto','2026-06-23 15:59:38',1);
/*!40000 ALTER TABLE `conteudos_publicos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contratos_manutencao`
--

DROP TABLE IF EXISTS `contratos_manutencao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contratos_manutencao` (
  `id_contrato` int unsigned NOT NULL AUTO_INCREMENT,
  `id_equipamento` int unsigned NOT NULL,
  `id_fornecedor` int unsigned NOT NULL,
  `tipo_contrato` varchar(100) COLLATE utf8mb4_bin NOT NULL,
  `data_inicio` date NOT NULL,
  `data_fim` date DEFAULT NULL,
  `periodicidade` varchar(50) COLLATE utf8mb4_bin DEFAULT NULL,
  `observacoes` text COLLATE utf8mb4_bin,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_contrato`),
  KEY `fk_contrato_equipamento` (`id_equipamento`),
  KEY `fk_contrato_fornecedor` (`id_fornecedor`),
  CONSTRAINT `fk_contrato_equipamento` FOREIGN KEY (`id_equipamento`) REFERENCES `equipamentos` (`id_equipamento`),
  CONSTRAINT `fk_contrato_fornecedor` FOREIGN KEY (`id_fornecedor`) REFERENCES `fornecedores` (`id_fornecedor`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contratos_manutencao`
--

LOCK TABLES `contratos_manutencao` WRITE;
/*!40000 ALTER TABLE `contratos_manutencao` DISABLE KEYS */;
INSERT INTO `contratos_manutencao` VALUES (1,2,2,'Manutenção preventiva e corretiva','2024-09-07','2027-09-06','Semestral','Inclui deslocação, peças e mão de obra.',1),(2,23,1,'Manutenção preventiva e corretiva','2026-01-01','2028-12-31','Semestral','Inclui inspeção, calibração e substituição de componentes.',1),(3,39,2,'hjhjh','2026-06-04','2026-06-04',NULL,NULL,1),(4,39,2,'Manutenção preventiva','2026-06-04','2029-06-03','Semestral','Inclui inspeção técnica, testes funcionais e calibração.',1);
/*!40000 ALTER TABLE `contratos_manutencao` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `documentos`
--

DROP TABLE IF EXISTS `documentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `documentos` (
  `id_documento` int unsigned NOT NULL AUTO_INCREMENT,
  `tipo` enum('manual_utilizador','manual_servico','certificado_calibracao','contrato_manutencao','fatura','declaracao_conformidade','relatorio_tecnico','outro') COLLATE utf8mb4_bin NOT NULL,
  `nome` varchar(150) COLLATE utf8mb4_bin NOT NULL,
  `data_documento` date DEFAULT NULL,
  `data_validade` date DEFAULT NULL,
  `caminho_ficheiro` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  `id_equipamento` int unsigned NOT NULL,
  `id_fornecedor` int unsigned DEFAULT NULL,
  `criado_em` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_documento`),
  KEY `fk_documento_equipamento` (`id_equipamento`),
  KEY `fk_documento_fornecedor` (`id_fornecedor`),
  CONSTRAINT `fk_documento_equipamento` FOREIGN KEY (`id_equipamento`) REFERENCES `equipamentos` (`id_equipamento`),
  CONSTRAINT `fk_documento_fornecedor` FOREIGN KEY (`id_fornecedor`) REFERENCES `fornecedores` (`id_fornecedor`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documentos`
--

LOCK TABLES `documentos` WRITE;
/*!40000 ALTER TABLE `documentos` DISABLE KEYS */;
INSERT INTO `documentos` VALUES (1,'manual_utilizador','Manual de Utilizador IntelliVue MP5','2022-04-18',NULL,'uploads/manual_intellivue_mp5.pdf',1,1,'2026-06-23 15:59:38'),(2,'certificado_calibracao','Certificado de Calibração 2025','2025-03-12','2026-03-12','uploads/certificado_calibracao_mp5_2025.pdf',1,1,'2026-06-23 15:59:38'),(3,'contrato_manutencao','Contrato de Manutenção Evita V500','2024-09-07','2027-09-06','uploads/contrato_manutencao_evita_v500.pdf',2,2,'2026-06-23 15:59:38'),(4,'manual_utilizador','Manual Intellivue MP5','2022-04-18',NULL,'uploads/manual_intellivue_mp5.pdf',23,1,'2026-06-23 22:35:22'),(5,'manual_utilizador','Manual técnico do equipamento','2026-06-04',NULL,'uploads/manual-tecnico.pdf',39,2,'2026-06-23 23:53:16');
/*!40000 ALTER TABLE `documentos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `equipamentos`
--

DROP TABLE IF EXISTS `equipamentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `equipamentos` (
  `id_equipamento` int unsigned NOT NULL AUTO_INCREMENT,
  `codigo_interno` varchar(50) COLLATE utf8mb4_bin NOT NULL,
  `designacao` varchar(150) COLLATE utf8mb4_bin NOT NULL,
  `id_categoria` int unsigned NOT NULL,
  `marca` varchar(100) COLLATE utf8mb4_bin NOT NULL,
  `modelo` varchar(100) COLLATE utf8mb4_bin NOT NULL,
  `numero_serie` varchar(100) COLLATE utf8mb4_bin NOT NULL,
  `fabricante` varchar(150) COLLATE utf8mb4_bin NOT NULL,
  `data_aquisicao` date DEFAULT NULL,
  `ano_fabrico` year DEFAULT NULL,
  `custo_aquisicao` decimal(12,2) DEFAULT NULL,
  `tipo_entrada` enum('compra','doacao','aluguer','emprestimo') COLLATE utf8mb4_bin NOT NULL,
  `estado` enum('ativo','em_manutencao','inativo','em_calibracao','em_quarentena','abatido') COLLATE utf8mb4_bin NOT NULL,
  `criticidade` enum('baixa','media','alta','suporte_de_vida') COLLATE utf8mb4_bin NOT NULL,
  `observacoes` text COLLATE utf8mb4_bin,
  `id_localizacao` int unsigned NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `criado_em` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_equipamento`),
  UNIQUE KEY `codigo_interno` (`codigo_interno`),
  UNIQUE KEY `uq_equipamento_serie` (`fabricante`,`modelo`,`numero_serie`),
  KEY `fk_equipamento_categoria` (`id_categoria`),
  KEY `fk_equipamento_localizacao` (`id_localizacao`),
  CONSTRAINT `fk_equipamento_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`),
  CONSTRAINT `fk_equipamento_localizacao` FOREIGN KEY (`id_localizacao`) REFERENCES `localizacoes` (`id_localizacao`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `equipamentos`
--

LOCK TABLES `equipamentos` WRITE;
/*!40000 ALTER TABLE `equipamentos` DISABLE KEYS */;
INSERT INTO `equipamentos` VALUES (1,'04.002.00','Monitor Multiparamétrico',1,'Philips','IntelliVue MP5','MP5-2022-45873','Philips','2022-04-18',2022,7850.00,'compra','ativo','alta','Monitor utilizado na vigilância contínua de doentes.',1,1,'2026-06-23 15:59:35'),(2,'05.001.00','Ventilador Pulmonar',2,'Dräger','Evita V500','EV500-2021-9934','Dräger','2021-09-07',2021,28500.00,'compra','ativo','suporte_de_vida','Equipamento destinado ao suporte ventilatório invasivo.',1,1,'2026-06-23 15:59:35'),(3,'06.014.00','Bomba De Infusão',3,'B. Braun','Infusomat Space','INF-2020-88321','B. Braun','2020-02-12',2020,2450.00,'compra','em_calibracao','media','Bomba de infusão volumétrica para administração de terapêutica.',5,1,'2026-06-23 15:59:35'),(4,'05.008.00','Desfibrilhador',2,'Zoll','R Series','ZR-2021-7712','Zoll','2021-06-22',2021,16750.00,'compra','em_manutencao','suporte_de_vida','Desfibrilhador utilizado no serviço de urgência.',2,1,'2026-06-23 15:59:35'),(5,'XBBXBJX','Xjauxihnx',5,'Xqx','Xwx','XWXWX','Xxwcxswc','2026-06-17',1981,12.00,'doacao','ativo','media',NULL,4,1,'2026-06-23 18:41:02'),(9,'07.001.00','Eletrocardiógrafo',4,'GE Healthcare','MAC 2000','ECG-2023-001','GE Healthcare','2023-01-12',2023,4200.00,'compra','ativo','alta','Equipamento para realização de ECG de repouso.',2,1,'2026-06-23 18:53:02'),(10,'07.002.00','Ecógrafo Portátil',4,'Mindray','M7 Premium','ECO-2022-014','Mindray','2022-05-20',2022,18500.00,'compra','ativo','alta','Ecógrafo portátil usado em diagnóstico à cabeceira.',4,1,'2026-06-23 18:53:02'),(11,'08.001.00','Autoclave',5,'Matachana','S1000','AUT-2021-088','Matachana','2021-03-15',2021,32000.00,'compra','ativo','media','Equipamento de esterilização para material clínico.',3,1,'2026-06-23 18:53:02'),(12,'06.015.00','Bomba De Seringa',3,'B. Braun','Perfusor Space','PER-2020-112','B. Braun','2020-11-04',2020,2100.00,'compra','ativo','media','Bomba de seringa para administração controlada de fármacos.',1,1,'2026-06-23 18:53:02'),(13,'04.003.00','Oxímetro De Pulso',1,'Nonin','PalmSAT 2500','OXI-2024-017','Nonin','2024-02-10',2024,650.00,'compra','ativo','media','Equipamento portátil para medição de SpO2.',5,1,'2026-06-23 18:53:02'),(14,'05.009.00','Desfibrilhador Externo Automático',2,'Zoll','AED Plus','AED-2022-031','Zoll','2022-08-18',2022,2350.00,'compra','ativo','suporte_de_vida','DEA disponível para resposta rápida em emergência.',2,1,'2026-06-23 18:53:02'),(15,'05.010.00','Ventilador De Transporte',2,'Dräger','Oxylog 3000 Plus','OXY-2021-045','Dräger','2021-12-02',2021,14500.00,'compra','em_manutencao','suporte_de_vida','Ventilador destinado a transporte intra-hospitalar.',1,1,'2026-06-23 18:53:02'),(16,'09.001.00','Centrífuga Laboratorial',5,'Hettich','Rotina 380','CEN-2020-057','Hettich','2020-09-30',2020,3900.00,'compra','ativo','baixa','Centrífuga usada em processamento de amostras laboratoriais.',4,1,'2026-06-23 18:53:02'),(17,'07.003.00','Tensiómetro Digital',1,'Omron','HEM-907','TEN-2023-022','Omron','2023-06-14',2023,480.00,'compra','ativo','baixa','Equipamento para medição automática de pressão arterial.',5,1,'2026-06-23 18:53:02'),(18,'06.016.00','Unidade Eletrocirúrgica',3,'Erbe','VIO 300D','ELE-2021-073','Erbe','2021-04-21',2021,9800.00,'compra','em_calibracao','alta','Unidade de eletrocirurgia utilizada em bloco operatório.',3,1,'2026-06-23 18:53:02'),(19,'04.004.00','Monitor De Sinais Vitais',1,'Philips','SureSigns VS4','MON-2022-066','Philips','2022-10-11',2022,5200.00,'compra','ativo','alta','Monitor de sinais vitais para serviço de medicina interna.',5,1,'2026-06-23 18:53:02'),(20,'07.004.00','Eletroencefalógrafo',4,'Nihon Kohden','EEG-1200','EEG-2020-019','Nihon Kohden','2020-07-08',2020,22500.00,'compra','ativo','alta','Sistema de EEG para exames neurológicos.',4,1,'2026-06-23 18:53:02'),(21,'10.001.00','Cama Hospitalar Elétrica',3,'Linet','Eleganza 2','CAM-2023-091','Linet','2023-03-24',2023,3100.00,'compra','ativo','baixa','Cama elétrica articulada para internamento.',5,1,'2026-06-23 18:53:02'),(22,'05.011.00','Aspirador Cirúrgico',3,'Medela','Basic 30','ASP-2021-028','Medela','2021-01-28',2021,1250.00,'compra','ativo','media','Aspirador cirúrgico usado em procedimentos clínicos.',3,1,'2026-06-23 18:53:02'),(23,'09.002.00','Analisador Bioquímico',5,'Roche','Cobas C111','BIO-2022-103','Roche','2022-09-16',2022,27500.00,'compra','ativo','media','Analisador para testes bioquímicos laboratoriais.',4,1,'2026-06-23 18:53:02'),(39,'09.003.00','Analisador de Imunoquímica',5,'Roche','cobas e 411','E411-2026-8338','Roche Diagnostics','2026-06-10',2025,28500.00,'compra','ativo','alta','Equipamento utilizado em análises de imunoquímica e diagnóstico laboratorial.',3,1,'2026-06-23 23:41:53');
/*!40000 ALTER TABLE `equipamentos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `equipamentos_fornecedores`
--

DROP TABLE IF EXISTS `equipamentos_fornecedores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `equipamentos_fornecedores` (
  `id_equipamento` int unsigned NOT NULL,
  `id_fornecedor` int unsigned NOT NULL,
  `funcao` enum('fabricante','distribuidor','assistencia_tecnica','consumiveis') COLLATE utf8mb4_bin NOT NULL,
  `data_inicio` date DEFAULT NULL,
  `data_fim` date DEFAULT NULL,
  PRIMARY KEY (`id_equipamento`,`id_fornecedor`,`funcao`),
  KEY `fk_equipamento_fornecedor_fornecedor` (`id_fornecedor`),
  CONSTRAINT `fk_equipamento_fornecedor_equipamento` FOREIGN KEY (`id_equipamento`) REFERENCES `equipamentos` (`id_equipamento`),
  CONSTRAINT `fk_equipamento_fornecedor_fornecedor` FOREIGN KEY (`id_fornecedor`) REFERENCES `fornecedores` (`id_fornecedor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `equipamentos_fornecedores`
--

LOCK TABLES `equipamentos_fornecedores` WRITE;
/*!40000 ALTER TABLE `equipamentos_fornecedores` DISABLE KEYS */;
INSERT INTO `equipamentos_fornecedores` VALUES (1,1,'fabricante','2022-04-18',NULL),(2,2,'fabricante','2021-09-07',NULL),(2,2,'assistencia_tecnica','2021-09-07',NULL),(3,3,'distribuidor','2020-02-12',NULL),(23,2,'fabricante','2026-06-10','2029-06-06'),(39,7,'assistencia_tecnica','2026-06-04','2029-06-04');
/*!40000 ALTER TABLE `equipamentos_fornecedores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fornecedores`
--

DROP TABLE IF EXISTS `fornecedores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fornecedores` (
  `id_fornecedor` int unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(150) COLLATE utf8mb4_bin NOT NULL,
  `nif` char(9) COLLATE utf8mb4_bin NOT NULL,
  `telefone` varchar(20) COLLATE utf8mb4_bin DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_bin DEFAULT NULL,
  `morada` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `website` varchar(200) COLLATE utf8mb4_bin DEFAULT NULL,
  `pessoa_contacto` varchar(100) COLLATE utf8mb4_bin DEFAULT NULL,
  `telefone_contacto` varchar(20) COLLATE utf8mb4_bin DEFAULT NULL,
  `tipo` enum('fabricante','distribuidor','assistencia_tecnica','consumiveis') COLLATE utf8mb4_bin NOT NULL,
  `observacoes` text COLLATE utf8mb4_bin,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `criado_em` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_fornecedor`),
  UNIQUE KEY `nif` (`nif`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fornecedores`
--

LOCK TABLES `fornecedores` WRITE;
/*!40000 ALTER TABLE `fornecedores` DISABLE KEYS */;
INSERT INTO `fornecedores` VALUES (1,'Philips Portuguesa, S.A.','500069283','214164200','healthcare.portugal@philips.com','Lagoas Park, Edifício 14, 2740-262 Porto Salvo','https://www.philips.pt/healthcare','Departamento de Suporte Técnico','214164200','fabricante','Fabricante de equipamentos de monitorização e diagnóstico.',1,'2026-06-23 15:59:35'),(2,'Dräger Portugal, Lda.','503208123','214241750','info.portugal@draeger.com','Av. do Forte, 6-6A, 2790-072 Carnaxide','https://www.draeger.com/pt_pt','Assistência Técnica','214241750','assistencia_tecnica','Assistência técnica de ventiladores e equipamentos de suporte de vida.',1,'2026-06-23 15:59:35'),(3,'Fornecedor','501506543','214368288','geral.pt@bbraun.com','Est. Consiglieri Pedroso, 80, 2730-053 Barcarena','https://www.bbraun.pt','Apoio ao Cliente','214368200','distribuidor','Distribuidor de bombas de infusão e consumíveis hospitalares',1,'2026-06-23 15:59:35'),(7,'Siemens Healthcare, Lda.','507925173','214178000','healthcare.pt@siemens-healthineers.com','Rua Irmãos Siemens, 1, 2720-093 Amadora','https://www.siemens-healthineers.com/pt','Departamento De Assistência Técnica','214178000','assistencia_tecnica','Fornecedor de equipamentos de diagnóstico e assistência técnica hospitalar.',1,'2026-06-23 18:58:27'),(8,'Sofiax','268817707','911129297','sofia@gmail.com','Rua do Monte, 31K','https://www.sofiax.pt','Sofia','296381209','assistencia_tecnica','olá',0,'2026-06-23 23:23:56');
/*!40000 ALTER TABLE `fornecedores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `garantias`
--

DROP TABLE IF EXISTS `garantias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `garantias` (
  `id_garantia` int unsigned NOT NULL AUTO_INCREMENT,
  `id_equipamento` int unsigned NOT NULL,
  `data_inicio` date NOT NULL,
  `data_fim` date NOT NULL,
  `entidade_responsavel` varchar(150) COLLATE utf8mb4_bin DEFAULT NULL,
  `observacoes` text COLLATE utf8mb4_bin,
  PRIMARY KEY (`id_garantia`),
  KEY `fk_garantia_equipamento` (`id_equipamento`),
  CONSTRAINT `fk_garantia_equipamento` FOREIGN KEY (`id_equipamento`) REFERENCES `equipamentos` (`id_equipamento`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `garantias`
--

LOCK TABLES `garantias` WRITE;
/*!40000 ALTER TABLE `garantias` DISABLE KEYS */;
INSERT INTO `garantias` VALUES (1,1,'2022-04-18','2025-04-17','Philips Portuguesa, S.A.','Garantia geral de três anos.'),(2,2,'2021-09-07','2024-09-06','Dräger Portugal, Lda.','Garantia do fabricante expirada.'),(3,3,'2020-02-12','2022-02-11','B. Braun Medical, Lda.','Garantia comercial expirada.'),(4,23,'2026-01-01','2028-12-31','Philips Portuguesa, S.A.','Garantia comercial com cobertura de peças e mão de obra.'),(5,39,'2026-06-04','2029-06-07','Phillips Portugal, S.A.','Garantia de três anos para peças e mão de obra');
/*!40000 ALTER TABLE `garantias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `localizacoes`
--

DROP TABLE IF EXISTS `localizacoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `localizacoes` (
  `id_localizacao` int unsigned NOT NULL AUTO_INCREMENT,
  `edificio` varchar(100) COLLATE utf8mb4_bin NOT NULL,
  `piso` varchar(30) COLLATE utf8mb4_bin NOT NULL,
  `servico` varchar(100) COLLATE utf8mb4_bin NOT NULL,
  `sala` varchar(50) COLLATE utf8mb4_bin DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_localizacao`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `localizacoes`
--

LOCK TABLES `localizacoes` WRITE;
/*!40000 ALTER TABLE `localizacoes` DISABLE KEYS */;
INSERT INTO `localizacoes` VALUES (1,'Edifício Principal','Piso 3','Unidade de Cuidados Intensivos','UCI-01',1),(2,'Edifício Sul','Piso 0','Urgência','URG-02',1),(3,'Edifício Principal','Piso 2','Bloco Operatório','BO-03',1),(4,'Edifício Norte','Piso -1','Imagiologia','IMG-09',1),(5,'Edifício Principal','Piso 4','Medicina Interna','MED-10',1),(6,'Edificio Principal','Piso 3','Ortopedia','ORT-01',1),(7,'Edifício Principal','Piso 3','Unidade De Cuidados Intensivos','UCI-01',1),(8,'Edificio Principal','Piso 4','Medicina Interna','MED-11',0);
/*!40000 ALTER TABLE `localizacoes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `logs`
--

DROP TABLE IF EXISTS `logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `logs` (
  `id_log` int unsigned NOT NULL AUTO_INCREMENT,
  `id_utilizador` int unsigned DEFAULT NULL,
  `evento` varchar(100) COLLATE utf8mb4_bin NOT NULL,
  `entidade` varchar(60) COLLATE utf8mb4_bin DEFAULT NULL,
  `id_registo` int unsigned DEFAULT NULL,
  `detalhes` text COLLATE utf8mb4_bin,
  `endereco_ip` varchar(45) COLLATE utf8mb4_bin DEFAULT NULL,
  `criado_em` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_log`),
  KEY `fk_log_utilizador` (`id_utilizador`),
  CONSTRAINT `fk_log_utilizador` FOREIGN KEY (`id_utilizador`) REFERENCES `utilizadores` (`id_utilizador`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logs`
--

LOCK TABLES `logs` WRITE;
/*!40000 ALTER TABLE `logs` DISABLE KEYS */;
INSERT INTO `logs` VALUES (1,1,'sistema_inicializado','sistema',NULL,'Dados iniciais da aplicação BioTrack Solutions carregados.','127.0.0.1','2026-06-23 15:59:38');
/*!40000 ALTER TABLE `logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `utilizadores`
--

DROP TABLE IF EXISTS `utilizadores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `utilizadores` (
  `id_utilizador` int unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8mb4_bin NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_bin NOT NULL,
  `palavra_passe` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  `perfil` enum('administrador','tecnico') COLLATE utf8mb4_bin NOT NULL DEFAULT 'tecnico',
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `ultimo_login` datetime DEFAULT NULL,
  `criado_em` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_utilizador`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `utilizadores`
--

LOCK TABLES `utilizadores` WRITE;
/*!40000 ALTER TABLE `utilizadores` DISABLE KEYS */;
INSERT INTO `utilizadores` VALUES (1,'Maria Loureiro','admin@biotrack.pt','$2y$10$VRc/YlpF6lre3lI6S80DweE6Xv3IsjziCOth1wTs5YGK/S/kIuAzK','administrador',1,'2026-06-24 12:02:57','2026-06-23 15:59:34'),(2,'Técnico BioTrack','tecnico@biotrack.pt','$2y$12$OeaxVSh3Tc9vcPxkultv7.jLOWhasVLZ4GMKPCI2vz9GwRrn21px2','tecnico',1,'2026-06-24 04:07:59','2026-06-24 04:02:48');
/*!40000 ALTER TABLE `utilizadores` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-24 16:13:21
