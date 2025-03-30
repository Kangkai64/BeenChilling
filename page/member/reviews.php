<?php
require '../../_base.php';
$_title = 'BeenChilling';
include '../../_head.php';

$reviews = [];

$stm = $_db->query('SELECT r.review_id, r.member_id, r.review_text, u.id, u.name, u.photo
                    FROM `review` r
                    INNER JOIN `user` u ON r.member_id = u.id;');
$reviews = $stm->fetchAll(PDO::FETCH_OBJ);

?>

    <h1 class="horizontal">
        <span>R</span>
        <span>e</span>
        <span>v</span>
        <span>i</span>
        <span>e</span>
        <span>w</span>
        <span>s</span>
    </h1>

    <div id="reviews">
        <?php foreach ($reviews as $review) : ?>
            <div class="reviews">
                <?php if ($review->name === "2.5jo Satoru") : ?>
                    <img src="../../images/photo/<?= $review->photo ?>" alt="profile pic"
                        onmouseover="this.src='../../images/photo/<?= $review->photo ?>'"
                        onmouseout="this.src='../../images/photo/67e93531c71d1_like.png'">
                <?php else : ?>
                    <img src="../../images/photo/<?= $review->photo ?>" alt="profile pic">
                <?php endif; ?>
                <span><?= $review->name ?></span>
                <p><?= $review->review_text ?></p>
            </div>
        <?php endforeach; ?>
    </div>

<?php
include '../../_foot.php';
