<?php
require '../_base.php';

$_title = 'BeenChilling';
include '../_head.php';

$name = req('name');
$typeid = req('TypeID');

$fields = [
    'id'    => 'ProductID',
    'name'  => 'ProductName',
    'action' => 'Action',
];

$sort = req('sort');
key_exists($sort, $fields) || $sort = 'id';

$dir = req('dir');
in_array($dir, ['asc','desc']) || $dir = 'asc';


$stm = $_db->prepare('SELECT * FROM product 
                        WHERE ProductName LIKE ?
                        AND (TypeID = ? OR ?)');
$stm->execute(["%$name%", $typeid, $typeid == null]);
$arr =$stm->fetchAll();

?>
    
   <?php topics_text("Get a BeenChilling like John Cena."); ?>

    <!-- <p class = product-list-category>
        <a href="?TypeID=1">Sundae</a>
        <a href="?TypeID=2">Dessert</a>
        <a href="?TypeID=3">Ice-cream</a>
    </p> -->
    

    <form >
        <div class = search-div>
            <?= html_search('name') ?> 
            <?= html_select('TypeID', $_producttype, 'All') ?>
            <button>Search</button>
        </div>
    </form>

    <table class = "product-list-table">
        <tr>
            <?= table_headers($fields,$sort,$dir) ?>
        </tr>

        <?php foreach ($arr as $s): ?>
        <tr>
            <td><?=$s->ProductID ?></td>
            <td><?=$s->ProductName ?></td>
            <td>
                <button  data-get="detail.php?id=<?= $s->ProductID ?>">Detail</button>
                <button  data-get="update.php?id=<?= $s->ProductID ?>">Update</button>
                <button data-post="delete.php?id=<?= $s->ProductID ?>" data-confirm>Delete</button>
            </td>
        </tr>
        <?php endforeach ?>
    </table>

    


<?php
include '../_foot.php';