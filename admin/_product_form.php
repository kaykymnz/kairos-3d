<?php

$formColors = isset($_POST['cores_json'])
    ? product_colors((string) $_POST['cores_json'])
    : product_colors($product['cores'] ?? []);
$imageLabels = [
    'imagem' => 'Imagem principal (obrigatória)',
    'imagem_2' => 'Imagem adicional 1 (opcional)',
    'imagem_3' => 'Imagem adicional 2 (opcional)',
    'imagem_4' => 'Imagem adicional 3 (opcional)',
];
?>
<form method="post" enctype="multipart/form-data" class="admin-panel admin-form product-form">
  <input type="hidden" name="csrf_token" value="<?= escape_html(csrf_token()) ?>">
  <label>Nome
    <input name="nome" maxlength="150" required value="<?= escape_html($_POST['nome'] ?? $product['nome'] ?? '') ?>">
  </label>
  <label>Categoria
    <select name="categoria" required>
      <option value="">Selecione</option>
      <?php foreach (PRODUCT_CATEGORIES as $category): ?>
        <option value="<?= escape_html($category) ?>" <?= ($_POST['categoria'] ?? $product['categoria'] ?? '') === $category ? 'selected' : '' ?>><?= escape_html($category) ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label>Rótulo da categoria no catálogo (opcional)
    <input name="categoria_exibicao" maxlength="80" value="<?= escape_html($_POST['categoria_exibicao'] ?? $product['categoria_exibicao'] ?? '') ?>">
  </label>
  <label>Valor (R$)
    <input type="number" name="valor" min="0" max="99999999.99" step="0.01" required value="<?= escape_html($_POST['valor'] ?? $product['valor'] ?? '') ?>">
  </label>
  <label>Chamada curta (opcional)
    <input name="chamada" maxlength="180" value="<?= escape_html($_POST['chamada'] ?? $product['chamada'] ?? '') ?>">
  </label>
  <label>Código do drop (opcional)
    <input name="codigo_drop" maxlength="40" value="<?= escape_html($_POST['codigo_drop'] ?? $product['codigo_drop'] ?? '') ?>">
  </label>
  <label>Dimensões (opcional)
    <input name="dimensoes" maxlength="100" value="<?= escape_html($_POST['dimensoes'] ?? $product['dimensoes'] ?? '') ?>">
  </label>
  <label>Selo do produto
    <select name="selo">
      <option value="">Sem selo</option>
      <?php foreach (PRODUCT_BADGES as $badge): ?>
        <option value="<?= escape_html($badge) ?>" <?= ($_POST['selo'] ?? $product['selo'] ?? '') === $badge ? 'selected' : '' ?>><?= escape_html($badge) ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label class="form-wide">Descrição (opcional)
    <textarea name="descricao" rows="4"><?= escape_html($_POST['descricao'] ?? $product['descricao'] ?? '') ?></textarea>
  </label>

  <fieldset class="color-fieldset form-wide">
    <legend>Cores disponíveis</legend>
    <input type="hidden" id="colorsJson" name="cores_json" value="<?= escape_html(json_encode($formColors, JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_QUOT)) ?>">
    <div id="colorList" class="color-list" data-initial-colors="<?= escape_html(json_encode($formColors, JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_QUOT)) ?>"></div>
    <div class="color-add">
      <input id="newColor" type="color" value="#b8a1d7" aria-label="Escolher cor">
      <button id="addColor" class="button button-secondary" type="button">Adicionar cor</button>
      <button id="suggestColors" class="button button-secondary" type="button">Sugerir cores da imagem principal</button>
    </div>
    <p class="form-help">As sugestões são calculadas no navegador a partir da imagem e podem ser alteradas.</p>
    <p id="colorSuggestionMessage" class="form-help" aria-live="polite"></p>
  </fieldset>

  <fieldset class="image-fieldset form-wide">
    <legend>Imagens</legend>
    <?php foreach ($imageLabels as $column => $label): ?>
      <label class="image-input">
        <?= escape_html($label) ?>
        <?php if (!empty($product[$column])): ?>
          <span class="current-image"><img src="../<?= escape_html($product[$column]) ?>" alt=""><span><?= escape_html(basename($product[$column])) ?></span></span>
        <?php endif; ?>
        <input type="file" name="<?= escape_html($column) ?>" accept="image/jpeg,image/png,image/webp" <?= $column === 'imagem' && empty($product['imagem']) ? 'required' : '' ?>>
        <img class="image-preview" alt="Prévia da imagem selecionada" hidden>
        <?php if ($column !== 'imagem' && !empty($product[$column])): ?>
          <span class="checkbox-label"><input type="checkbox" name="remover_<?= escape_html($column) ?>" value="1"> Remover esta imagem</span>
        <?php endif; ?>
      </label>
    <?php endforeach; ?>
    <p class="form-help">JPG, PNG ou WEBP; até 5 MB por imagem. A imagem principal é exibida no catálogo.</p>
  </fieldset>

  <div class="form-actions form-wide">
    <a class="button button-secondary" href="index.php">Cancelar</a>
    <button class="button button-primary" type="submit">Salvar produto</button>
  </div>
</form>
