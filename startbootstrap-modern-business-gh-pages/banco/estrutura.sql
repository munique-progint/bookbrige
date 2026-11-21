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
    estado CHAR(2) NULL,
    nivel VARCHAR(50) NOT NULL,
    descricao TEXT NOT NULL,
    imagem VARCHAR(255),
    status VARCHAR(30) NOT NULL DEFAULT 'Disponível',
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
);

CREATE TABLE pastas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    nome_pasta VARCHAR(100) NOT NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
        ON DELETE CASCADE,

    UNIQUE (id_usuario, nome_pasta)
);

CREATE TABLE materiais_salvos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_material INT NOT NULL,
    nome_pasta VARCHAR(100) NOT NULL,
    data_adicao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
        ON DELETE CASCADE,

    FOREIGN KEY (id_material) REFERENCES materiais(id)
        ON DELETE CASCADE,

    UNIQUE KEY unico_material_pasta (
        id_usuario,
        id_material,
        nome_pasta
    )
);
CREATE TABLE conteudos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    nome_conteudo VARCHAR(150) NOT NULL,
   categoria VARCHAR(50) NOT NULL,
area VARCHAR(100) DEFAULT NULL,
nivel VARCHAR(50) NOT NULL,
descricao TEXT NOT NULL,
imagem VARCHAR(255) NOT NULL,
arquivo_pdf VARCHAR(255) NOT NULL,
data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
        ON DELETE CASCADE
);
CREATE TABLE favoritos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_conteudo INT DEFAULT NULL,
    id_material INT DEFAULT NULL,
    data_favorito TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (id_usuario) REFERENCES usuarios(id),
    FOREIGN KEY (id_conteudo) REFERENCES conteudos(id),
    FOREIGN KEY (id_material) REFERENCES materiais(id),

    UNIQUE KEY favorito_conteudo (id_usuario, id_conteudo),
    UNIQUE KEY favorito_material (id_usuario, id_material)
);