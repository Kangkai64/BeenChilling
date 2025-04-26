<?php
require '../../_base.php';

auth('Admin');

$_title = 'Update Order';
include '../../_head.php';

if (is_get()) {
    $order_id = isset($_GET['order_id']) ? $_GET['order_id'] : 0;
    
    if (!$order_id) {
        redirect("order_list.php");
    }
    
    // Load order data
    $stm = $_db->prepare('
        SELECT `order`.*, user.name, user.email, user.phone_number
        FROM `order`
        JOIN user ON `order`.member_id = user.id
        WHERE `order`.order_id = ?
    ');
    $stm->execute([$order_id]);
    $order = $stm->fetch(PDO::FETCH_OBJ);
    
    if (!$order) {
        temp('error', 'Order not found');
        redirect("order_list.php");
    }
    
    // Set order data in GLOBALS for form population
    foreach ((array)$order as $key => $value) {
        $GLOBALS[$key] = $value;
    }
}

if (is_post()) {
    $order_id = req('order_id');
    $payment_status = req('payment_status');
    $shipping_address = req('shipping_address');
    $billing_address = req('billing_address');
    
    // Validate payment status
    if ($payment_status == '') {
        $_err['payment_status'] = 'Required';
    } else if (!in_array($payment_status, ['pending', 'awaiting_payment', 'paid', 'failed'])) {
        $_err['payment_status'] = 'Invalid status';
    }

    // Validate shipping address
    if ($shipping_address == '') {
        $_err['shipping_address'] = 'Required';
    }

    // Validate billing address
    if ($billing_address == '') {
        $_err['billing_address'] = 'Required';
    }

    if (!$_err) {
        // Update order
        $stm = $_db->prepare('
            UPDATE `order` 
            SET payment_status = ?, 
                shipping_address = ?, 
                billing_address = ?
            WHERE order_id = ?
        ');
        $stm->execute([$payment_status, $shipping_address, $billing_address, $order_id]);

        temp('info', 'Order updated successfully');
        redirect('order_list.php');
    }
}
?>

<form method="post" class="form" data-title="Update Order">
    <input type="hidden" name="order_id" value="<?= $order_id ?>">
    
    <div class="form-group">
        <label for="order_id">Order ID</label>
        <p><?= $order_id ?></p>
    </div>

    <div class="form-group">
        <label for="name">Customer Name</label>
        <p><?= $name ?></p>
    </div>

    <div class="form-group">
        <label for="email">Email</label>
        <?= html_text('email') ?>
        <?= err('email') ?>
    </div>

    <div class="form-group">
        <label for="phone_number">Phone Number</label>
        <?= html_text('phone_number') ?>
        <?= err('phone_number') ?>
    </div>

    <div class="form-group">
        <label for="total_amount">Total Amount</label>
        <p>RM<?= number_format($total_amount, 2) ?></p>
    </div>

    <div class="form-group">
        <label for="payment_status">Payment Status</label>
        <?= html_select('payment_status', $payment_status_options) ?>
        <?= err('payment_status') ?>
    </div>

    <div class="form-group">
        <label for="order_status">Order Status</label>
        <?= html_select('order_status', $order_status_options) ?>
        <?= err('order_status') ?>
    </div>

    <div class="form-group">
        <label for="shipping_address">Shipping Address</label>
        <textarea name="shipping_address" id="shipping_address" rows="4"><?= htmlspecialchars($shipping_address) ?></textarea>
        <?= err('shipping_address') ?>
    </div>

    <div class="form-group">
        <label for="billing_address">Billing Address</label>
        <textarea name="billing_address" id="billing_address" rows="4"><?= htmlspecialchars($billing_address) ?></textarea>
        <?= err('billing_address') ?>
    </div>

    <section>
        <button type="submit">Update</button>
        <button type="reset">Reset</button>
    </section>
</form>

<button class="button" data-get="order_list.php">Back</button>

<?php include '../../_foot.php'; ?> 