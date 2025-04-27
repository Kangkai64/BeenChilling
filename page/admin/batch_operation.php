<?php
include '../../_base.php';
auth('Admin');

// Get the table information based on the page parameter
$table = $_GET['table'] ?? 'user';
$valid_tables = ['user', 'product', 'order'];

if (!in_array($table, $valid_tables)) {
    temp('error', 'Invalid table specified');
    redirect('product_list.php');
}

// Define table configurations
$table_config = [
    'user' => [
        'table_name' => 'user',
        'primary_key' => 'id',
        'display_name' => 'User',
        'fields' => [
            ['name' => 'id', 'required' => true, 'display' => 'ID'],
            ['name' => 'email', 'required' => true, 'display' => 'Email'],
            ['name' => 'password', 'required' => true, 'display' => 'Password'],
            ['name' => 'name', 'required' => true, 'display' => 'Name'],
            ['name' => 'photo', 'required' => false, 'display' => 'Photo'],
            ['name' => 'phone_number', 'required' => true, 'display' => 'Phone Number'],
            ['name' => 'reward_point', 'required' => false, 'display' => 'Reward Points'],
            ['name' => 'status', 'required' => false, 'display' => 'Status'],
            ['name' => 'role', 'required' => true, 'display' => 'Role'],
            ['name' => 'created_at', 'required' => false, 'display' => 'Created At'],
            ['name' => 'updated_at', 'required' => false, 'display' => 'Updated At'],
            ['name' => 'deleted_at', 'required' => false, 'display' => 'Deleted At']
        ],
        'back_link' => 'user_list.php'
    ],
    'product' => [
        'table_name' => 'product',
        'primary_key' => 'product_id',
        'display_name' => 'Product',
        'fields' => [
            ['name' => 'product_id', 'required' => true, 'display' => 'ID'],
            ['name' => 'product_name', 'required' => true, 'display' => 'Name'],
            ['name' => 'price', 'required' => true, 'display' => 'Price'],
            ['name' => 'description', 'required' => true, 'display' => 'Description'],
            ['name' => 'product_image', 'required' => false, 'display' => 'Image'],
            ['name' => 'type_id', 'required' => true, 'display' => 'Type ID'],
            ['name' => 'stock', 'required' => true, 'display' => 'Stock']
        ],
        'back_link' => 'product_list.php'
    ],
    'order' => [
        'table_name' => 'order',
        'primary_key' => 'order_id',
        'display_name' => 'Order',
        'fields' => [
            ['name' => 'order_id', 'required' => true, 'display' => 'ID'],
            ['name' => 'member_id', 'required' => true, 'display' => 'Member ID'],
            ['name' => 'cart_id', 'required' => true, 'display' => 'Cart ID'],
            ['name' => 'total_amount', 'required' => true, 'display' => 'Total Amount'],
            ['name' => 'shipping_address', 'required' => true, 'display' => 'Shipping Address'],
            ['name' => 'billing_address', 'required' => true, 'display' => 'Billing Address'],
            ['name' => 'payment_method', 'required' => true, 'display' => 'Payment Method'],
            ['name' => 'payment_status', 'required' => true, 'display' => 'Payment Status'],
            ['name' => 'order_status', 'required' => true, 'display' => 'Order Status']
        ],
        'back_link' => 'order_list.php'
    ]
];

// Get current table configuration
$current_config = $table_config[$table];
$primary_key = $current_config['primary_key'];
$display_name = $current_config['display_name'];
$fields = $current_config['fields'];
$back_link = $current_config['back_link'];
$table_name = $current_config['table_name'];

// Process batch operations
$batch_message = '';
$debug_log = []; // Array to store detailed error information

if (is_post() && isset($_POST['batch_action'])) {
    $batch_action = $_POST['batch_action'];
    
    // Handle file upload for batch operations
    if (isset($_FILES['batch_file']) && $_FILES['batch_file']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['batch_file']['tmp_name'];
        $file_name = $_FILES['batch_file']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Check if the file is CSV or TXT
        if ($file_ext === 'csv' || $file_ext === 'txt') {
            $file_content = file_get_contents($file_tmp);
            $lines = explode(PHP_EOL, $file_content);
            
            $success_count = 0;
            $error_count = 0;
            
            // Enable logging with line numbers
            $line_number = 0;
            
            if ($batch_action === 'insert') {
                // Batch Insert
                foreach ($lines as $line) {
                    $line_number++;
                    if (empty(trim($line))) continue;
                    
                    $data = str_getcsv($line);
                    if (count($data) < count(array_filter($fields, function($field) { return $field['required']; }))) {
                        $debug_log[] = "Line $line_number: Insufficient data (required fields missing)";
                        $error_count++;
                        continue;
                    }
                    
                    try {
                        // Prepare field names and placeholders
                        $field_names = [];
                        $placeholders = [];
                        $values = [];
                        
                        // Map CSV data to fields
                        for ($i = 0; $i < count($fields) && $i < count($data); $i++) {
                            if (!empty($data[$i])) {
                                $field_names[] = $fields[$i]['name'];
                                $placeholders[] = '?';
                                $values[] = $data[$i];
                            }
                        }
                        
                        if (empty($field_names)) {
                            $debug_log[] = "Line $line_number: No valid fields to insert";
                            $error_count++;
                            continue;
                        }
                        
                        $sql = "INSERT INTO " . $table_name . " (" . implode(', ', $field_names) . ") VALUES (" . implode(', ', $placeholders) . ")";
                        $stmt = $_db->prepare($sql);
                        $result = $stmt->execute($values);
                        
                        if ($result) {
                            $success_count++;
                        } else {
                            $error_info = $stmt->errorInfo();
                            $debug_log[] = "Line $line_number: Database error - " . ($error_info[2] ?? 'Unknown error');
                            $error_count++;
                        }
                    } catch (PDOException $e) {
                        $debug_log[] = "Line $line_number: Exception - " . $e->getMessage();
                        $error_count++;
                    }
                }
                
            } elseif ($batch_action === 'update') {
                // Batch Update
                foreach ($lines as $line) {
                    $line_number++;
                    if (empty(trim($line))) continue;
                    
                    $data = str_getcsv($line);
                    if (count($data) < 2) {
                        $debug_log[] = "Line $line_number: Insufficient data (need at least ID and one field)";
                        $error_count++;
                        continue;
                    }
                    
                    $id_value = $data[0];
                    if (!$id_value) {
                        $debug_log[] = "Line $line_number: Missing ID value";
                        $error_count++;
                        continue;
                    }
                    
                    try {
                        // Determine which fields to update based on provided data
                        $update_fields = [];
                        $update_values = [];
                        
                        // Skip the first column (ID) and process remaining fields
                        for ($i = 1; $i < count($data) && $i < count($fields); $i++) {
                            if (isset($data[$i]) && $data[$i] !== '') {
                                $update_fields[] = $fields[$i]['name'] . " = ?";
                                $update_values[] = $data[$i];
                            }
                        }
                        
                        if (empty($update_fields)) {
                            $debug_log[] = "Line $line_number: No fields to update";
                            $error_count++;
                            continue;
                        }

                        if ($table === 'user') {
                            if (!empty($update_fields['photo'])) {
                                // Check if the photo file exists in images/photo folder
                                if (file_exists('../../images/photo/' . $update_fields['photo'])) {
                                    $update_fields['photo'] = $update_fields['photo'];
                                } else {
                                    $update_fields['photo'] = 'default_avatar.png';
                                }
                            }
                        }
                        
                        $update_sql = "UPDATE " . $table_name . " SET " . implode(", ", $update_fields) . " WHERE " . $primary_key . " = ?";
                        $update_values[] = $id_value;
                        
                        $stmt = $_db->prepare($update_sql);
                        $result = $stmt->execute($update_values);
                        
                        if ($result) {
                            if ($stmt->rowCount() > 0) {
                                $success_count++;
                            } else {
                                $debug_log[] = "Line $line_number: ID not found - $id_value";
                                $error_count++;
                            }
                        } else {
                            $error_info = $stmt->errorInfo();
                            $debug_log[] = "Line $line_number: Database error - " . ($error_info[2] ?? 'Unknown error');
                            $error_count++;
                        }
                    } catch (PDOException $e) {
                        $debug_log[] = "Line $line_number: Exception - " . $e->getMessage();
                        $error_count++;
                    }
                }
            } elseif ($batch_action === 'delete') {
                // Batch Delete
                $ids_to_delete = [];

                foreach ($lines as $line) {
                    $line_number++;
                    if (empty(trim($line))) continue;

                    $id_value = trim($line);
                    if (!$id_value) {
                        $debug_log[] = "Line $line_number: Missing ID value";
                        $error_count++;
                        continue;
                    }

                    // Add valid IDs to the array
                    $ids_to_delete[] = $id_value;
                }

                if (!empty($ids_to_delete)) {
                    // Determine the appropriate delete page based on current table
                    $delete_pages = [
                        'user' => 'user_delete.php',
                        'product' => 'product_delete.php',
                        'order' => 'order_delete.php'
                    ];

                    $delete_page = $delete_pages[$table] ?? '';

                    if (!empty($delete_page)) {
                        // Create IDs list and redirect
                        $id_list = implode(',', $ids_to_delete);
                        redirect($delete_page . '?batch=1&ids=' . urlencode($id_list));
                    } else {
                        // Fallback to direct database deletion if no specific delete page found
                        foreach ($ids_to_delete as $id_value) {
                            try {
                                $stmt = $_db->prepare("DELETE FROM " . $table_name . " WHERE " . $primary_key . " = ?");
                                $result = $stmt->execute([$id_value]);

                                if ($result) {
                                    if ($stmt->rowCount() > 0) {
                                        $success_count++;
                                    } else {
                                        $debug_log[] = "ID not found - $id_value";
                                        $error_count++;
                                    }
                                } else {
                                    $error_info = $stmt->errorInfo();
                                    $debug_log[] = "Database error - " . ($error_info[2] ?? 'Unknown error');
                                    $error_count++;
                                }
                            } catch (PDOException $e) {
                                $debug_log[] = "Exception - " . $e->getMessage();
                                $error_count++;
                            }
                        }
                        temp('info', "Batch Delete completed: $success_count records deleted successfully, $error_count failed");
                    }
                } else {
                    temp('error', "No valid IDs found for deletion");
                }
        }
            
            // Set appropriate message based on results
            $action_text = ucfirst($batch_action);
            temp('info', "Batch $action_text completed: $success_count records processed successfully, $error_count failed");
        } else {
            temp('error', "Error: Only CSV or TXT files are allowed.");
        }
    } else {
        $upload_error = $_FILES['batch_file']['error'] ?? UPLOAD_ERR_NO_FILE;
        $error_message = match($upload_error) {
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize directive in php.ini',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE directive in the HTML form',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload',
            default => 'Unknown upload error'
        };
        $_err['batch_file'] = "Error: $error_message";
    }
}

$_title = 'BeenChilling';
include '../../_head.php';
?>

<div class="batch-operations-panel">
    <h3>Batch Operations - <?= htmlspecialchars($display_name) ?></h3>
    <form method="post" enctype="multipart/form-data" class="form-group" data-title="Batch Operations">
        <div class="form-group">
            <label for="batch_file">Upload CSV or TXT File:</label>
            <?= html_file('batch_file', '*.csv,*.txt') ?>
            <?= err('batch_file') ?>
            <strong>Format:</strong>
            <ul>
                <li><strong>Insert:</strong> 
                    <?php 
                    $field_names = array_map(function($field) {
                        return $field['display'];
                    }, $fields);
                    echo htmlspecialchars(implode(',', $field_names));
                    ?>
                </li>
                <li><strong>Update:</strong> 
                    <?= htmlspecialchars($fields[0]['display']) ?> followed by fields to update
                </li>
                <li><strong>Delete:</strong> One ID per line</li>
            </ul>
        </div>
        <div class="form-group">
            <div class="checkbox-group">
                <label style="font-size: 1em;">
                    <input type="checkbox" name="debug_mode" value="1" <?= isset($_POST['debug_mode']) ? 'checked' : '' ?>>
                    Enable Debug Mode
                </label>
            </div>
        </div>
        <div class="form-group batch-buttons">
            <button type="submit" name="batch_action" value="insert" class="button batch-button">Batch Insert</button>
            <button type="submit" name="batch_action" value="update" class="button batch-button">Batch Update</button>
            <button type="submit" name="batch_action" value="delete" class="button batch-button">Batch Delete</button>
        </div>
    </form>
</div>

<?php if (!empty($debug_log) && isset($_POST['debug_mode'])): ?>
<div class="debug-panel">
    <h3>Debug Information</h3>
    <div class="debug-content">
        <pre><?php foreach($debug_log as $log_entry) echo htmlspecialchars($log_entry) . "\n"; ?></pre>
    </div>
</div>
<?php endif; ?>

<button class="button" data-get="<?= htmlspecialchars($back_link) ?>">Back</button>

<?php
include '../../_foot.php';