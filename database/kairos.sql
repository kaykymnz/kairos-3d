CREATE TABLE IF NOT EXISTS produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    imagem VARCHAR(255) NOT NULL,
    imagem_2 VARCHAR(255) NULL,
    imagem_3 VARCHAR(255) NULL,
    imagem_4 VARCHAR(255) NULL,
    cores TEXT NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    categoria VARCHAR(80) NOT NULL,
    categoria_exibicao VARCHAR(80) NULL,
    descricao TEXT NULL,
    chamada VARCHAR(180) NULL,
    codigo_drop VARCHAR(40) NULL,
    dimensoes VARCHAR(100) NULL,
    selo VARCHAR(80) NULL,
    destaque TINYINT(1) NOT NULL DEFAULT 0,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS usuarios (
    usuario VARCHAR(80) PRIMARY KEY,
    senha VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO produtos
    (nome, imagem, cores, valor, categoria, categoria_exibicao, descricao, chamada, codigo_drop, dimensoes, selo, destaque)
VALUES
    (
        'Dragão articulado',
        'assets/dragao.png',
        '["#c04020","#f07030","#902010"]',
        79.90,
        'Geek',
        'GEEK',
        '20 cm de pura personalidade. Um dragão articulado, impresso em 3D, para trazer um pouco de fantasia ao seu setup ou à sua coleção.',
        '20 cm de pura personalidade.',
        'DROP / 007',
        '20 CM',
        'LOOT DA SEMANA',
        1
    ),
    (
        'Astro planter',
        'assets/astro-planter.png',
        '["#d0c0d0","#a090a0","#706070"]',
        69.90,
        'Decor',
        'DECOR',
        'Um pequeno explorador para dar vida à sua mesa. Consulte as opções de cores e o tamanho do vaso.',
        NULL,
        NULL,
        '12 CM',
        'QUERIDINHO',
        0
    ),
    (
        'Dock de controle',
        'assets/dock-controle.png',
        '["#a0b0a0","#c0c0c0","#606060"]',
        49.90,
        'Setup',
        'SETUP',
        'Deixe seu controle em destaque e mantenha o setup organizado com este suporte impresso em 3D.',
        NULL,
        NULL,
        '15 CM',
        NULL,
        0
    ),
    (
        'Vaso Órbita',
        'assets/vaso-orbita.png',
        '["#b0a090","#d0c0b0","#807050"]',
        89.90,
        'Decor',
        'DECOR',
        'Peça decorativa com linhas espirais e visual orgânico. Uma forma diferente de destacar suas plantas.',
        NULL,
        NULL,
        '18 CM',
        NULL,
        0
    ),
    (
        'Torre do mago',
        'assets/torre-mago.png',
        '["#d0e0f0","#2050a0","#4070c0"]',
        119.90,
        'Geek',
        'RPG',
        'Um cenário de fantasia para sua coleção, mesa de RPG ou setup. Consulte as opções de acabamento.',
        NULL,
        NULL,
        '20 CM',
        'NOVO DROP',
        0
    ),
    (
        'Cable buddy',
        'assets/cable-buddy.png',
        '["#e0c0c0","#b08080","#c0a0a0"]',
        29.90,
        'Setup',
        'SETUP',
        'Trio de personagens para organizar cabos e deixar sua mesa com mais personalidade.',
        NULL,
        NULL,
        'KIT COM 3',
        NULL,
        0
    ),
    (
        'Mini bot',
        'assets/mini-bot.png',
        '["#e0d0b0","#c0b080","#b09040"]',
        39.90,
        'Geek',
        'COLECIONÁVEL',
        'Um pequeno robô colecionável impresso em 3D. Escolha sua cor favorita ao falar com o lab.',
        NULL,
        NULL,
        '9 CM',
        NULL,
        0
    );
