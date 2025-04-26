<?php
require '../../_base.php';
$_title = 'BeenChilling';
include '../../_head.php';

// Initialize success/error variables
$success = '';
$error = '';

// Process form submission
if (is_post()) {
    // Check if user is logged in before processing the form
    if ($_user) {
        // Check if the user has a paid order before allowing reviews
        $stm = $_db->prepare('SELECT COUNT(*) FROM `order` WHERE member_id = ? AND payment_status = "paid"');
        $stm->execute([$_user->id]);
        $hasPaidOrder = $stm->fetchColumn() > 0;
        
        if (!$hasPaidOrder) {
            $error = 'You must have a completed purchase to leave a review';
        } else {
            $ratings = post('ratings');
            $review_text = post('review_text');
            
            // Form validation
            if (empty($ratings)) {
                $_err['ratings'] = 'Please select a rating';
            } elseif ($ratings < 1 || $ratings > 5) {
                $_err['ratings'] = 'Rating must be between 1 and 5';
            }
            
            if (empty($review_text)) {
                $_err['review_text'] = 'Please enter your review';
            }
            
            // If no errors, insert the review
            if (empty($_err)) {
                // Get the latest review_id from the database
                $stm = $_db->query('SELECT review_id FROM review ORDER BY review_id DESC LIMIT 1');
                $last_review = $stm->fetch(PDO::FETCH_OBJ);
                
                // Generate new review_id
                if ($last_review) {
                    // Extract the number part and increment
                    $last_id_num = intval(substr($last_review->review_id, 1));
                    $new_id_num = $last_id_num + 1;
                } else {
                    // If no reviews exist yet, start with 1
                    $new_id_num = 1;
                }
                
                // Format the new ID with leading zeros (R0001, R0002, etc.)
                $review_id = 'R' . str_pad($new_id_num, 4, '0', STR_PAD_LEFT);
                
                // Insert the review with the generated ID
                $stm = $_db->prepare('INSERT INTO review (review_id, member_id, ratings, review_text) VALUES (?, ?, ?, ?)');
                $stm->execute([$review_id, $_user->id, $ratings, $review_text]);
                
                // Set success flag directly without redirect
                $success = 'Your review has been submitted!';
                
                // Clear form data
                unset($GLOBALS['ratings']);
                unset($GLOBALS['review_text']);
            }
        }
    } else {
        // Store error in session and redirect to login
        $_SESSION['login_message'] = 'You must be logged in to submit a review';
        redirect('/page/login.php');
        exit;
    }
}

// Fetch all reviews
$stm = $_db->query('SELECT r.review_id, r.member_id, r.ratings, r.review_text, u.id, u.name, u.photo
                    FROM `review` r
                    INNER JOIN `user` u ON r.member_id = u.id
                    ORDER BY r.review_id DESC');
$reviews = $stm->fetchAll(PDO::FETCH_OBJ);

// Check if the current user has a paid order (for UI purposes)
$userHasPaidOrder = false;
if ($_user) {
    $stm = $_db->prepare('SELECT COUNT(*) FROM `order` WHERE member_id = ? AND payment_status = "paid"');
    $stm->execute([$_user->id]);
    $userHasPaidOrder = $stm->fetchColumn() > 0;
}
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

    <!-- Success Popup -->
    <?php if ($success): ?>
    <div id="success-popup" class="popup-notification success">
        <div class="popup-content">
            <span class="close-popup">&times;</span>
            <div class="popup-icon">✓</div>
            <p><?= $success ?></p>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Error Popup -->
    <?php if ($error): ?>
    <div id="error-popup" class="popup-notification error">
        <div class="popup-content">
            <span class="close-popup">&times;</span>
            <div class="popup-icon">!</div>
            <p><?= $error ?></p>
        </div>
    </div>
    <?php endif; ?>

    <div id="reviews">
        <?php foreach ($reviews as $review) : ?>
            <div class="reviews">
                <?php if ($review->name === "2.5jo Satoru") : ?>
                    <img src="/images/photo/<?= $review->photo ?>" alt="profile pic"
                        onmouseover="this.src='/images/photo/<?= $review->photo ?>'"
                        onmouseout="this.src='/images/photo/67e93531c71d1_like.png'">
                <?php else : ?>
                    <img src="/images/photo/<?= $review->photo ?>" alt="profile pic">
                <?php endif; ?>
                <span><?= $review->name ?></span>
                <span>Ratings
                    <?php for ($i = 0; $i < $review->ratings; $i++) : ?>
                    ⭐
                    <?php endfor; ?>
                </span>
                <p><?= $review->review_text ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Review Form -->
    <section id="review-form-section">
        <h2>Share Your Experience</h2>
        
        <?php if ($_user): ?>
            <?php if ($userHasPaidOrder): ?>
                <form method="post" class="review-form">
                    <div class="form-group">
                        <label for="star-rating">Your Rating:</label>
                        <div class="star-rating">
                            <input type="hidden" name="ratings" id="selected-rating" value="<?= isset($_err) ? post('ratings') : '' ?>">
                            <div class="stars">
                                <span class="star" data-rating="1">☆</span>
                                <span class="star" data-rating="2">☆</span>
                                <span class="star" data-rating="3">☆</span>
                                <span class="star" data-rating="4">☆</span>
                                <span class="star" data-rating="5">☆</span>
                            </div>
                            <div class="rating-text">Select your rating</div>
                        </div>
                        <?php if (isset($_err['ratings'])): ?>
                            <div class="error"><?= $_err['ratings'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="review_text">Your Review:</label>
                        <textarea id="review_text" name="review_text" rows="5"><?= isset($_err) ? post('review_text') : '' ?></textarea>
                        <?php if (isset($_err['review_text'])): ?>
                            <div class="error"><?= $_err['review_text'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="submit-review">Submit Review</button>
                    </div>
                </form>
            <?php else: ?>
                <div class="review-restriction">
                    <p style="text-align: center;">Only customers with completed purchases can leave reviews.</p>
                    <p style="text-align: center;"><a href="/page/member/product.php">Browse our products</a> to make a purchase.</p>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="login-prompt">
                <p style="text-align: center;">Please <a href="/page/login.php">login</a> to leave a review.</p>
            </div>
        <?php endif; ?>
    </section>

<?php
include '../../_foot.php';