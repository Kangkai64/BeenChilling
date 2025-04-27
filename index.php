<?php
require '_base.php';

if ($_user && $_user->role == 'Admin') {
    redirect('/page/admin/product_list.php');
}

try {
// Fetch sales data
$stmt = $_db->query("
        SELECT p.product_id, p.product_name, p.price, p.product_image, 
               SUM(oi.quantity) AS total_sold
        FROM order_item oi
        JOIN product p ON oi.product_id = p.product_id
        GROUP BY p.product_id, p.product_name, p.price, p.product_image
");
$sales_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch top 5 best-selling products
$stmt = $_db->query("
        SELECT p.product_id, p.product_name, p.price, p.product_image, 
               SUM(oi.quantity) AS total_sold
        FROM order_item oi
        JOIN product p ON oi.product_id = p.product_id
        GROUP BY p.product_id, p.product_name, p.price, p.product_image
    ORDER BY total_sold DESC
    LIMIT 5
");
$top_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $_err['db'] = 'Database error: ' . $e->getMessage();
    $sales_data = [];
    $top_products = [];
}

$_title = 'BeenChilling';
include '_head.php';
?>

<h1 class="horizontal bestSeller">
    <span>B</span><span>e</span><span>s</span><span>t</span>
    <span>s</span><span>e</span><span>l</span><span>l</span><span>e</span><span>r</span>
</h1>

<div class="bestSeller">
    <?php if (!empty($top_products)): ?>
        <?php foreach (array_slice($top_products, 0, 2) as $product): ?>
    <div class="product-container">
                <img src="/images/product/<?= htmlspecialchars($product['product_image']) ?>" 
                     alt="<?= htmlspecialchars($product['product_name']) ?>">
                <button class="cta" data-get="/page/member/product_details.php?id=<?= $product['product_id'] ?>">
                    Buy Now
                </button>
    </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div><br>

<?php if (!empty($sales_data)): ?>
<h2 class="page-nav">Product Sales Distribution</h2>
    <div class="sales-chart-wrapper">
    <canvas id="salesChart"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
        const productNames = <?= json_encode(array_column($sales_data, 'product_name')) ?>;
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
                    responsive: true,
                    maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                                font: {
                                    family: 'Beon, Times New Roman, serif',
                                    size: 14
                                }
                        }
                    }
                }
            }
        });
    });
</script>
<?php endif; ?>

<?php if (!empty($top_products)): ?>
<h2 class="page-nav">🏆 Top 5 Best-Selling Products</h2>
    <table class="top-products-container">
        <thead>
            <tr>
                <th>Rank</th>
                <th>Product Name</th>
                <th>Price</th>
                <th>Sales</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
    <?php
        $medals = ['🥇', '🥈', '🥉', '🏅', '🎖️'];
    ?>
    <?php foreach ($top_products as $i => $product): ?>
                <tr class="top-product-item">
                    <td class="medal"><?= $medals[$i] ?></td>
                    <td class="product-name">
                        <?= htmlspecialchars($product['product_name']) ?>
                    </td>
                    <td class="price">
                        RM <?= number_format($product['price'], 2) ?>
                    </td>
                    <td class="sales-count">
                        <?= $product['total_sold'] ?> sold
                    </td>
                    <td class="top-product-buttons">
                        <a href="/page/member/product_details.php?id=<?= $product['product_id'] ?>" class="button">View</a>
                        <button class="add-to-cart" data-id="<?= $product['product_id'] ?>" data-action="cart" data-image="/images/product/<?= $product['product_image'] ?>">Add Cart</button>
                        <?php if($_user): ?>
                            <button class="add-to-cart" data-id="<?= $product['product_id'] ?>" data-action="wishlist" data-image="/images/product/<?= $product['product_image'] ?>">Wishlist</button>
                        <?php endif; ?>
                    </td>
                </tr>
    <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<iframe style="width: 700px; height: 400px; margin: 0 auto;" title="vimeo-player" src="https://player.vimeo.com/video/890988764?h=05bb284c71" allowfullscreen></iframe>

<div class="working">
    <iframe src="https://www.google.com/maps/d/u/0/embed?mid=1ekYt6jaQQaAzk3YPIMx9DHfYYcNgzls&ehbc=2E312F" width="100%" height="100%"></iframe>
</div>

<?php include '_foot.php'; ?>
