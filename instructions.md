# KAIROS 3D — Especificação de Migração para PHP e MySQL

## 1. Contexto do projeto

O Kairos 3D é um site de catálogo de produtos impressos em 3D.

Atualmente, o projeto foi desenvolvido utilizando exclusivamente:
- HTML
- SCSS/CSS
- JavaScript
- Assets (imagens, ícones e demais recursos).

O projeto está publicado no GitHub Pages:

https://kaykymnz.github.io/kairos-3d/

Atualmente, os produtos estão cadastrados diretamente no código do site.

O objetivo é migrar o projeto para PHP e MySQL, permitindo o gerenciamento dinâmico dos produtos por meio de um painel administrativo.

O projeto continuará sendo exclusivamente um catálogo. Não haverá vendas, checkout, pagamentos ou gerenciamento de pedidos.

## 2. Objetivo principal

Transformar o catálogo estático em um sistema dinâmico, utilizando PHP puro e MySQL, mantendo integralmente a identidade visual e a experiência de navegação atuais.

**Regra fundamental: NÃO RECONSTRUIR O FRONT-END.**

O site atual já possui um design aprovado. A implementação deverá aproveitar os arquivos, componentes, estilos, scripts e assets existentes.

A mudança deve se concentrar na implementação do PHP, banco de dados e painel administrativo.

## 3. Tecnologias e restrições

- PHP puro, sem frameworks.
- MySQL como banco de dados.
- HTML existente.
- SCSS/CSS existente.
- JavaScript existente.
- PDO para conexão com o banco.
- Sem Laravel.
- Sem API REST separada.
- Sem Composer, salvo necessidade técnica devidamente justificada.
- Sem autenticação ou login.
- Sem sistema de vendas.

Não implementar funcionalidades ou dependências que não sejam necessárias ao escopo.

## 4. Requisitos funcionais

### RF01 — Catálogo público

O catálogo deverá carregar os produtos diretamente do banco de dados.

Manter todas as funcionalidades e características visuais existentes, incluindo:

- Listagem de produtos.
- Pesquisa por nome.
- Filtros por categoria.
- Exibição de imagens.
- Exibição de preços.
- Exibição das cores disponíveis.
- Página de detalhes dos produtos.
- Sacola, caso existente no projeto.
- Direcionamento para atendimento pelo WhatsApp.
- Seções institucionais e de apresentação da marca.

Nenhum produto deverá depender de informações fixas cadastradas diretamente no HTML ou JavaScript.

### RF02 — Produtos em destaque

Atualmente, o catálogo possui uma área de destaque ocupada pelo Dragão Articulado.

Essa área deverá passar a utilizar os produtos marcados como destaque no painel administrativo.

Regras:

- Permitir que vários produtos sejam marcados como destaque simultaneamente.
- Permitir remover o destaque de um produto individualmente.
- Os produtos destacados deverão aparecer na área de destaque do catálogo.
- Carregar dinamicamente imagem, nome, preço e demais informações utilizadas pela apresentação atual.
- Não deixar nenhum produto fixo diretamente no código.

O Codex deverá analisar como a seção de destaque funciona atualmente e adaptar sua apresentação para suportar múltiplos produtos, preservando ao máximo o layout e os estilos existentes.

Se necessário, utilizar a estrutura de navegação ou carrossel já existente no projeto, sem criar uma reformulação visual.

### RF03 — Cadastro de produtos

Criar uma tela administrativa para cadastrar novos produtos.

Campos:

| Campo | Obrigatório |
|---|---|
| Nome | Sim |
| Imagem | Sim |
| Cores disponíveis | Sim |
| Valor | Sim |
| Categoria | Sim |
| Descrição | Não |
| Dimensões | Não |

**Cadastro de cores:**

As cores deverão ser adicionadas individualmente por meio de uma interface dinâmica.

Exemplo:

- Campo para informar uma cor.
- Botão para adicionar.
- Exibição das cores já adicionadas.
- Possibilidade de remover uma cor antes de salvar.

Não utilizar um único campo de texto com cores separadas por vírgulas.

**Categorias fixas:**

As categorias deverão ser definidas previamente no sistema, utilizando as categorias já existentes no catálogo atual.

Não criar gerenciamento de categorias no painel administrativo.

O formulário deverá apresentar as categorias disponíveis por meio de um campo de seleção.

**Imagens:**

- Permitir selecionar uma imagem do computador.
- Armazenar os arquivos em `uploads/produtos/`.
- Salvar no banco apenas o caminho da imagem.
- Exibir a imagem selecionada durante o cadastro, se possível.

### RF04 — Edição de produtos

Permitir editar qualquer produto cadastrado.

- Carregar os dados atuais.
- Alterar todos os campos disponíveis.
- Adicionar e remover cores individualmente.
- Permitir substituir a imagem.
- Manter a imagem atual quando nenhuma nova imagem for selecionada.
- Salvar as alterações no MySQL.

### RF05 — Exclusão de produtos

Permitir excluir produtos cadastrados.

- Solicitar confirmação antes da exclusão.
- Remover o registro do banco.
- Remover a imagem associada, quando não estiver sendo utilizada por outro produto.
- Se o produto estiver marcado como destaque, remover essa condição automaticamente.

### RF06 — Dashboard administrativo

Criar um painel administrativo acessível pelo endereço `/admin`.

O dashboard deverá apresentar todos os produtos cadastrados.

Informações da listagem:

- Imagem do produto.
- Nome.
- Categoria.
- Valor.
- Cores disponíveis.
- Indicador de destaque.

Ações disponíveis para cada produto:

- Editar.
- Excluir.
- Tornar destaque.
- Remover destaque.

Também deverá existir um botão para cadastrar novo produto.

A interface administrativa poderá ser simples e funcional, mas deverá ter identidade visual coerente com o projeto.

Não é necessário reproduzir integralmente o layout do catálogo no painel administrativo.

## 5. Banco de dados

Criar um banco de dados simples, com apenas uma tabela principal: `produtos`.

Estrutura sugerida:

| Coluna | Tipo |
|---|---|
| id | INT AUTO_INCREMENT PRIMARY KEY |
| nome | VARCHAR(150) |
| imagem | VARCHAR(255) |
| cores | TEXT |
| valor | DECIMAL(10,2) |
| categoria | VARCHAR(80) |
| descricao | TEXT NULL |
| dimensoes | VARCHAR(100) NULL |
| destaque | TINYINT(1) DEFAULT 0 |
| criado_em | TIMESTAMP DEFAULT CURRENT_TIMESTAMP |

**Armazenamento das cores:**

Salvar as cores individualmente em formato JSON dentro da coluna `cores`.

Exemplo:

`["Preto", "Branco", "Vermelho"]`

A interface deverá permitir adicionar e remover cores individualmente, independentemente de como são armazenadas no banco.

Não criar tabelas adicionais para cores, categorias, usuários ou pedidos.

**Produtos em destaque:**

A coluna `destaque` deverá aceitar múltiplos produtos com valor `1`.

Não implementar restrição que permita apenas um produto destacado.

## 6. Migração dos produtos existentes

Todos os produtos que já existem no catálogo deverão ser preservados durante a migração.

Requisitos:

- Identificar todos os produtos existentes.
- Reaproveitar suas imagens e assets.
- Migrar nomes, preços, categorias, cores e descrições existentes.
- Preservar os dados reais, sem inventar ou substituir informações.
- Marcar inicialmente como destaque o Dragão Articulado, mantendo a apresentação atual.

A migração deverá ser feita por meio de um arquivo SQL de inserção inicial ou outro mecanismo simples e reproduzível.

## 7. Estrutura de diretórios sugerida

```text
kairos-3d/
│
├── index.php
├── produto.php
│
├── admin/
│   ├── index.php
│   ├── cadastrar.php
│   ├── editar.php
│   ├── excluir.php
│   └── destaque.php
│
├── config/
│   └── database.php
│
├── includes/
│   ├── header.php
│   └── footer.php
│
├── assets/
│   ├── css/
│   ├── js/
│   ├── images/
│   └── ...
│
├── uploads/
│   └── produtos/
│
└── database/
    └── kairos.sql
```

Essa estrutura é uma sugestão. O Codex deverá analisar a estrutura real do repositório antes de implementar e poderá adaptá-la quando necessário.

Evitar criar arquivos e pastas desnecessários.

## 8. Requisitos não funcionais

- Manter o site responsivo.
- Preservar o comportamento atual em dispositivos móveis e desktop.
- Preservar animações, transições e interações existentes.
- Utilizar PDO para conexão com MySQL.
- Utilizar consultas preparadas, sem necessidade de implementar uma camada complexa de segurança.
- Manter o código simples e compreensível.
- Não introduzir bibliotecas ou frameworks sem necessidade.
- Não alterar a identidade visual.
- Utilizar a logo já existente no projeto como favicon do navegador.

**Observação sobre acesso administrativo:**

Não haverá autenticação. O painel `/admin` será acessível sem login, conforme solicitado. Qualquer pessoa que conhecer o endereço poderá realizar operações administrativas.

## 9. Fora do escopo

Não desenvolver:

- Login ou autenticação.
- Cadastro de usuários.
- Controle de permissões.
- Sistema de vendas.
- Checkout.
- Integração com pagamentos.
- Controle de estoque.
- Gerenciamento de pedidos.
- Gerenciamento de categorias.
- API REST independente.
- Reformulação visual do catálogo.

## 10. Critérios de aceitação

- [ ] Projeto executando em ambiente PHP + MySQL.
- [ ] Produtos carregados dinamicamente do banco.
- [ ] Todos os produtos atuais preservados.
- [ ] Pesquisa funcionando.
- [ ] Filtros funcionando.
- [ ] Detalhes dos produtos funcionando.
- [ ] Sacola e WhatsApp funcionando como atualmente.
- [ ] Cadastro de produtos funcionando.
- [ ] Cadastro individual de cores funcionando.
- [ ] Categorias fixas funcionando.
- [ ] Upload de imagens funcionando.
- [ ] Edição de produtos funcionando.
- [ ] Exclusão de produtos funcionando.
- [ ] Múltiplos produtos em destaque funcionando.
- [ ] Dashboard administrativo funcionando.
- [ ] Favicon utilizando a logo existente.
- [ ] Layout desktop preservado.
- [ ] Layout mobile preservado.

## 11. Orientações de implementação para o Codex

Antes de realizar qualquer alteração:

1. Analise todo o repositório e identifique a estrutura atual.
2. Entenda como os produtos são cadastrados e apresentados.
3. Identifique como funcionam a pesquisa, os filtros, os detalhes e a área de destaque.
4. Identifique os assets correspondentes a cada produto.
5. Identifique os arquivos responsáveis pelo favicon.

Após essa análise:

1. Apresente brevemente a estrutura atual e o plano de migração.
2. Implemente a conexão com MySQL.
3. Implemente a estrutura do banco e a migração dos produtos.
4. Adapte o catálogo para consumir os dados do banco.
5. Implemente o painel administrativo.
6. Teste todas as funcionalidades.
7. Corrija eventuais problemas de layout ou comportamento.

**REGRA MAIS IMPORTANTE:**

Não substituir o projeto atual por uma nova implementação.

Rodar comandos no CMD apenas se for EXTREMAMENTE NECESSÁRIO, qualquer ação que possa ser feita diretamente em arquivos ou com ferramentas, deverão ser feitas dessa mandeira.

Trabalhar sobre os arquivos existentes, reaproveitando o máximo possível do código original.

O resultado deve ser visualmente equivalente ao site atual, com a diferença de que os produtos agora serão gerenciados dinamicamente pelo PHP e MySQL.