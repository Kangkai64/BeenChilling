<?php
include '../_base.php';

auth('Member');

if (is_post()) {
    $cart = get_or_create_cart();
    if (!$cart) redirect('cart.php');

    // ------------------------------------------
    // DB transaction (insert order and items)
    // ------------------------------------------

    // (A) Begin transaction
    $_db->beginTransaction();
    
    try {
        // (B) Insert order, keep order id
        $stm = $_db->prepare('
            INSERT INTO `order` (member_id, cart_id, total_amount, shipping_address, billing_address, payment_method)
            VALUES (?, ?, 0, ?, ?, ?)
        ');
        
        // Get shipping/billing info from the form
        $shipping_address = $_POST['shipping_address'] ?? null;
        $billing_address = $_POST['billing_address'] ?? null;
        $payment_method = $_POST['payment_method'] ?? null;
        $cart = get_or_create_cart();

        $stm->execute([$_user->id, $cart->cart_id, $shipping_address, $billing_address, $payment_method]);
        $order_id = $_db->lastInsertId();
        
        // (C) Insert order items
        $stm = $_db->prepare('
            INSERT INTO order_item (order_id, product_id, quantity, price)
            VALUES (?, ?, ?, (SELECT price FROM product WHERE product_id = ?))
        ');

        $total_amount = 0;
        foreach ($cart as $product_id => $quantity) {
            // Get current product price
            $price_stm = $_db->prepare('SELECT price FROM product WHERE ProductID = ?');
            $price_stm->execute([$product_id]);
            $price = $price_stm->fetchColumn();
            
            // Insert order item
            $stm->execute([$order_id, $product_id, $quantity, $product_id]);
            
            // Calculate total
            $total_amount += ($price * $quantity);
        }

        // (D) Update order total amount
        $stm = $_db->prepare('
            UPDATE `order`
            SET total_amount = ?
            WHERE order_id = ?
        ');
        
        $stm->execute([$total_amount, $order_id]);

        // (E) Commit transaction
        $_db->commit();

        // (3) Clear shopping cart
        clear_cart($cart->cart_id);

        // (4) Redirect to order details page
        temp('info', 'Order placed successfully');
        redirect("order-detail.php?id=$order_id");
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $_db->rollBack();
        temp('error', 'Order processing failed: ' . $e->getMessage());
        redirect('cart.php');
    }
}

redirect('cart.php');