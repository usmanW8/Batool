<?php
require_once '../config/database.php';

header('Content-Type: application/json');

$type = $_GET['type'] ?? '';
$id = intval($_GET['id'] ?? 0);

if ($type === 'category_products') {
    $products = db_fetch_all("SELECT * FROM shop_products WHERE category_id = ?", [$id], 'i');
    echo json_encode($products);
} elseif ($type === 'snugglet_products') {
    $products = db_fetch_all("SELECT * FROM shop_products WHERE snugglet_id = ?", [$id], 'i');
    echo json_encode($products);
} else {
    echo json_encode([]);
}
?>
