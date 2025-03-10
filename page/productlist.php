<?php
require '../_base.php';

$_title = 'BeenChilling';
include '../_head.php';

$TypeID = isset($_GET['TypeID']) ? $_GET['TypeID'] : 1;

$stm = $_db->prepare('SELECT * FROM product WHERE TypeID = ?');
$stm->execute([$TypeID]);
$arr =$stm->fetchAll(PDO::FETCH_OBJ);

?>
    
   <?php topics_text("Get a BeenChilling like John Cena."); ?>

    <p class = product-list-category>
        <a href="?TypeID=1">Sundae</a>
        <a href="?TypeID=2">Dessert</a>
        <a href="?TypeID=3">Ice-cream</a>
    </p>
    <table class = "product-list-table">
        <tr>
            <th>Product ID</th>
            <th>Product Name</th>
            <th>Price</th>
            <th>Description</th>

        </tr>

        <?php foreach ($arr as $s): ?>
        <tr>
            <td><?=$s->ProductID ?></td>
            <td><?=$s->ProductName ?></td>
            <td><?=$s->Price ?></td>
            <td><?=$s->Description ?></td>
        </tr>
        <?php endforeach ?>
    </table>

    


<?php
include '../_foot.php';