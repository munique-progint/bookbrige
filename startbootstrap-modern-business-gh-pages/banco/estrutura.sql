
USE bookbridge;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    telefone VARCHAR(20) NOT NULL,
    senha VARCHAR(255) NOT NULL,
    tipo_usuario VARCHAR(30) NOT NULL,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE materiais (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    nome_material VARCHAR(150) NOT NULL,
    categoria VARCHAR(50) NOT NULL,
    area VARCHAR(100) NULL,
    nivel VARCHAR(50) NOT NULL,
    descricao TEXT NOT NULL,
    imagem VARCHAR(255),
    status VARCHAR(30) NOT NULL DEFAULT 'Disponível',
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
);
