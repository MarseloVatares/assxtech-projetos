<?php
require __DIR__ . '/config.php';
$pdo = db();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    if ($action === 'save') {
        $description = trim((string)($_POST['description'] ?? ''));
        $type = (string)($_POST['type'] ?? '');
        $category = trim((string)($_POST['category'] ?? ''));
        $amount = filter_var($_POST['amount'] ?? null, FILTER_VALIDATE_FLOAT);
        $date = (string)($_POST['movement_date'] ?? '');
        $validDate = DateTime::createFromFormat('Y-m-d', $date);
        if ($description === '' || !in_array($type,['income','expense'],true) || $amount === false || $amount <= 0 || !$validDate || $validDate->format('Y-m-d') !== $date) {
            flash('Confira a descrição, o tipo, o valor e a data.', 'error');
        } else {
            $stmt = $pdo->prepare('INSERT INTO transactions (description, type, category, amount, movement_date) VALUES (?,?,?,?,?)');
            $stmt->execute([$description,$type,$category ?: null,$amount,$date]);
            flash('Movimentação registrada com sucesso.');
        }
        redirect();
    }
    if ($action === 'delete') {
        $stmt = $pdo->prepare('DELETE FROM transactions WHERE id=?');
        $stmt->execute([(int)($_POST['id'] ?? 0)]);
        flash('Movimentação excluída.');
        redirect();
    }
}
$month = (string)($_GET['month'] ?? date('Y-m'));
if (!preg_match('/^\d{4}-\d{2}$/',$month)) $month = date('Y-m');
$start = $month . '-01';
$end = date('Y-m-t', strtotime($start));
$summaryStmt = $pdo->prepare("SELECT COALESCE(SUM(CASE WHEN type='income' THEN amount ELSE 0 END),0) income, COALESCE(SUM(CASE WHEN type='expense' THEN amount ELSE 0 END),0) expense FROM transactions WHERE movement_date BETWEEN ? AND ?");
$summaryStmt->execute([$start,$end]); $summary=$summaryStmt->fetch();
$stmt=$pdo->prepare('SELECT * FROM transactions WHERE movement_date BETWEEN ? AND ? ORDER BY movement_date DESC,id DESC');
$stmt->execute([$start,$end]); $items=$stmt->fetchAll();
$balance=(float)$summary['income']-(float)$summary['expense'];
$flash=get_flash();
?>
<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Financeiro | Conecta Gestão</title><link rel="stylesheet" href="app.css"></head><body><div class="shell">
<header class="top"><div><div class="brand">conecta<span>gestão</span></div><div class="sub">Painel de gestão financeira</div></div><a href="../README.md">Manual do projeto ↗</a></header>
<section class="hero"><div><span class="eyebrow">Visão geral</span><h1>Gestão financeira</h1><p>Organize receitas e despesas e acompanhe o resultado do período.</p></div><a class="btn light" href="#nova">＋ Nova movimentação</a></section>
<?php if($flash):?><div class="flash <?=e($flash['type'])?>"><?=e($flash['message'])?></div><?php endif;?>
<section class="stats"><div class="stat"><label>Receitas do mês</label><strong>R$ <?=number_format((float)$summary['income'],2,',','.')?></strong><small>Entradas registradas</small></div><div class="stat"><label>Despesas do mês</label><strong>R$ <?=number_format((float)$summary['expense'],2,',','.')?></strong><small>Saídas registradas</small></div><div class="stat"><label>Resultado do mês</label><strong style="color:<?=$balance>=0?'#29936b':'#c44c57'?>">R$ <?=number_format($balance,2,',','.')?></strong><small>Receitas menos despesas</small></div></section>
<section class="panel"><div class="panel-head"><h2>Filtrar período</h2></div><form method="get" class="actions"><input class="search" type="month" name="month" value="<?=e($month)?>"><button class="btn">Aplicar filtro</button></form></section>
<section class="panel" id="nova"><div class="panel-head"><h2>Registrar movimentação</h2></div><form method="post"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="save"><div class="form-grid">
<div class="field"><label>Descrição *</label><input name="description" required maxlength="160" placeholder="Ex.: Venda de produto"></div><div class="field"><label>Tipo *</label><select name="type" required><option value="income">Receita</option><option value="expense">Despesa</option></select></div><div class="field"><label>Categoria</label><input name="category" maxlength="80" placeholder="Ex.: Vendas, aluguel"></div><div class="field"><label>Valor (R$) *</label><input name="amount" type="number" min="0.01" step="0.01" required></div><div class="field"><label>Data *</label><input name="movement_date" type="date" required value="<?=date('Y-m-d')?>"></div></div><div class="actions" style="margin-top:15px"><button class="btn">Salvar movimentação</button></div></form></section>
<section class="panel"><div class="panel-head"><h2>Histórico de movimentações</h2><span class="muted" style="font-size:11px"><?=count($items)?> registro(s)</span></div><div class="table-wrap"><table><thead><tr><th>DATA</th><th>DESCRIÇÃO</th><th>CATEGORIA</th><th>TIPO</th><th>VALOR</th><th>AÇÃO</th></tr></thead><tbody>
<?php if(!$items):?><tr><td colspan="6" class="empty">Nenhuma movimentação neste período.</td></tr><?php endif;?>
<?php foreach($items as $item):?><tr><td><?=date('d/m/Y',strtotime($item['movement_date']))?></td><td><strong><?=e($item['description'])?></strong></td><td><?=e($item['category']?:'—')?></td><td><span class="pill <?=$item['type']?>"><?=$item['type']==='income'?'Receita':'Despesa'?></span></td><td style="font-weight:800;color:<?=$item['type']==='income'?'#29936b':'#c44c57'?>"><?=$item['type']==='income'?'+':'−'?> R$ <?=number_format((float)$item['amount'],2,',','.')?></td><td><form method="post" onsubmit="return confirm('Excluir esta movimentação?')"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=(int)$item['id']?>"><button class="btn danger small">Excluir</button></form></td></tr><?php endforeach;?>
</tbody></table></div></section><div class="footer">Conecta Gestão · Sistema demonstrativo para avaliação técnica</div></div></body></html>