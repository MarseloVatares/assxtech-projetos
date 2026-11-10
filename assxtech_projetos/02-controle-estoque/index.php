<?php
require __DIR__ . '/config.php';
$pdo = db();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    if ($action === 'save') {
        $id = (int)($_POST['id'] ?? 0);
        $name = trim((string)($_POST['name'] ?? ''));
        $sku = trim((string)($_POST['sku'] ?? ''));
        $category = trim((string)($_POST['category'] ?? ''));
        $quantity = filter_var($_POST['quantity'] ?? null, FILTER_VALIDATE_INT);
        $price = filter_var($_POST['price'] ?? null, FILTER_VALIDATE_FLOAT);
        if ($name === '' || $quantity === false || $quantity < 0 || $price === false || $price < 0) {
            flash('Preencha o nome, a quantidade e o preço corretamente.', 'error');
        } else {
            if ($id > 0) {
                $stmt = $pdo->prepare('UPDATE products SET name=?, sku=?, category=?, quantity=?, price=? WHERE id=?');
                $stmt->execute([$name, $sku ?: null, $category ?: null, $quantity, $price, $id]);
                flash('Produto atualizado com sucesso.');
            } else {
                $stmt = $pdo->prepare('INSERT INTO products (name, sku, category, quantity, price) VALUES (?,?,?,?,?)');
                $stmt->execute([$name, $sku ?: null, $category ?: null, $quantity, $price]);
                flash('Produto cadastrado com sucesso.');
            }
        }
        redirect();
    }
    if ($action === 'delete') {
        $stmt = $pdo->prepare('DELETE FROM products WHERE id=?');
        $stmt->execute([(int)($_POST['id'] ?? 0)]);
        flash('Produto excluído.');
        redirect();
    }
}
$edit = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id=?');
    $stmt->execute([(int)$_GET['edit']]);
    $edit = $stmt->fetch() ?: null;
}
$q = trim((string)($_GET['q'] ?? ''));
if ($q !== '') {
    $stmt = $pdo->prepare('SELECT * FROM products WHERE name LIKE ? OR sku LIKE ? OR category LIKE ? ORDER BY name');
    $like = "%$q%"; $stmt->execute([$like,$like,$like]); $products = $stmt->fetchAll();
} else {
    $products = $pdo->query('SELECT * FROM products ORDER BY created_at DESC, id DESC')->fetchAll();
}
$summary = $pdo->query('SELECT COUNT(*) total, COALESCE(SUM(quantity),0) units, COALESCE(SUM(quantity*price),0) value, SUM(CASE WHEN quantity<=5 THEN 1 ELSE 0 END) low FROM products')->fetch();
$flash = get_flash();
?>
<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Estoque | Conecta Gestão</title><link rel="stylesheet" href="app.css"></head><body><div class="shell">
<header class="top"><div><div class="brand">conecta<span>gestão</span></div><div class="sub">Painel de controle de estoque</div></div><a href="../README.md">Manual do projeto ↗</a></header>
<section class="hero"><div><span class="eyebrow">Visão geral</span><h1>Controle de estoque</h1><p>Acompanhe seus produtos, quantidades e valores em um só lugar.</p></div><a class="btn light" href="#cadastro">＋ Novo produto</a></section>
<?php if ($flash): ?><div class="flash <?=e($flash['type'])?>"><?=e($flash['message'])?></div><?php endif; ?>
<section class="stats"><div class="stat"><label>Produtos cadastrados</label><strong><?=number_format((int)$summary['total'],0,',','.')?></strong><small>Itens diferentes</small></div><div class="stat"><label>Unidades em estoque</label><strong><?=number_format((int)$summary['units'],0,',','.')?></strong><small>Soma das quantidades</small></div><div class="stat"><label>Valor estimado</label><strong>R$ <?=number_format((float)$summary['value'],2,',','.')?></strong><small><?=(int)$summary['low']?> produto(s) com estoque baixo</small></div></section>
<section class="panel" id="cadastro"><div class="panel-head"><h2><?=$edit?'Editar produto':'Cadastrar produto'?></h2><?php if($edit):?><a class="btn light small" href="index.php">Cancelar edição</a><?php endif;?></div>
<form method="post"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?=e($edit['id']??0)?>"><div class="form-grid">
<div class="field"><label>Nome do produto *</label><input name="name" required maxlength="140" value="<?=e($edit['name']??'')?>"></div><div class="field"><label>Código / SKU</label><input name="sku" maxlength="50" value="<?=e($edit['sku']??'')?>"></div><div class="field"><label>Categoria</label><input name="category" maxlength="80" value="<?=e($edit['category']??'')?>"></div><div class="field"><label>Quantidade *</label><input type="number" min="0" name="quantity" required value="<?=e($edit['quantity']??0)?>"></div><div class="field"><label>Preço unitário (R$) *</label><input type="number" min="0" step="0.01" name="price" required value="<?=e($edit['price']??'0.00')?>"></div></div><div class="actions" style="margin-top:15px"><button class="btn" type="submit"><?=$edit?'Salvar alterações':'Cadastrar produto'?></button></div></form></section>
<section class="panel"><div class="panel-head"><h2>Produtos cadastrados</h2><form method="get"><input class="search" name="q" placeholder="Buscar nome, código ou categoria..." value="<?=e($q)?>"></form></div><div class="table-wrap"><table><thead><tr><th>PRODUTO</th><th>CÓDIGO</th><th>CATEGORIA</th><th>QUANTIDADE</th><th>PREÇO</th><th>VALOR TOTAL</th><th>AÇÕES</th></tr></thead><tbody>
<?php if(!$products):?><tr><td colspan="7" class="empty">Nenhum produto encontrado.</td></tr><?php endif;?>
<?php foreach($products as $p):?><tr><td><strong><?=e($p['name'])?></strong></td><td><?=e($p['sku']?:'—')?></td><td><?=e($p['category']?:'—')?></td><td><span class="pill <?=$p['quantity']<=5?'low':''?>"><?=e($p['quantity'])?> un.</span></td><td>R$ <?=number_format((float)$p['price'],2,',','.')?></td><td>R$ <?=number_format((int)$p['quantity']*(float)$p['price'],2,',','.')?></td><td><div class="row-actions"><a class="btn light small" href="?edit=<?=(int)$p['id']?>">Editar</a><form method="post" onsubmit="return confirm('Excluir este produto?')"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=(int)$p['id']?>"><button class="btn danger small">Excluir</button></form></div></td></tr><?php endforeach;?>
</tbody></table></div></section><div class="footer">Conecta Gestão · Sistema demonstrativo para avaliação técnica</div></div></body></html>