<?php

require_once __DIR__ . '/../includes/functions.php';

function save_product_from_form(array $input, array $files, ?array $product = null): int
{
    $name = trim((string) ($input['nome'] ?? ''));
    $category = (string) ($input['categoria'] ?? '');
    $categoryLabel = trim((string) ($input['categoria_exibicao'] ?? ''));
    $price = $input['valor'] ?? '';
    $badge = (string) ($input['selo'] ?? '');
    $colors = json_decode((string) ($input['cores_json'] ?? ''), true);

    if ($name === '' || mb_strlen($name) > 150) {
        throw new InvalidArgumentException('Informe um nome com até 150 caracteres.');
    }
    if (!in_array($category, PRODUCT_CATEGORIES, true)) {
        throw new InvalidArgumentException('Selecione uma categoria válida.');
    }
    if ($categoryLabel !== '' && mb_strlen($categoryLabel) > 80) {
        throw new InvalidArgumentException('O rótulo de categoria deve ter até 80 caracteres.');
    }
    $callout = trim((string) ($input['chamada'] ?? ''));
    if (mb_strlen($callout) > 180) {
        throw new InvalidArgumentException('A chamada curta deve ter até 180 caracteres.');
    }
    $dropCode = trim((string) ($input['codigo_drop'] ?? ''));
    if (mb_strlen($dropCode) > 40) {
        throw new InvalidArgumentException('O código do drop deve ter até 40 caracteres.');
    }
    if (!is_numeric($price) || (float) $price < 0 || (float) $price > 99999999.99) {
        throw new InvalidArgumentException('Informe um valor válido.');
    }
    if ($badge !== '' && !in_array($badge, PRODUCT_BADGES, true)) {
        throw new InvalidArgumentException('Selecione um selo válido.');
    }
    if (!is_array($colors)) {
        throw new InvalidArgumentException('Informe as cores disponíveis.');
    }

    $colors = array_values(array_unique(array_filter(
        $colors,
        static fn ($color): bool => is_string($color) && preg_match('/^#[0-9a-fA-F]{6}$/', $color)
    )));
    if ($colors === []) {
        throw new InvalidArgumentException('Informe ao menos uma cor em formato hexadecimal.');
    }

    $images = [];
    $newImages = [];
    $oldImages = [];
    try {
        foreach (PRODUCT_IMAGE_COLUMNS as $column) {
            $oldPath = $product[$column] ?? null;
            $uploadedPath = uploaded_product_image($files[$column] ?? ['error' => UPLOAD_ERR_NO_FILE]);

            if ($uploadedPath !== null) {
                $images[$column] = $uploadedPath;
                $newImages[] = $uploadedPath;
                if ($oldPath) {
                    $oldImages[] = $oldPath;
                }
            } elseif ($column !== 'imagem' && !empty($input['remover_' . $column])) {
                $images[$column] = null;
                if ($oldPath) {
                    $oldImages[] = $oldPath;
                }
            } else {
                $images[$column] = $oldPath;
            }
        }

        if (!$images['imagem']) {
            throw new InvalidArgumentException('Selecione a imagem principal do produto.');
        }
    } catch (Throwable $error) {
        foreach ($newImages as $path) {
            $absolutePath = dirname(__DIR__) . DIRECTORY_SEPARATOR
                . str_replace('/', DIRECTORY_SEPARATOR, $path);
            if (is_file($absolutePath)) {
                unlink($absolutePath);
            }
        }
        throw $error;
    }

    $values = [
        'nome' => $name,
        'imagem' => $images['imagem'],
        'imagem_2' => $images['imagem_2'],
        'imagem_3' => $images['imagem_3'],
        'imagem_4' => $images['imagem_4'],
        'cores' => json_encode($colors, JSON_THROW_ON_ERROR),
        'valor' => number_format((float) $price, 2, '.', ''),
        'categoria' => $category,
        'categoria_exibicao' => $categoryLabel !== '' ? $categoryLabel : null,
        'descricao' => trim((string) ($input['descricao'] ?? '')) ?: null,
        'chamada' => $callout !== '' ? $callout : null,
        'codigo_drop' => $dropCode !== '' ? $dropCode : null,
        'dimensoes' => trim((string) ($input['dimensoes'] ?? '')) ?: null,
        'selo' => $badge !== '' ? $badge : null,
    ];

    try {
        if ($product === null) {
            $columns = array_keys($values);
            $sql = 'INSERT INTO produtos (' . implode(', ', $columns) . ') VALUES ('
                . implode(', ', array_fill(0, count($columns), '?')) . ')';
            $statement = database()->prepare($sql);
            $statement->execute(array_values($values));
            $productId = (int) database()->lastInsertId();
        } else {
            $assignments = array_map(
                static fn (string $column): string => "$column = ?",
                array_keys($values)
            );
            $statement = database()->prepare(
                'UPDATE produtos SET ' . implode(', ', $assignments) . ' WHERE id = ?'
            );
            $statement->execute([...array_values($values), $product['id']]);
            $productId = (int) $product['id'];
        }
    } catch (Throwable $error) {
        foreach ($newImages as $path) {
            $absolutePath = dirname(__DIR__) . DIRECTORY_SEPARATOR
                . str_replace('/', DIRECTORY_SEPARATOR, $path);
            if (is_file($absolutePath)) {
                unlink($absolutePath);
            }
        }
        throw $error;
    }

    foreach (array_unique($oldImages) as $path) {
        remove_product_image_if_unused($path);
    }

    return $productId;
}
