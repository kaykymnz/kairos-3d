<?php

require __DIR__ . '/includes/functions.php';

$products = database()->query('SELECT * FROM produtos WHERE ativo = 1 ORDER BY id')->fetchAll();
$featuredProducts = array_values(array_filter(
    $products,
    static fn (array $product): bool => (int) $product['destaque'] === 1
));
$clientProducts = [];

foreach ($products as $product) {
    $clientProducts[(string) $product['id']] = [
        'id' => (string) $product['id'],
        'name' => $product['nome'],
        'price' => (float) $product['valor'],
        'category' => strtoupper($product['categoria_exibicao'] ?: $product['categoria']),
        'filterCategory' => $product['categoria'],
        'description' => $product['descricao'] ?? '',
        'dimensions' => $product['dimensoes'] ?? '',
        'badge' => $product['selo'] ?? '',
        'dropCode' => $product['codigo_drop'] ?: 'LAYER / LAB',
        'image' => $product['imagem'],
        'alt' => $product['nome'],
    ];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="theme-color" content="#fff5ee">
  <meta name="description" content="Kairos 3D — peças geek, design e objetos impressos em 3D, camada por camada.">
  <title>Kairos 3D — Impressão 3D</title>
  <link rel="icon" type="image/png" href="assets/logo-primaria.png">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=DM+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header class="site-header">
    <a class="brand" href="#" aria-label="Kairos 3D, página inicial">
      <img class="brand-logo" src="assets/logo-primaria.png" alt="Kairos 3D">
    </a>
    <nav class="desktop-nav" aria-label="Navegação do site">
      <a href="#catalogo">Catálogo</a>
      <a href="#personalizados">Personalizados</a>
      <a href="#sobre">Sobre a Kairos 3D</a>
    </nav>
    <nav class="header-actions" aria-label="Navegação principal">
      <button class="icon-button cart-button" id="cartButton" aria-label="Ver carrinho">
        <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="3.5" width="16" height="17" rx="3"/><path d="M8 8V7a4 4 0 0 1 8 0v1"/></svg>
        <span id="cartCount">0</span>
      </button>
      <button class="icon-button menu-button" id="menuButton" aria-label="Abrir menu"><span></span><span></span><span></span></button>
    </nav>
  </header>

  <main id="catalogPage">
    <section class="intro page-gutter">
      <p class="eyebrow"><i></i> PEQUENAS CAMADAS. GRANDES IDEIAS.</p>
      <h1>Seu universo,<br>fora da tela.</h1>
      <p class="intro-copy">Peças geek, design e um pouco de obsessão.<br>Tudo impresso em 3D, camada por camada.</p>
    </section>

    <section class="catalog page-gutter" id="catalogo">
      <div class="section-heading"><h2>Peças em destaque</h2></div>
      <label class="search-box">
        <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 5 5"/></svg>
        <input id="searchInput" type="search" placeholder="O que falta no seu universo?" autocomplete="off">
      </label>
      <div class="filters" role="group" aria-label="Filtrar produtos">
        <button class="filter active" data-filter="Tudo">Tudo</button>
        <button class="filter" data-filter="Geek">Geek</button>
        <button class="filter" data-filter="Decor">Decor</button>
        <button class="filter" data-filter="Setup">Setup</button>
      </div>

      <?php if ($featuredProducts !== []): ?>
        <div class="featured-track" id="featuredTrack" aria-label="Produtos em destaque">
          <?php foreach ($featuredProducts as $product): ?>
            <article class="featured" data-featured-id="<?= escape_html($product['id']) ?>">
              <div class="featured-info">
                <h3><?= escape_html($product['nome']) ?></h3>
                <p><?= escape_html($product['chamada'] ?: ($product['dimensoes'] ?: ($product['categoria_exibicao'] ?: $product['categoria']))) ?></p>
                <strong><?= escape_html(format_price($product['valor'])) ?></strong>
              </div>
              <img src="<?= escape_html($product['imagem']) ?>" alt="<?= escape_html($product['nome']) ?>">
              <button class="add-button featured-add" data-add="<?= escape_html($product['id']) ?>" aria-label="Adicionar <?= escape_html($product['nome']) ?> à sacola">+</button>
              <a class="featured-link" href="#produto-<?= escape_html($product['id']) ?>" aria-label="Ver detalhes de <?= escape_html($product['nome']) ?>"></a>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <div class="product-toolbar">
        <span id="pieceCount">Explore todas as peças</span>
      </div>
      <div class="product-grid" id="productGrid">
        <?php foreach ($products as $product): ?>
          <?php $colors = product_colors($product['cores']); ?>
          <article class="product-card"
                   data-id="<?= escape_html($product['id']) ?>"
                   data-name="<?= escape_html($product['nome']) ?>"
                   data-category="<?= escape_html($product['categoria']) ?>"
                   data-price="<?= escape_html($product['valor']) ?>">
            <a class="product-image" href="#produto-<?= escape_html($product['id']) ?>">
              <img src="<?= escape_html($product['imagem']) ?>" alt="<?= escape_html($product['nome']) ?>">
              <?php if (!empty($product['selo'])): ?>
                <span class="tag image-tag"><?= escape_html($product['selo']) ?></span>
              <?php endif; ?>
            </a>
            <div class="product-info">
              <span class="product-meta"><?= escape_html(strtoupper($product['categoria_exibicao'] ?: $product['categoria'])) ?><?= $product['dimensoes'] ? ' · ' . escape_html($product['dimensoes']) : '' ?></span>
              <h3><?= escape_html($product['nome']) ?></h3>
              <div class="swatches">
                <?php foreach ($colors as $color): ?>
                  <i style="background-color: <?= escape_html($color) ?>"></i>
                <?php endforeach; ?>
                <small><?= count($colors) ?> cores</small>
              </div>
              <div class="price-row">
                <strong><?= escape_html(format_price($product['valor'])) ?></strong>
                <button class="add-button" data-add="<?= escape_html($product['id']) ?>" aria-label="Adicionar <?= escape_html($product['nome']) ?> à sacola">+</button>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
      <p class="empty-state" id="emptyState" hidden>Nenhuma peça encontrada. Tente outra busca.</p>
      <p class="all-seen"><span>✓</span> VOCÊ EXPLOROU TODO O DROP.</p>
    </section>

    <section class="custom-cta page-gutter" id="personalizados">
      <div class="cta-panel"><p class="mono">SIDE QUEST / PERSONALIZADOS</p><h2>Sua ideia merece<br>uma nova dimensão.</h2><p>Outra cor, outro tamanho ou algo só seu?<br>A gente cria junto.</p><a class="cta-button" href="https://wa.me/5511959990386" target="_blank" rel="noopener"> <span>♡</span> Bora conversar</a></div>
    </section>
    <section class="shipping page-gutter" id="sobre"><span class="shipping-icon">◇</span><div><strong>Feito pra você. Sem estoque infinito.</strong><p>Produção em 3–5 dias úteis. Frete calculado na sacola. Enviamos para todo o Brasil.</p></div></section>
  </main>

  <main id="productPage" hidden>
    <div class="product-page-top page-gutter"><a href="#" class="back-link">← &nbsp;Voltar ao catálogo</a><span class="mono">CATÁLOGO / PEÇA</span></div>
    <section class="detail page-gutter">
      <div class="detail-image"><span class="tag"></span><span class="mono detail-drop">LAYER / LAB</span><img id="detailImage" src="assets/dragao.png" alt="Dragão articulado"></div>
      <div class="detail-copy"><span class="product-meta"></span><h1 id="detailTitle"></h1><p id="detailDescription"></p></div>
      <div class="detail-price"><div><span class="product-meta">VALOR DA PEÇA</span><strong id="detailPrice"></strong></div><div class="production"><span class="product-meta">PRODUÇÃO</span><strong>3–5 dias úteis</strong></div></div>
      <div class="detail-actions"><p>Curtiu esse loot? Fale com a equipe da Kairos 3D para combinar os detalhes da sua peça.</p><a class="primary-action" id="whatsappLink" href="https://wa.me/5511959990386" target="_blank" rel="noopener"><span>◯</span> Tenho interesse · WhatsApp</a><a class="secondary-action" href="https://instagram.com/" target="_blank" rel="noopener"><span>◎</span> Conversar no Instagram</a></div>
      <p class="detail-signoff mono">KAIROS 3D / CAMADA POR CAMADA.</p>
    </section>
  </main>

  <footer class="site-footer page-gutter"><div class="footer-brand"><a class="brand" href="#" aria-label="Kairos 3D, página inicial"><img class="brand-logo" src="assets/logo-primaria.png" alt="Kairos 3D"></a><a class="instagram" href="https://instagram.com/" aria-label="Instagram">◎</a></div><div class="footer-bottom"><span>© 2026 KAIROS 3D</span><a href="#personalizados">Dúvidas e trocas</a></div></footer>

  <aside class="cart-drawer" id="cartDrawer" aria-label="Sacola" aria-hidden="true">
    <div class="drawer-head"><h2>Sua sacola</h2><button id="closeCart" aria-label="Fechar sacola">×</button></div>
    <div id="cartItems" class="cart-items"><p class="cart-empty">Sua sacola ainda está vazia.</p></div>
    <div class="cart-total"><span>Total</span><strong id="cartTotal">R$ 0,00</strong></div>
    <a id="checkoutLink" class="primary-action checkout" href="https://wa.me/5511959990386" target="_blank" rel="noopener">Finalizar pelo WhatsApp</a>
    <p class="cart-note">Frete calculado no atendimento.</p>
  </aside>
  <div class="drawer-backdrop" id="drawerBackdrop"></div>
  <div class="toast" id="toast" role="status" aria-live="polite"></div>
  <script>window.KAIROS_PRODUCTS = <?= json_encode($clientProducts, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) ?>;</script>
  <script src="script.js"></script>
</body>
</html>
