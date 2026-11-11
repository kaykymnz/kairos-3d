# Kairos 3D

Catálogo de produtos impressos em 3D, em PHP puro com MySQL. O catálogo mantém o HTML, CSS, JavaScript e os assets existentes; o painel `/admin` gerencia os produtos.

## Rodar localmente com WampServer

1. Coloque a pasta do projeto dentro de `C:\wamp64\www\kairos-3d`.
2. Inicie Apache e MySQL pelo WampServer.
3. Crie o banco `kairos_3d` no phpMyAdmin e importe `database/kairos.sql` **uma única vez**. O arquivo cria as tabelas e insere os sete produtos existentes.
4. Copie `config/local.example.php` para `config/local.php` e configure host, banco, usuário e senha. O arquivo local é ignorado pelo Git.
5. Acesse `http://localhost/kairos-3d/` e, antes de publicar o site, crie a conta pelo endereço `http://localhost/kairos-3d/admin/setup.php`.

O cadastro inicial só fica disponível quando a tabela `usuarios` está vazia; depois, o painel exige login. Use uma senha forte.

## Publicar no InfinityFree

1. Crie um banco MySQL no painel InfinityFree e importe `database/kairos.sql` pelo phpMyAdmin da hospedagem.
2. Crie `config/local.php` no servidor com o host, nome do banco, usuário e senha fornecidos pela hospedagem (parta de `config/local.example.php`; o arquivo local é ignorado pelo Git).
3. Envie o projeto para a pasta pública do site e confirme que `uploads/produtos/` permite escrita pelo PHP.
4. Antes de disponibilizar o site ao público, acesse `/admin/setup.php` e crie a conta. Esse endereço redireciona para o login assim que existir um usuário.

O site público precisa de hospedagem que execute PHP e ofereça MySQL; GitHub Pages não executa PHP.

## Produtos e imagens

- Os produtos iniciais são inseridos a partir do catálogo existente; as repetições antigas dos cards não viram registros duplicados.
- As imagens iniciais continuam em `assets/`. Novas imagens são salvas em `uploads/produtos/` com nomes baseados em `time()` e bytes aleatórios para evitar colisões.
- Cada produto tem uma imagem principal e até três imagens adicionais. As adicionais podem ser armazenadas e substituídas no painel; a exibição pública atual usa a imagem principal.
- A exclusão definitiva só fica disponível depois de inativar o produto. Imagens compartilhadas não são removidas.
- As sugestões das três cores predominantes são calculadas no navegador; o administrador pode alterar, adicionar ou remover as cores antes de salvar.

## Observações

- Os dados de conexão são configurados localmente e não devem conter credenciais de produção no repositório.
- `categoria_exibicao` conserva os rótulos existentes “RPG” e “COLECIONÁVEL” sem alterar a categoria usada pelos filtros.
- `chamada` e `codigo_drop` preservam os textos da área de destaque/detalhes e podem ser ajustados no formulário de produto.
- Não reimporte `database/kairos.sql` sobre um banco já populado, pois o bloco de carga inicial adicionará novamente os sete produtos.
- A sacola permanece no navegador e encaminha os itens ao atendimento por WhatsApp; não há vendas, pagamentos ou pedidos no sistema.
