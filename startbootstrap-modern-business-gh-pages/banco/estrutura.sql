-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Tempo de geração: 21/11/2026 às 01:14
-- Versão do servidor: 8.3.0
-- Versão do PHP: 8.3.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `bookbridge`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `conteudos`
--

CREATE TABLE `conteudos` (
  `id` int NOT NULL,
  `id_usuario` int NOT NULL,
  `nome_conteudo` varchar(150) NOT NULL,
  `categoria` varchar(50) NOT NULL,
  `area` varchar(100) DEFAULT NULL,
  `nivel` varchar(50) NOT NULL,
  `descricao` text NOT NULL,
  `imagem` varchar(255) NOT NULL,
  `arquivo_pdf` varchar(255) NOT NULL,
  `data_cadastro` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `favoritos`
--

CREATE TABLE `favoritos` (
  `id` int NOT NULL,
  `id_usuario` int NOT NULL,
  `id_conteudo` int DEFAULT NULL,
  `id_material` int DEFAULT NULL,
  `data_favorito` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `favoritos`
--

INSERT INTO `favoritos` (`id`, `id_usuario`, `id_conteudo`, `id_material`, `data_favorito`) VALUES
(3, 2, NULL, 3, '2026-11-20 22:50:29');

-- --------------------------------------------------------

--
-- Estrutura para tabela `materiais`
--

CREATE TABLE `materiais` (
  `id` int NOT NULL,
  `id_usuario` int NOT NULL,
  `nome_material` varchar(150) NOT NULL,
  `categoria` varchar(50) NOT NULL,
  `area` varchar(100) DEFAULT NULL,
  `estado` char(2) DEFAULT NULL,
  `nivel` varchar(50) NOT NULL,
  `descricao` text NOT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'Disponível',
  `data_cadastro` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `materiais`
--

INSERT INTO `materiais` (`id`, `id_usuario`, `nome_material`, `categoria`, `area`, `estado`, `nivel`, `descricao`, `imagem`, `status`, `data_cadastro`) VALUES
(3, 2, 'teste 1', 'Livro', 'Física', 'PB', 'Ensino Médio', 'teste 1', 'uploads/materiais/6b00cdf876954.jpeg', 'Disponível', '2026-11-20 22:47:20'),
(5, 2, 'teste 2 editado', 'Livro', 'Inglês', 'PB', 'Ensino Superior', 'teste 222', 'uploads/materiais/6b00ce27b16b7.jpeg', 'Disponível', '2026-11-20 22:48:07'),
(6, 2, 'teste 3', 'Materiais escolares', 'Todas as matérias', 'RS', 'Ensino Médio', 'teste 3', 'uploads/materiais/6b00cea2d6a09.jpeg', 'Disponível', '2026-11-20 22:50:10');

-- --------------------------------------------------------

--
-- Estrutura para tabela `materiais_salvos`
--

CREATE TABLE `materiais_salvos` (
  `id` int NOT NULL,
  `id_usuario` int NOT NULL,
  `id_material` int NOT NULL,
  `nome_pasta` varchar(100) NOT NULL,
  `data_adicao` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pastas`
--

CREATE TABLE `pastas` (
  `id` int NOT NULL,
  `id_usuario` int NOT NULL,
  `nome_pasta` varchar(100) NOT NULL,
  `data_criacao` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `pastas`
--

INSERT INTO `pastas` (`id`, `id_usuario`, `nome_pasta`, `data_criacao`) VALUES
(3, 2, 'teste 1', '2026-11-20 22:55:20'),
(4, 2, 'teste 2', '2026-11-20 22:56:24');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int NOT NULL,
  `nome` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `tipo_usuario` varchar(30) NOT NULL,
  `data_cadastro` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `telefone`, `senha`, `tipo_usuario`, `data_cadastro`) VALUES
(2, 'Munique Prado', 'munique1@gmail.com', '51997198785', '$2y$10$.sg214l.PD94mDCORTJHC.n5aUAXlBmfXhRPrWUVIWoNSAYoGjoeq', 'estudante', '2026-11-20 22:44:29');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `conteudos`
--
ALTER TABLE `conteudos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Índices de tabela `favoritos`
--
ALTER TABLE `favoritos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `favorito_conteudo` (`id_usuario`,`id_conteudo`),
  ADD UNIQUE KEY `favorito_material` (`id_usuario`,`id_material`),
  ADD KEY `id_conteudo` (`id_conteudo`),
  ADD KEY `id_material` (`id_material`);

--
-- Índices de tabela `materiais`
--
ALTER TABLE `materiais`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Índices de tabela `materiais_salvos`
--
ALTER TABLE `materiais_salvos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unico_material_pasta` (`id_usuario`,`id_material`,`nome_pasta`),
  ADD KEY `id_material` (`id_material`);

--
-- Índices de tabela `pastas`
--
ALTER TABLE `pastas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_usuario` (`id_usuario`,`nome_pasta`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `conteudos`
--
ALTER TABLE `conteudos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `favoritos`
--
ALTER TABLE `favoritos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `materiais`
--
ALTER TABLE `materiais`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `materiais_salvos`
--
ALTER TABLE `materiais_salvos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `pastas`
--
ALTER TABLE `pastas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `conteudos`
--
ALTER TABLE `conteudos`
  ADD CONSTRAINT `conteudos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `favoritos`
--
ALTER TABLE `favoritos`
  ADD CONSTRAINT `favoritos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `favoritos_ibfk_2` FOREIGN KEY (`id_conteudo`) REFERENCES `conteudos` (`id`),
  ADD CONSTRAINT `favoritos_ibfk_3` FOREIGN KEY (`id_material`) REFERENCES `materiais` (`id`);

--
-- Restrições para tabelas `materiais`
--
ALTER TABLE `materiais`
  ADD CONSTRAINT `materiais_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`);

--
-- Restrições para tabelas `materiais_salvos`
--
ALTER TABLE `materiais_salvos`
  ADD CONSTRAINT `materiais_salvos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `materiais_salvos_ibfk_2` FOREIGN KEY (`id_material`) REFERENCES `materiais` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `pastas`
--
ALTER TABLE `pastas`
  ADD CONSTRAINT `pastas_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
