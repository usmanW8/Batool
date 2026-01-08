<?php
/**
 * Additional Email Helper Functions for Status Updates
 * Include this file after email-helper.php
 */

/**
 * Send Order Status Update Email
 * 
 * @param string $customerEmail Customer's email address
 * @param string $order_id Order ID
 * @param string $customer_name Customer name
 * @param string $newStatus New order status
 * @param string $orderType Type of order ('custom_request' or 'portfolio_order')
 * @param array $productInfo Additional product/order information
 * @return bool True on success, false on failure
 */
function sendStatusUpdateEmail($customerEmail, $order_id, $customer_name, $newStatus, $orderType = 'portfolio_order', $productInfo = []) {
    $mail = new PHPMailer;
   
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'batoolsaptitude@gmail.com';
        $mail->Password = 'hfyn gssx fixm yxii';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        
        // Recipients
        $mail->setFrom('batoolsaptitude@gmail.com', "Batool's Aptitude");
        $mail->addAddress($customerEmail, $customer_name);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Order Status Update - ' . $order_id;
        $mail->Body = getStatusUpdateEmailTemplate($order_id, $customer_name, $newStatus, $orderType, $productInfo);
        $mail->AltBody = getStatusUpdatePlainText($order_id, $customer_name, $newStatus, $orderType);
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('Status Update Email Error: ' . $mail->ErrorInfo);
        return false;
    }
}

/**
 * HTML Template for Status Update Email
 */
function getStatusUpdateEmailTemplate($order_id, $customer_name, $status, $orderType, $productInfo) {
    // Status information mapping
    $statusInfo = [
        // Portfolio Order Statuses
        'pending' => ['icon' => '⏳', 'color' => '#f59e0b', 'title' => 'Order Pending', 'message' => 'Your order is being reviewed by our team.'],
        'processing' => ['icon' => '⚙️', 'color' => '#3b82f6', 'title' => 'Order Processing', 'message' => 'Great news! Your order is now being processed and will be ready soon.'],
        'shipped' => ['icon' => '📦', 'color' => '#8b5cf6', 'title' => 'Order Shipped', 'message' => 'Your order has been shipped! It\'s on its way to you.'],
        'delivered' => ['icon' => '✅', 'color' => '#10b981', 'title' => 'Order Delivered', 'message' => 'Your order has been delivered! We hope you love it!'],
        'cancelled' => ['icon' => '❌', 'color' => '#ef4444', 'title' => 'Order Cancelled', 'message' => 'Your order has been cancelled. If you have questions, please contact us.'],
        // Custom Request Statuses
        'in_progress' => ['icon' => '🎨', 'color' => '#3b82f6', 'title' => 'Work in Progress', 'message' => 'Our artisans have started working on your custom request!'],
        'completed' => ['icon' => '✅', 'color' => '#10b981', 'title' => 'Request Completed', 'message' => 'Your custom product is ready! We\'ll contact you shortly for delivery.'],
    ];
    
    $info = $statusInfo[$status] ?? ['icon' => '📝', 'color' => '#6b7280', 'title' => 'Status Updated', 'message' => 'Your order status has been updated.'];
    $gradient = ($orderType === 'custom_request') ? 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)' : 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)';
    
    $productCard = '';
    if (!empty($productInfo['product_title'])) {
        $productCard = "
        <div class='product-card'>
            <h3 class='product-title'>{$productInfo['product_title']}</h3>
            " . (!empty($productInfo['product_price']) ? "<div class='product-price'>{$productInfo['product_price']}</div>" : "") . "
        </div>";
    }
    
    return "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <style>
            body { font-family: 'Arial', sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
            .container { max-width: 600px; margin: 20px auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
            .header { background: {$gradient}; color: white; padding: 30px 20px; text-align: center; }
            .header h1 { margin: 0; font-size: 28px; }
            .header p { margin: 10px 0 0 0; opacity: 0.9; }
            .content { padding: 30px 20px; }
            .status-badge { display: inline-block; background: {$info['color']}; color: white; padding: 12px 24px; border-radius: 25px; font-weight: bold; font-size: 18px; margin: 20px 0; }
            .order-id-badge { display: inline-block; background: {$gradient}; color: white; padding: 8px 16px; border-radius: 20px; font-weight: bold; font-size: 14px; margin: 10px 0; }
            .product-card { background: #f9f9f9; border-radius: 10px; padding: 20px; margin: 20px 0; }
            .product-title { font-size: 18px; font-weight: bold; color: #333; margin: 0 0 10px 0; }
            .product-price { font-size: 20px; font-weight: bold; color: {$info['color']}; margin: 10px 0; }
            .info-box { background: #f0f9ff; border-left: 4px solid {$info['color']}; padding: 15px; margin: 20px 0; border-radius: 5px; }
            .info-box p { margin: 0; color: #1e40af; }
            .footer { background: #f9f9f9; padding: 20px; text-align: center; color: #666; font-size: 14px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>{$info['icon']} {$info['title']}</h1>
                <p>Order status update notification</p>
            </div>
            
            <div class='content'>
                <p>Dear <strong>{$customer_name}</strong>,</p>
                
                <p>{$info['message']}</p>
                
                <center>
                    <div class='order-id-badge'>Order ID: {$order_id}</div>
                    <div class='status-badge'>{$info['icon']} " . ucfirst(str_replace('_', ' ', $status)) . "</div>
                </center>
                
                {$productCard}
                
                ". getStatusSpecificContent($status, $orderType) ."
                
                <div class='info-box'>
                    <p><strong>💡 Need help?</strong> Contact us via WhatsApp or email if you have any questions about your order.</p>
                </div>
            </div>
            
            <div class='footer'>
                <p><strong>Batool's Aptitude</strong><br>
                Email: batoolsaptitude@gmail.com<br>
                Lahore, Pakistan</p>
                <p style='margin-top: 15px; font-size: 12px; color: #999;'>
                    This is an automated status notification email.
                </p>
            </div>
        </div>
    </body>
    </html>
    ";
}

/**
 * Get status-specific content for email
 */
function getStatusSpecificContent($status, $orderType) {
    $content = '';
    
    switch($status) {
        case 'processing':
        case 'in_progress':
            $content = "
            <div class='info-box'>
                <p><strong>What's happening now?</strong></p>
                <ul style='margin: 10px 0; padding-left: 20px;'>
                    <li>Our team is carefully preparing your order</li>
                    <li>Quality checks are being performed</li>
                    <li>Packaging materials are being prepared</li>
                    <li>You'll receive shipping details soon</li>
                </ul>
            </div>";
            break;
            
        case 'shipped':
            $content = "
            <div class='info-box'>
                <p><strong>📦 Shipping Information</strong></p>
                <p style='margin-top: 10px;'>Your package is on its way! You can expect delivery within 3-5 business days.</p>
                <p style='margin-top: 10px; color: #666; font-size: 14px;'>We'll notify you once it arrives at your doorstep.</p>
            </div>";
            break;
            
        case 'delivered':
            $content = "
            <div class='info-box'>
                <p><strong>🎉 Thank you for your order!</strong></p>
                <p style='margin-top: 10px;'>We hope you love your purchase! If you're satisfied, we'd love to hear from you.</p>
                <p style='margin-top: 10px;'>Share your experience on social media and tag us!</p>
            </div>";
            break;
            
        case 'completed':
            $content = "
            <div class='info-box'>
                <p><strong>🎨 Your Custom Product is Ready!</strong></p>
                <p style='margin-top: 10px;'>Our artisans have completed your custom order. We'll contact you shortly via WhatsApp to arrange pickup or delivery.</p>
            </div>";
            break;
            
        case 'cancelled':
            $content = "
            <div class='info-box' style='background: #fef2f2; border-left-color: #ef4444;'>
                <p style='color: #991b1b;'><strong>Order Cancellation Details</strong></p>
                <p style='margin-top: 10px; color: #991b1b;'>If you didn't request this cancellation or have questions, please contact us immediately.</p>
                <p style='margin-top: 10px; color: #991b1b;'>Any payment made will be refunded within 5-7 business days.</p>
            </div>";
            break;
    }
    
    return $content;
}

/**
 * Plain text version for status update
 */
function getStatusUpdatePlainText($order_id, $customer_name, $status, $orderType) {
    $statusText = ucfirst(str_replace('_', ' ', $status));
    
    $text = "ORDER STATUS UPDATE\n\n";
    $text .= "Dear {$customer_name},\n\n";
    $text .= "Your order status has been updated.\n\n";
    $text .= "Order ID: {$order_id}\n";
    $text .= "New Status: {$statusText}\n\n";
    
    switch($status) {
        case 'processing':
        case 'in_progress':
            $text .= "Your order is now being processed by our team.\n";
            break;
        case 'shipped':
            $text .= "Your order has been shipped and is on its way to you!\n";
            break;
        case 'delivered':
            $text .= "Your order has been delivered. Thank you for your purchase!\n";
            break;
        case 'completed':
            $text .= "Your custom product is ready! We'll contact you shortly.\n";
            break;
        case 'cancelled':
            $text .= "Your order has been cancelled. Contact us if you have questions.\n";
            break;
    }
    
    $text .= "\nBest regards,\nBatool's Aptitude\nbatoolsaptitude@gmail.com";
    
    return $text;
}
