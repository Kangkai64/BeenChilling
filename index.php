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

include '_head.php';
?>

<h1 class="horizontal bestSeller">
    <span>B</span><span>e</span><span>s</span><span>t</span>
    <span>s</span><span>e</span><span>l</span><span>l</span>
    <span>e</span><span>r</span>
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

<iframe id="video" title="vimeo-player" src="https://player.vimeo.com/video/890988764?h=05bb284c71" allowfullscreen></iframe>

<div class="working">
    <iframe src="https://www.google.com/maps/d/u/0/embed?mid=1ekYt6jaQQaAzk3YPIMx9DHfYYcNgzls&ehbc=2E312F" width="100%" height="100%"></iframe>
</div>

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

<?php include '_foot.php'; ?>
