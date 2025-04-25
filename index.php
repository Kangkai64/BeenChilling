<?php
require '_base.php';

$_title = 'BeenChilling';

// Fetch sales data
$stmt = $_db->query("
    SELECT product.ProductName, SUM(order_item.quantity) AS total_sold
    FROM order_item
    JOIN product ON order_item.product_id = product.ProductID
    GROUP BY order_item.product_id
");
$sales_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Fetch top 5 best-selling products
$stmt = $_db->query("
    SELECT product.ProductName, SUM(order_item.quantity) AS total_sold
    FROM order_item
    JOIN product ON order_item.product_id = product.ProductID
    GROUP BY order_item.product_id
    ORDER BY total_sold DESC
    LIMIT 5
");
$top_products = $stmt->fetchAll(PDO::FETCH_ASSOC);


include '_head.php';
?>

<h1 class="horizontal bestSeller">
    <span>B</span><span>e</span><span>s</span><span>t</span>
    <span>s</span><span>e</span><span>l</span><span>l</span><span>e</span><span>r</span>
</h1>

<div class="bestSeller">
    <div class="product-container">
        <img src="/images/product/bestSeller_mixedSundae.png" alt="Mixed Sundae">
        <button class="cta">Buy Now</button>
    </div>
    <div class="product-container">
        <img src="/images/product/bestSeller_bananaSplit.png" alt="Banana Split">
        <button class="cta">Buy Now</button>
    </div>
</div><br>

<h2 class="page-nav">Product Sales Distribution</h2>
<div style="max-width: 400px; margin: 0 auto;">
    <canvas id="salesChart"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const productNames = <?= json_encode(array_column($sales_data, 'ProductName')) ?>;
    const productSales = <?= json_encode(array_column($sales_data, 'total_sold')) ?>;

    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: productNames,
                datasets: [{
                    label: 'Product Sales',
                    data: productSales,
                    backgroundColor: [
                        '#FF6384', '#36A2EB', '#FFCE56', '#66BB6A', '#BA68C8',
                        '#FFA726', '#8D6E63', '#26C6DA', '#D4E157', '#7E57C2'
                    ]
                }]
            },
            options: {
                plugins: {
                    legend: {
                        labels: {
                            color: '#ff0' // ← change this to any color you want
                        }
                    }
                }
            }
        });
    });
</script>

<h2 class="page-nav">🏆 Top 5 Best-Selling Products</h2>

<div style="
    max-width: 400px;
    margin: 20px auto;
    border: 2px solid #ccc;
    border-radius: 12px;
    padding: 20px;
    background: #f9f9f9;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
">
    <?php
        $medals = ['🥇', '🥈', '🥉', '🏅', '🎖️'];
        $top_products = array_slice($sales_data, 0, 5);
    ?>
    <?php foreach ($top_products as $i => $product): ?>
        <div style="
            display: flex;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #e0e0e0;
        ">
            <span style="font-size: 1.5em; margin-right: 10px;"><?= $medals[$i] ?></span>
            <span style="flex-grow: 1; font-weight: bold;">
                <?= htmlspecialchars($product['ProductName']) ?>
            </span>
            <span style="color: gray; font-size: 0.9em;">
                (<?= $product['total_sold'] ?> sold)
            </span>
        </div>
    <?php endforeach; ?>
</div>



<iframe id="video" title="vimeo-player" src="https://player.vimeo.com/video/890988764?h=05bb284c71" allowfullscreen></iframe>

<div class="working">
    <iframe src="https://www.google.com/maps/d/u/0/embed?mid=1ekYt6jaQQaAzk3YPIMx9DHfYYcNgzls&ehbc=2E312F" width="100%" height="100%"></iframe>
</div>



<?php include '_foot.php'; ?>
