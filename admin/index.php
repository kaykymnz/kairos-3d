<?php

require __DIR__ . '/../includes/functions.php';
require_admin();

$products = database()->query('SELECT * FROM produtos ORDER BY ativo DESC, id DESC')->fetchAll();
$pageTitle = 'Produtos';
$successMessage = isset($_GET['salvo']) ? 'Produto salvo com sucesso.' : '';
require __DIR__ . '/_header.php';
?>
<div class="admin-toolbar">
  <p>Gerencie produtos ativos, destaques e itens arquivados.</p>
  <a class="button button-primary" href="cadastrar.php">Cadastrar produto</a>
</div>
<?php if ($products === []): ?>
  <section class="admin-panel"><p>Nenhum produto cadastrado.</p></section>
<?php else: ?>
  <div class="admin-product-list">
    <?php foreach ($products as $product): ?>
      <?php $colors = product_colors($product['cores']); ?>
      <article class="admin-product <?= (int) $product['ativo'] === 0 ? 'is-inactive' : '' ?>">
        <img class="admin-product-image" src="../<?= escape_html($product['imagem']) ?>" alt="">
        <div class="admin-product-info">
          <div class="admin-product-heading">
            <h2><?= escape_html($product['nome']) ?></h2>
            <span class="status-badge <?= (int) $product['ativo'] === 1 ? 'status-active' : 'status-inactive' ?>">
              <?= (int) $product['ativo'] === 1 ? 'Ativo' : 'Inativo' ?>
            </span>
            <?php if ((int) $product['destaque'] === 1): ?><span class="status-badge status-featured">Destaque</span><?php endif; ?>
          </div>
          <p><?= escape_html($product['categoria']) ?> · <?= escape_html(format_price($product['valor'])) ?></p>
          <div class="admin-colors" aria-label="Cores disponíveis">
            <?php foreach ($colors as $color): ?><i style="background-color: <?= escape_html($color) ?>" title="<?= escape_html($color) ?>"></i><?php endforeach; ?>
            <span><?= count($colors) ?> cores</span>
          </div>
          <div class="admin-actions">
            <a class="button button-secondary" href="editar.php?id=<?= (int) $product['id'] ?>">Editar</a>
            <form method="post" action="status.php">
              <input type="hidden" name="csrf_token" value="<?= escape_html(csrf_token()) ?>">
              <input type="hidden" name="id" value="<?= (int) $product['id'] ?>">
              <input type="hidden" name="ativo" value="<?= (int) $product['ativo'] === 1 ? '0' : '1' ?>">
              <button class="button button-secondary" type="submit"><?= (int) $product['ativo'] === 1 ? 'Inativar' : 'Reativar' ?></button>
            </form>
            <?php if ((int) $product['ativo'] === 1): ?>
              <form method="post" action="destaque.php">
                <input type="hidden" name="csrf_token" value="<?= escape_html(csrf_token()) ?>">
                <input type="hidden" name="id" value="<?= (int) $product['id'] ?>">
                <input type="hidden" name="destaque" value="<?= (int) $product['destaque'] === 1 ? '0' : '1' ?>">
                <button class="button button-secondary" type="submit"><?= (int) $product['destaque'] === 1 ? 'Remover destaque' : 'Tornar destaque' ?></button>
              </form>
            <?php endif; ?>
            <?php if ((int) $product['ativo'] === 0): ?>
              <form method="post" action="excluir.php" data-confirm="Excluir permanentemente este produto e as imagens que não estiverem em uso?">
                <input type="hidden" name="csrf_token" value="<?= escape_html(csrf_token()) ?>">
                <input type="hidden" name="id" value="<?= (int) $product['id'] ?>">
                <button class="button button-danger" type="submit">Excluir permanentemente</button>
              </form>
            <?php endif; ?>
          </div>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
<?php require __DIR__ . '/_footer.php'; ?>
