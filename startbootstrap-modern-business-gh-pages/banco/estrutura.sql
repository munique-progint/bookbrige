SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET NAMES utf8mb4;

CREATE TABLE `conteudos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `nome_conteudo` varchar(150) NOT NULL,
  `categoria` varchar(50) NOT NULL,
  `area` varchar(100) DEFAULT NULL,
  `nivel` varchar(50) NOT NULL,
  `descricao` text NOT NULL,
  `imagem` varchar(255) NOT NULL,
  `arquivo_pdf` varchar(255) NOT NULL,
  `data_cadastro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `conteudos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `favoritos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `id_conteudo` int DEFAULT NULL,
  `id_material` int DEFAULT NULL,
  `data_favorito` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `favorito_conteudo` (`id_usuario`,`id_conteudo`),
  UNIQUE KEY `favorito_material` (`id_usuario`,`id_material`),
  KEY `id_conteudo` (`id_conteudo`),
  KEY `id_material` (`id_material`),
  CONSTRAINT `favoritos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `favoritos_ibfk_2` FOREIGN KEY (`id_conteudo`) REFERENCES `conteudos` (`id`),
  CONSTRAINT `favoritos_ibfk_3` FOREIGN KEY (`id_material`) REFERENCES `materiais` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `materiais` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `nome_material` varchar(150) NOT NULL,
  `categoria` varchar(50) NOT NULL,
  `area` varchar(100) DEFAULT NULL,
  `estado` char(2) DEFAULT NULL,
  `nivel` varchar(50) NOT NULL,
  `descricao` text NOT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'Disponível',
  `data_cadastro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `materiais_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `materiais_salvos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `id_material` int NOT NULL,
  `nome_pasta` varchar(100) NOT NULL,
  `data_adicao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unico_material_pasta` (`id_usuario`,`id_material`,`nome_pasta`),
  KEY `id_material` (`id_material`),
  CONSTRAINT `materiais_salvos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `materiais_salvos_ibfk_2` FOREIGN KEY (`id_material`) REFERENCES `materiais` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `pastas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `nome_pasta` varchar(100) NOT NULL,
  `data_criacao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_usuario` (`id_usuario`,`nome_pasta`),
  CONSTRAINT `pastas_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `tipo_usuario` varchar(30) NOT NULL,
  `data_cadastro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `conteudos_salvos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `id_conteudo` int NOT NULL,
  `nome_pasta` varchar(100) NOT NULL,
  `data_adicao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unico_conteudo_pasta` (`id_usuario`,`id_conteudo`,`nome_pasta`),
  KEY `id_conteudo` (`id_conteudo`),
  CONSTRAINT `conteudos_salvos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `conteudos_salvos_ibfk_2` FOREIGN KEY (`id_conteudo`) REFERENCES `conteudos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `favoritos` (`id`, `id_usuario`, `id_conteudo`, `id_material`, `data_favorito`) VALUES
(3, 2, NULL, 3, '2026-11-20 22:50:29');

INSERT INTO `materiais` (`id`, `id_usuario`, `nome_material`, `categoria`, `area`, `estado`, `nivel`, `descricao`, `imagem`, `status`, `data_cadastro`) VALUES
(3, 2, 'teste 1', 'Livro', 'Física', 'PB', 'Ensino Médio', 'teste 1', 'uploads/materiais/6b00cdf876954.jpeg', 'Disponível', '2026-11-20 22:47:20'),
(5, 2, 'teste 2 editado', 'Livro', 'Inglês', 'PB', 'Ensino Superior', 'teste 222', 'uploads/materiais/6b00ce27b16b7.jpeg', 'Disponível', '2026-11-20 22:48:07'),
(6, 2, 'teste 3', 'Materiais escolares', 'Todas as matérias', 'RS', 'Ensino Médio', 'teste 3', 'uploads/materiais/6b00cea2d6a09.jpeg', 'Disponível', '2026-11-20 22:50:10');

INSERT INTO `pastas` (`id`, `id_usuario`, `nome_pasta`, `data_criacao`) VALUES
(3, 2, 'teste 1', '2026-11-20 22:55:20'),
(4, 2, 'teste 2', '2026-11-20 22:56:24');

INSERT INTO `usuarios` (`id`, `nome`, `email`, `telefone`, `senha`, `tipo_usuario`, `data_cadastro`) VALUES
(2, 'Munique Prado', 'munique1@gmail.com', '51997198785', '$2y$10$.sg214l.PD94mDCORTJHC.n5aUAXlBmfXhRPrWUVIWoNSAYoGjoeq', 'estudante', '2026-11-20 22:44:29');

COMMIT;
