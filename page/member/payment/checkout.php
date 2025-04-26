<?php
require '../../../_base.php';

// Ensure user is logged in as a member
auth('Member');

// Function to create an order from the cart
function create_order($cart_id)
{
    global $_db, $_user;

    try {
        $order_data = null;
        $_db->beginTransaction();

        // Get cart summary
        $cart_summary = get_cart_summary($cart_id);
        $total_amount = $cart_summary->total_price ?? 0;

        // Fetch the latest order ID
        $stmt = $_db->query("SELECT order_id FROM `order` ORDER BY order_id DESC LIMIT 1");
        $lastId = $stmt->fetchColumn();

        if ($lastId && preg_match('/OR(\d+)/', $lastId, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = 1; // If no records exist yet
        }

        // Format the new order ID with leading zeroes (e.g., OR0004)
        $order_id = 'OR' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        // Process shipping address
        $shipping_address = [];

        if (isset($_POST['shipping_address_option']) && $_POST['shipping_address_option'] === 'saved') {
            // Get saved address from database
            $shipping_address_id = $_POST['saved_shipping_address_id'];

            $stm = $_db->prepare('SELECT * FROM shipping_address WHERE shipping_address_id = ? AND user_id = ?');
            $stm->execute([$shipping_address_id, $_user->id]);
            $saved_address = $stm->fetch(PDO::FETCH_ASSOC);

            if ($saved_address) {
                $shipping_address = $saved_address;
            }
        } else {
            // Process new address
            $shipping_address = [
                'address_name' => $_POST['shipping_address_name'] ?? '',
                'recipient_name' => $_POST['shipping_recipient_name'] ?? '',
                'street_address' => $_POST['shipping_street_address'] ?? '',
                'city' => $_POST['shipping_city'] ?? '',
                'state' => $_POST['shipping_state'] ?? '',
                'postal_code' => $_POST['shipping_postal_code'] ?? '',
                'country' => $_POST['shipping_country'] ?? 'Malaysia',
                'address_phone_number' => $_POST['shipping_phone_number'] ?? ''
            ];

            // Save new address if requested
            if (isset($_POST['save_shipping_address']) && $_POST['save_shipping_address'] == '1') {
                // Fetch the latest shipping address ID
                $stmt = $_db->query("SELECT shipping_address_id FROM shipping_address ORDER BY shipping_address_id DESC LIMIT 1");
                $lastId = $stmt->fetchColumn();

                if ($lastId && preg_match('/SA(\d+)/', $lastId, $matches)) {
                    $nextNumber = intval($matches[1]) + 1;
                } else {
                    $nextNumber = 1; // If no records exist yet
                }

                // Format the new shipping address ID with leading zeros (e.g., SA0004)
                $shipping_address_id = 'SA' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

                $stm = $_db->prepare('
            INSERT INTO shipping_address (
                shipping_address_id, user_id, address_name, recipient_name,
               street_address, city, state, postal_code, country, address_phone_number
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
       ');
                $stm->execute([
                    $shipping_address_id,
                    $_user->id,
                    $shipping_address['address_name'],
                    $shipping_address['recipient_name'],
                    $shipping_address['street_address'],
                    $shipping_address['city'],
                    $shipping_address['state'],
                    $shipping_address['postal_code'],
                    $shipping_address['country'],
                    $shipping_address['address_phone_number']
                ]);
            }
        }

        // Process billing address
        if (isset($_POST['same_address']) && $_POST['same_address'] == '1') {
            // Use shipping address as billing address
            $billing_address = $shipping_address;
        } else {
            // Process billing address
            if (isset($_POST['billing_address_option']) && $_POST['billing_address_option'] === 'saved') {
                $billing_address_id = $_POST['saved_billing_address_id'];

                $stm = $_db->prepare('SELECT * FROM shipping_address WHERE shipping_address_id = ? AND user_id = ?');
                $stm->execute([$billing_address_id, $_user->id]);
                $saved_address = $stm->fetch(PDO::FETCH_ASSOC);

                if ($saved_address) {
                    $billing_address = $saved_address;
                }
            } else {
                // Process new billing address
                $billing_address = [
                    'address_name' => $_POST['billing_address_name'] ?? '',
                    'recipient_name' => $_POST['billing_recipient_name'] ?? '',
                    'street_address' => $_POST['billing_street_address'] ?? '',
                    'city' => $_POST['billing_city'] ?? '',
                    'state' => $_POST['billing_state'] ?? '',
                    'postal_code' => $_POST['billing_postal_code'] ?? '',
                    'country' => $_POST['billing_country'] ?? 'Malaysia',
                    'address_phone_number' => $_POST['billing_phone_number'] ?? ''
                ];

                // Save new billing address if requested
                if (isset($_POST['save_billing_address']) && $_POST['save_billing_address'] == '1') {
                    // Fetch the latest shipping address ID
                    $stmt = $_db->query("SELECT shipping_address_id FROM shipping_address WHERE shipping_address_id LIKE 'SA%' ORDER BY shipping_address_id DESC LIMIT 1");
                    $lastId = $stmt->fetchColumn();

                    if ($lastId && preg_match('/SA(\d+)/', $lastId, $matches)) {
                        $nextNumber = intval($matches[1]) + 1;
                    } else {
                        $nextNumber = 1; // If no records exist yet
                    }

                    // Format the new shipping address ID with leading zeros (e.g., SA0004)
                    $billing_address_id = 'SA' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

                    $stm = $_db->prepare('
                INSERT INTO shipping_address (
                    shipping_address_id, user_id, address_name, recipient_name,
                    street_address, city, state, postal_code, country, address_phone_number
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ');
                    $stm->execute([
                        $billing_address_id,
                        $_user->id,
                        $billing_address['address_name'],
                        $billing_address['recipient_name'],
                        $billing_address['street_address'],
                        $billing_address['city'],
                        $billing_address['state'],
                        $billing_address['postal_code'],
                        $billing_address['country'],
                        $billing_address['address_phone_number']
                    ]);
                }
            }
        }


        // Format addresses for database storage
        $formatted_shipping_address = implode(', ', array_filter([
            $shipping_address['recipient_name'] ?? '',
            $shipping_address['address_phone_number'] ?? '',
            $shipping_address['street_address'] ?? '',
            $shipping_address['city'] ?? '',
            $shipping_address['state'] ?? '',
            $shipping_address['postal_code'] ?? '',
            $shipping_address['country'] ?? 'Malaysia'
        ]));

        $formatted_billing_address = implode(', ', array_filter([
            $billing_address['recipient_name'] ?? '',
            $billing_address['address_phone_number'] ?? '',
            $billing_address['street_address'] ?? '',
            $billing_address['city'] ?? '',
            $billing_address['state'] ?? '',
            $billing_address['postal_code'] ?? '',
            $billing_address['country'] ?? 'Malaysia'
        ]));

        // Insert the order with address information - keep payment_status as "pending"
        $stm = $_db->prepare('
            INSERT INTO `order` (order_id, member_id, cart_id, order_date, total_amount, 
            shipping_address, billing_address, payment_method, payment_status, order_status)
            VALUES (?, ?, ?, NOW(), ?, ?, ?, "Billplz", "pending", "pending")
        ');
        $result = $stm->execute([
            $order_id,
            $_user->id,
            $cart_id,
            $total_amount,
            $formatted_shipping_address,
            $formatted_billing_address
        ]);

        if (!$result) {
            error_log("Error creating order: " . print_r($_db->errorInfo(), true)); // Log database error
            throw new Exception("Failed to create order");
        }

        // Mark the cart as ordered
        $stm = $_db->prepare('UPDATE cart SET status = "ordered" WHERE cart_id = ?');
        $stm->execute([$cart_id]);

        // Transfer cart items to order items
        $stm = $_db->prepare('
            SELECT * FROM cart_item
            WHERE cart_id = ?
            ');
        $stm->execute([$cart_id]);
        $cart_items = $stm->fetchAll(PDO::FETCH_ASSOC);

        // Debug cart items
        error_log("Cart items: " . print_r($cart_items, true));
        var_dump($cart_items);

        foreach ($cart_items as $item) {
            // Generate a unique order item ID
            $order_item_id = 'OI' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);

            // Insert the item into order_item table
            $stm = $_db->prepare('
            INSERT INTO order_item (
                order_item_id, order_id, product_id, quantity, price
            ) VALUES (?, ?, ?, ?, ?)
        ');

            $result = $stm->execute([
                $order_item_id,
                $order_id,
                $item['product_id'],
                $item['quantity'],
                $item['price']
            ]);

            if (!$result) {
                error_log("Error creating order item: " . print_r($_db->errorInfo(), true));
                throw new Exception("Failed to create order item");
            }
        }

        $_db->commit();

        // Return order information
        return [
            'order_id' => $order_id,
            'total_amount' => $total_amount
        ];
    } catch (Exception $e) {
        $_db->rollBack();
        throw $e;
    }
}

// Store Billplz payment information
function store_billplz_info($order_id, $bill_id, $collection_id)
{
    global $_db;

    // Update payment status to "awaiting_payment" when linking to Billplz
    $stm = $_db->prepare('
        UPDATE `order` 
        SET billplz_bill_id = ?, 
            billplz_collection_id = ?,
            payment_status = "awaiting_payment" 
        WHERE order_id = ?
    ');
    return $stm->execute([$bill_id, $collection_id, $order_id]);
}

// Process checkout - BEFORE ANY HTML OUTPUT
if (is_post() && isset($_POST['btn']) && $_POST['btn'] === 'confirm') {
    try {
        $pending_order_id = req('order_id');
        $order = null;

        // Check if this is an existing order
        if ($pending_order_id) {
            $stm = $_db->prepare('SELECT * FROM `order` WHERE order_id = ? AND member_id = ?');
            $stm->execute([$pending_order_id, $_user->id]);
            $order = $stm->fetch(PDO::FETCH_OBJ);

            // If this is an existing pending order, we'll continue with payment
            if ($order && ($order->payment_status == 'pending' || $order->payment_status == 'failed' || $order->payment_status == 'awaiting_payment')) {
                // Use existing order data
                $order_data = [
                    'order_id' => $order->order_id,
                    'total_amount' => $order->total_amount
                ];
            } else {
                // Invalid order or not in a state that can be paid
                temp('info', "Invalid order or order cannot be paid at this time.");
                redirect('/page/member/cart.php');
            }
        } else {
            // Create a new order from cart
            $cart = get_or_create_cart();

            if ($cart) {
                $cart_summary = get_cart_summary($cart->cart_id);

                // Modified check for empty cart - use isset() to verify properties exist
                if (!$cart_summary || !isset($cart_summary->total_items) || $cart_summary->total_items < 1) {
                    temp('info', "Your cart is empty. Please add products to your cart before checkout.");
                    redirect('/page/member/cart.php');
                }

                $order_data = create_order($cart->cart_id);
                if (!$order_data) {  // Simplified empty check
                    temp('info', "Unable to process the order. Please try again.");
                    redirect('/page/member/cart.php');
                }
            } else {
                temp('info', "Your cart is empty. Please add products to your cart before checkout.");
                redirect('/page/member/cart.php');
            }
        }

        // Proceed with payment processing
        // Billplz API credentials
        $api_key = 'c4829771-97fd-40ee-a49f-10385d8f587b';
        $collection_id = 'racg2vr3';
        $x_signature_key = '08251797c8178a8bd90a55eb721f622cb59b5f17424e25280b278a6bc9b09365350c95aa6faac986fd01c2d282d7539bcd79b4075377e9a74ffdb856f7175810';

        // Get the absolute URLs for callback and redirect
        $site_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
        $callback_url = 'https://8860-2001-f40-97c-7b29-a58d-4218-69ba-851c.ngrok-free.app/page/member/payment/payment_callback.php';
        $redirect_url = $site_url . '/page/member/payment/payment_status.php?order_id=' . $order_data['order_id'];

        // Prepare API request to Billplz
        $params = [
            'collection_id' => $collection_id,
            'email' => $_user->email,
            'name' => $_user->name,
            'amount' => round($order_data['total_amount'] * 100), // Convert to cents
            'callback_url' => $callback_url,
            'redirect_url' => $redirect_url,
            'description' => 'Payment for Order #' . $order_data['order_id'],
            'reference_1_label' => 'Order ID',
            'reference_1' => $order_data['order_id']
        ];

        // Make API call to create bill
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://www.billplz-sandbox.com/api/v3/bills');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERPWD, $api_key . ':');
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
        $response = curl_exec($curl);

        // Check for cURL errors
        if (curl_errno($curl)) {
            curl_close($curl);
            throw new Exception("Connection Error: " . curl_error($curl));
        } else {
            $result = json_decode($response, true);
            curl_close($curl);

            if (isset($result['id']) && isset($result['url'])) {
                // Store Billplz bill ID and update status to "awaiting_payment"
                $stored = store_billplz_info($order_data['order_id'], $result['id'], $collection_id);

                if ($stored) {
                    // Redirect to Billplz payment page
                    header("Location: " . $result['url']);
                    exit; // Make sure script stops here
                } else {
                    throw new Exception("Error storing payment information. Please try again.");
                }
            } else {
                // Handle API error
                $error_message = isset($result['error']['message']) ? $result['error']['message'] : "Please try again later.";
                temp('info', "Payment gateway error: " . $error_message);
                redirect('/page/member/cart.php');
            }
        }
    } catch (Exception $e) {
        temp('info', $e->getMessage());
        error_log("Checkout Error: " . $e->getMessage());
        redirect('/page/member/cart.php');
    }
}

if (is_get()) {
    // Get order information from URL parameter
    $order_id = req('order_id');

    if ($order_id) {
        // Check if this is an existing order that needs payment
        $stm = $_db->prepare('SELECT * FROM `order` WHERE order_id = ? AND member_id = ?');
        $stm->execute([$order_id, $_user->id]);
        $order = $stm->fetch(PDO::FETCH_OBJ);

        if ($order) {
            // Get order items
            $stm = $_db->prepare('SELECT * FROM order_item WHERE order_id = ?');
            $stm->execute([$order_id]);
            $order_items = $stm->fetchAll(PDO::FETCH_OBJ);

            // Get product information
            if ($order_items) {
                $placeholders = implode(',', array_fill(0, count($order_items), '?'));
                $stm = $_db->prepare("SELECT * FROM product WHERE product_id IN ($placeholders)");

                // Extract product_ids from $order_items
                $product_ids = array_map(function ($item) {
                    return $item->product_id;
                }, $order_items);

                // Execute with the array of product_ids
                $stm->execute($product_ids);
                $productResults = $stm->fetchAll(PDO::FETCH_OBJ);

                // Create an associative array with product_id as key
                $products = [];
                foreach ($productResults as $product) {
                    $products[$product->product_id] = $product;
                }
            }
        } else {
            // Order not found or doesn't belong to this user
            temp('info', "Order not found or you don't have permission to access it.");
            $order_items = [];
            $products = [];
        }
    } else {
        // No order ID provided, show cart information
        $cart = get_or_create_cart();
        $cart_items = $cart ? get_cart_items($cart->cart_id) : [];
        $cart_summary = $cart ? get_cart_summary($cart->cart_id) : null;
        $order = null;
        $order_items = [];
    }
}

$_title = 'BeenChilling';
include '../../../_head.php';

topics_text("Checkout", "200px");
?>

<div class="checkout-container">
    <h2>Order Summary</h2>

    <?php if (isset($order) && ($order->payment_status == 'pending' || $order->payment_status == 'failed')): ?>
        <div class="alert alert-info">
            <p><strong>Continue payment for order #<?= htmlspecialchars($order->order_id) ?></strong></p>
            <p>This order requires payment to be completed.</p>
        </div>
    <?php endif; ?>

    <table class="checkout-table">
        <tr>
            <th>Product</th>
            <th>Price (RM)</th>
            <th>Quantity</th>
            <th>Subtotal (RM)</th>
        </tr>

        <?php if (!empty($order_items)): ?>
            <?php foreach ($order_items as $item): ?>
                <?php if (isset($products[$item->product_id])): ?>
                    <tr>
                        <td><?= htmlspecialchars($products[$item->product_id]->product_name) ?></td>
                        <td class="right"><?= number_format($products[$item->product_id]->price, 2) ?></td>
                        <td class="center"><?= $item->quantity ?></td>
                        <td class="right"><?= number_format($products[$item->product_id]->price * $item->quantity, 2) ?></td>
                    </tr>
                <?php else: ?>
                    <tr>
                        <td colspan="4">Product ID "<?= htmlspecialchars($item->product_id) ?>" not found</td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>

            <?php if ($order): ?>
                <tr class="total-row">
                    <th colspan="3">Total</th>
                    <th class="right">RM <?= number_format($order->total_amount, 2) ?></th>
                </tr>
            <?php endif; ?>
        <?php elseif (!empty($cart_items)): ?>
            <?php foreach ($cart_items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item->product_name) ?></td>
                    <td class="right"><?= number_format($item->price, 2) ?></td>
                    <td class="center"><?= $item->quantity ?></td>
                    <td class="right"><?= number_format($item->price * $item->quantity, 2) ?></td>
                </tr>
            <?php endforeach; ?>

            <tr class="total-row">
                <th colspan="2">Total</th>
                <th><?= $cart_summary->total_items ?? 0 ?> items</th>
                <th class="right">RM <?= number_format($cart_summary->total_price ?? 0, 2) ?></th>
            </tr>
        <?php else: ?>
            <tr>
                <td colspan="4" class="center">Your cart is empty</td>
            </tr>
        <?php endif; ?>
    </table>

    <form method="post" action="" id="checkout-form">
        <?php if (isset($order) && $order !== null): ?>
            <input type="hidden" name="order_id" value="<?= htmlspecialchars($order->order_id) ?>">
        <?php endif; ?>

        <div class="customer-info">
            <br>
            <?php
            $total_amount = isset($order) ? $order->total_amount : ($cart_summary->total_price ?? 0);
            // Calculate potential reward points (RM1 = 1 point)
            $potential_points = floor($total_amount); // No rounding, simply floor value

            // Display this information on the checkout page
            echo '<div class="reward-points-info">';
            echo '<p><i class="fas fa-gift"></i> You will earn <strong>' . $potential_points . ' reward points</strong> with this purchase!</p>';
            echo '</div>';
            ?>
            <hr>
            <h2>Customer Information</h2>
            <p><strong>Name:</strong> <?= htmlspecialchars($_user->name) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($_user->email) ?></p>

            <?php if (!isset($order) || $order === null): ?>
                <?php
                // Fetch all saved addresses for the user
                $stm = $_db->prepare('
            SELECT * FROM shipping_address 
            WHERE user_id = ? 
            ORDER BY created_at DESC
        ');
                $stm->execute([$_user->id]);
                $saved_addresses = $stm->fetchAll(PDO::FETCH_OBJ);
                ?>

                <div class="address-section">
                    <hr>
                    <h2>Shipping Address</h2>

                    <div class="address-selection">
                        <?php if (count($saved_addresses) > 0): ?>
                            <div class="form-group">
                                <label>
                                    <input type="radio" name="shipping_address_option" value="saved" checked>
                                    Use a saved address
                                </label>

                                <div class="saved-addresses-container">
                                    <select id="saved_shipping_address" name="saved_shipping_address_id" class="form-control">
                                        <?php foreach ($saved_addresses as $address): ?>
                                            <option value="<?= htmlspecialchars($address->shipping_address_id) ?>">
                                                <?= htmlspecialchars($address->address_name) ?> -
                                                <?= htmlspecialchars($address->recipient_name) ?>,
                                                <?= htmlspecialchars($address->street_address) ?>,
                                                <?= htmlspecialchars($address->city) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>
                                    <input type="radio" name="shipping_address_option" value="new">
                                    Enter a new address
                                </label>
                            </div>
                        <?php else: ?>
                            <input type="hidden" name="shipping_address_option" value="new">
                        <?php endif; ?>

                        <div id="new_shipping_address" class="new-address-form" <?= count($saved_addresses) > 0 ? 'style="display:none;"' : '' ?>>
                            <!-- Shipping address form fields (unchanged) -->
                        </div>
                    </div>

                    <!-- Rest of the checkout form when creating new orders -->
                </div>
            <?php else: ?>
                <!-- Show order address information for existing orders -->
                <div class="address-section">
                    <hr>
                    <h2>Shipping Address</h2>
                    <p><?= nl2br(htmlspecialchars($order->shipping_address)) ?></p>

                    <h2>Billing Address</h2>
                    <p><?= nl2br(htmlspecialchars($order->billing_address)) ?></p>
                </div>
            <?php endif; ?>

            <div class="payment-section">
                <h2>Payment Method</h2>
                <p>You will be redirected to Billplz to complete your payment securely.</p>

                <?php if (!empty($cart_items) || !empty($order_items)): ?>
                    <div class="payment-info">
                        <p>By clicking 'Confirm and Pay', you agree to our terms and conditions.</p>
                        <p>Your payment will be processed securely through Billplz.</p>
                    </div>
                <?php endif; ?>
            </div>

            <section class="checkout-button-group">
                <?php if (isset($order) && $order !== null): ?>
                    <button class="button" data-get="/page/member/order_history.php">Back to Order History</button>
                <?php else: ?>
                    <button class="button" data-get="/page/member/cart.php">Back to Cart</button>
                <?php endif; ?>

                <?php if (!(empty($cart_items) && empty($order_items))): ?>
                    <button type="submit" name="btn" value="confirm" class="button">Confirm and Pay</button>
                <?php endif; ?>
            </section>

        </div>
    </form>
</div>

<?php
include '../../../_foot.php';
