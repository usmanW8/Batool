<?php
/**
 * Email Helper Functions
 * Handles sending order confirmation emails using PHPMailer
 */

// Manually include PHPMailer class files (avoiding deprecated autoload)
require_once __DIR__ . '/../../includes_php/PHPMailer/class.phpmailer.php';
require_once __DIR__ . '/../../includes_php/PHPMailer/class.smtp.php';

/**
 * Send Custom Request Order Confirmation Email
 * 
 * @param string $customerEmail Customer's email address
 * @param array $orderData Order details array
 * @return bool True on success, false on failure
 */
function sendCustomRequestConfirmation($customerEmail, $orderData) {
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
        $mail->addAddress($customerEmail, $orderData['customer_name']);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Custom Request Order Confirmation - ' . $orderData['order_id'];
        $mail->Body = getCustomRequestEmailTemplate($orderData);
        $mail->AltBody = getCustomRequestPlainText($orderData);
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('Email Error: ' . $mail->ErrorInfo);
        return false;
    }
}

/**
 * Send Portfolio Order Confirmation Email
 * 
 * @param string $customerEmail Customer's email address
 * @param array $orderData Order details array
 * @return bool True on success, false on failure
 */
function sendPortfolioOrderConfirmation($customerEmail, $orderData) {
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
        $mail->addAddress($customerEmail, $orderData['customer_name']);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Order Confirmation - ' . $orderData['order_id'];
        $mail->Body = getPortfolioOrderEmailTemplate($orderData);
        $mail->AltBody = getPortfolioOrderPlainText($orderData);
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('Email Error: ' . $mail->ErrorInfo);
        return false;
    }
}

/**
 * HTML Email Template for Custom Request Orders
 */
function getCustomRequestEmailTemplate($data) {
    $orderDate = date('F d, Y', strtotime($data['created_at'] ?? 'now'));
    
    return "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <style>
            body { font-family: 'Arial', sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
            .container { max-width: 600px; margin: 20px auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
            .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px 20px; text-align: center; }
            .header h1 { margin: 0; font-size: 28px; }
            .header p { margin: 10px 0 0 0; opacity: 0.9; }
            .content { padding: 30px 20px; }
            .order-id-badge { display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 10px 20px; border-radius: 25px; font-weight: bold; font-size: 16px; margin: 20px 0; }
            .details-box { background: #f9f9f9; border-left: 4px solid #667eea; padding: 15px; margin: 20px 0; border-radius: 5px; }
            .details-box h3 { margin-top: 0; color: #667eea; }
            .detail-row { margin: 10px 0; }
            .detail-label { font-weight: bold; color: #666; }
            .footer { background: #f9f9f9; padding: 20px; text-align: center; color: #666; font-size: 14px; }
            .button { display: inline-block; background: #667eea; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>✨ Order Confirmed!</h1>
                <p>Thank you for your custom request</p>
            </div>
            
            <div class='content'>
                <p>Dear <strong>{$data['customer_name']}</strong>,</p>
                
                <p>We're excited to confirm that we've received your custom product request! Our team will review your requirements and get back to you within 24-48 hours.</p>
                
                <div class='order-id-badge'>
                    Order ID: {$data['order_id']}
                </div>
                
                <div class='details-box'>
                    <h3>📋 Request Details</h3>
                    <div class='detail-row'>
                        <span class='detail-label'>Order Date:</span> {$orderDate}
                    </div>
                    <div class='detail-row'>
                        <span class='detail-label'>Status:</span> <span style='color: #f59e0b;'>Pending Review</span>
                    </div>
                </div>
                
                <div class='details-box'>
                    <h3>📞 Contact Information</h3>
                    <div class='detail-row'>
                        <span class='detail-label'>WhatsApp:</span> {$data['whatsapp_contact']}
                    </div>
                    <div class='detail-row'>
                        <span class='detail-label'>Email:</span> {$data['email']}
                    </div>
                    <div class='detail-row'>
                        <span class='detail-label'>Shipping Address:</span><br>
                        " . nl2br(htmlspecialchars($data['shipping_address'])) . "
                    </div>
                </div>
                
                " . (isset($data['ingredients']) && !empty($data['ingredients']) ? "
                <div class='details-box'>
                    <h3>🎨 Materials/Ingredients</h3>
                    <p>" . nl2br(htmlspecialchars($data['ingredients'])) . "</p>
                </div>
                " : "") . "
                
                " . (isset($data['additional_comments']) && !empty($data['additional_comments']) ? "
                <div class='details-box'>
                    <h3>💬 Additional Comments</h3>
                    <p>" . nl2br(htmlspecialchars($data['additional_comments'])) . "</p>
                </div>
                " : "") . "
                
                <p style='margin-top: 30px;'>
                    <strong>What's Next?</strong><br>
                    Our artisans will carefully review your request and contact you via WhatsApp or email to discuss:
                </p>
                <ul>
                    <li>Design specifications and customization options</li>
                    <li>Pricing and timeline estimates</li>
                    <li>Material selections and color choices</li>
                    <li>Shipping arrangements</li>
                </ul>
            </div>
            
            <div class='footer'>
                <p><strong>Batool's Aptitude</strong><br>
                Email: batoolsaptitude@gmail.com<br>
                Lahore, Pakistan</p>
                <p style='margin-top: 15px; font-size: 12px; color: #999;'>
                    This is an automated confirmation email. Please do not reply to this email.
                </p>
            </div>
        </div>
    </body>
    </html>
    ";
}

/**
 * HTML Email Template for Portfolio Orders
 */
function getPortfolioOrderEmailTemplate($data) {
    $orderDate = date('F d, Y', strtotime($data['created_at'] ?? 'now'));
    $baseUrl = 'http://localhost/Batool/';
    
    return "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <style>
            body { font-family: 'Arial', sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
            .container { max-width: 600px; margin: 20px auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
            .header { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 30px 20px; text-align: center; }
            .header h1 { margin: 0; font-size: 28px; }
            .header p { margin: 10px 0 0 0; opacity: 0.9; }
            .content { padding: 30px 20px; }
            .order-id-badge { display: inline-block; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 10px 20px; border-radius: 25px; font-weight: bold; font-size: 16px; margin: 20px 0; }
            .product-card { background: #f9f9f9; border-radius: 10px; padding: 20px; margin: 20px 0; }
            .product-title { font-size: 20px; font-weight: bold; color: #333; margin: 0 0 10px 0; }
            .product-price { font-size: 24px; font-weight: bold; color: #f5576c; margin: 10px 0; }
            .product-detail { margin: 8px 0; color: #666; }
            .details-box { background: #f9f9f9; border-left: 4px solid #f5576c; padding: 15px; margin: 20px 0; border-radius: 5px; }
            .details-box h3 { margin-top: 0; color: #f5576c; }
            .detail-row { margin: 10px 0; }
            .detail-label { font-weight: bold; color: #666; }
            .footer { background: #f9f9f9; padding: 20px; text-align: center; color: #666; font-size: 14px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>🎉 Order Confirmed!</h1>
                <p>Your purchase has been successfully placed</p>
            </div>
            
            <div class='content'>
                <p>Dear <strong>{$data['customer_name']}</strong>,</p>
                
                <p>Thank you for your order! We're thrilled to confirm your purchase. Your order is being processed and we'll notify you once it's ready for shipment.</p>
                
                <div class='order-id-badge'>
                    Order ID: {$data['order_id']}
                </div>
                
                <div class='product-card'>
                    <h3 class='product-title'>{$data['product_title']}</h3>
                    <div class='product-price'>{$data['product_price']}</div>
                    " . (isset($data['product_dimensions']) && !empty($data['product_dimensions']) ? "
                    <div class='product-detail'>📏 <strong>Dimensions:</strong> {$data['product_dimensions']}</div>
                    " : "") . "
                    " . (isset($data['product_materials']) && !empty($data['product_materials']) ? "
                    <div class='product-detail'>🎨 <strong>Materials:</strong> {$data['product_materials']}</div>
                    " : "") . "
                </div>
                
                <div class='details-box'>
                    <h3>📦 Order Information</h3>
                    <div class='detail-row'>
                        <span class='detail-label'>Order Date:</span> {$orderDate}
                    </div>
                    <div class='detail-row'>
                        <span class='detail-label'>Status:</span> <span style='color: #f59e0b;'>Pending Processing</span>
                    </div>
                </div>
                
                <div class='details-box'>
                    <h3>📍 Shipping Details</h3>
                    <div class='detail-row'>
                        <span class='detail-label'>Shipping Address:</span><br>
                        " . nl2br(htmlspecialchars($data['shipping_address'])) . "
                    </div>
                    <div class='detail-row'>
                        <span class='detail-label'>Contact Number:</span> {$data['whatsapp_contact']}
                    </div>
                    <div class='detail-row'>
                        <span class='detail-label'>Email:</span> {$data['email']}
                    </div>
                </div>
                
                <p style='margin-top: 30px;'>
                    <strong>What Happens Next?</strong><br>
                    We'll carefully package your artwork and contact you within 24-48 hours with:
                </p>
                <ul>
                    <li>Estimated shipping timeline</li>
                    <li>Tracking information (once shipped)</li>
                    <li>Payment confirmation details</li>
                </ul>
                
                <p style='margin-top: 20px; font-size: 14px; color: #666;'>
                    💡 <strong>Tip:</strong> Save your Order ID for future reference and tracking.
                </p>
            </div>
            
            <div class='footer'>
                <p><strong>Batool's Aptitude</strong><br>
                Email: batoolsaptitude@gmail.com<br>
                Lahore, Pakistan</p>
                <p style='margin-top: 15px; font-size: 12px; color: #999;'>
                    This is an automated confirmation email. Please do not reply to this email.
                </p>
            </div>
        </div>
    </body>
    </html>
    ";
}

/**
 * Plain text version for Custom Request
 */
function getCustomRequestPlainText($data) {
    $orderDate = date('F d, Y', strtotime($data['created_at'] ?? 'now'));
    
    $text = "ORDER CONFIRMATION\n\n";
    $text .= "Dear {$data['customer_name']},\n\n";
    $text .= "Thank you for your custom request!\n\n";
    $text .= "Order ID: {$data['order_id']}\n";
    $text .= "Order Date: {$orderDate}\n";
    $text .= "Status: Pending Review\n\n";
    $text .= "CONTACT INFORMATION:\n";
    $text .= "WhatsApp: {$data['whatsapp_contact']}\n";
    $text .= "Email: {$data['email']}\n";
    $text .= "Shipping Address: {$data['shipping_address']}\n\n";
    
    if (!empty($data['ingredients'])) {
        $text .= "Materials/Ingredients: {$data['ingredients']}\n\n";
    }
    
    if (!empty($data['additional_comments'])) {
        $text .= "Additional Comments: {$data['additional_comments']}\n\n";
    }
    
    $text .= "We'll contact you within 24-48 hours to discuss your request.\n\n";
    $text .= "Best regards,\nBatool's Aptitude\nbatoolsaptitude@gmail.com";
    
    return $text;
}

/**
 * Plain text version for Portfolio Order
 */
function getPortfolioOrderPlainText($data) {
    $orderDate = date('F d, Y', strtotime($data['created_at'] ?? 'now'));
    
    $text = "ORDER CONFIRMATION\n\n";
    $text .= "Dear {$data['customer_name']},\n\n";
    $text .= "Thank you for your order!\n\n";
    $text .= "Order ID: {$data['order_id']}\n";
    $text .= "Order Date: {$orderDate}\n\n";
    $text .= "PRODUCT DETAILS:\n";
    $text .= "Product: {$data['product_title']}\n";
    $text .= "Price: {$data['product_price']}\n";
    
    if (!empty($data['product_dimensions'])) {
        $text .= "Dimensions: {$data['product_dimensions']}\n";
    }
    
    if (!empty($data['product_materials'])) {
        $text .= "Materials: {$data['product_materials']}\n";
    }
    
    $text .= "\nSHIPPING ADDRESS:\n";
    $text .= "{$data['shipping_address']}\n\n";
    $text .= "Contact: {$data['whatsapp_contact']}\n";
    $text .= "Email: {$data['email']}\n\n";
    $text .= "We'll contact you within 24-48 hours with shipping details.\n\n";
    $text .= "Best regards,\nBatool's Aptitude\nbatoolsaptitude@gmail.com";
    
    return $text;
}

/**
 * Send Shop Order Confirmation Email
 * 
 * @param string $customerEmail Customer's email address
 * @param array $orderData Order details array
 * @return bool True on success, false on failure
 */
function sendShopOrderConfirmation($customerEmail, $orderData) {
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
        $mail->addAddress($customerEmail, $orderData['customer_name']);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Shop Order Confirmation - ' . $orderData['order_id'];
        $mail->Body = getShopOrderEmailTemplate($orderData);
        $mail->AltBody = getShopOrderPlainText($orderData);
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('Email Error: ' . $mail->ErrorInfo);
        return false;
    }
}

/**
 * HTML Email Template for Shop Orders
 */
function getShopOrderEmailTemplate($data) {
    $orderDate = date('F d, Y', strtotime($data['created_at'] ?? 'now'));
    
    return "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <style>
            body { font-family: 'Arial', sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
            .container { max-width: 600px; margin: 20px auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
            .header { background: linear-gradient(135deg, #78350F 0%, #A67B5B 100%); color: white; padding: 30px 20px; text-align: center; }
            .header h1 { margin: 0; font-size: 28px; }
            .header p { margin: 10px 0 0 0; opacity: 0.9; }
            .content { padding: 30px 20px; }
            .order-id-badge { display: inline-block; background: linear-gradient(135deg, #78350F 0%, #A67B5B 100%); color: white; padding: 10px 20px; border-radius: 25px; font-weight: bold; font-size: 16px; margin: 20px 0; }
            .product-card { background: #f9f9f9; border-radius: 10px; padding: 20px; margin: 20px 0; }
            .product-title { font-size: 20px; font-weight: bold; color: #333; margin: 0 0 10px 0; }
            .product-price { font-size: 24px; font-weight: bold; color: #78350F; margin: 10px 0; }
            .details-box { background: #f9f9f9; border-left: 4px solid #78350F; padding: 15px; margin: 20px 0; border-radius: 5px; }
            .details-box h3 { margin-top: 0; color: #78350F; }
            .detail-row { margin: 10px 0; }
            .detail-label { font-weight: bold; color: #666; }
            .footer { background: #f9f9f9; padding: 20px; text-align: center; color: #666; font-size: 14px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>🎉 Shop Order Confirmed!</h1>
                <p>We've received your order</p>
            </div>
            
            <div class='content'>
                <p>Dear <strong>{$data['customer_name']}</strong>,</p>
                
                <p>Thank you for shopping with us! Your order for <strong>{$data['product_name']}</strong> has been received and is being processed.</p>
                
                <div class='order-id-badge'>
                    Order ID: {$data['order_id']}
                </div>
                
                <div class='product-card'>
                    <h3 class='product-title'>{$data['product_name']}</h3>
                    <div class='product-price'>{$data['product_price']}</div>
                </div>
                
                <div class='details-box'>
                    <h3>📦 Order Information</h3>
                    <div class='detail-row'>
                        <span class='detail-label'>Order Date:</span> {$orderDate}
                    </div>
                </div>
                
                <div class='details-box'>
                    <h3>📍 Shipping Details</h3>
                    <div class='detail-row'>
                        <span class='detail-label'>Shipping Address:</span><br>
                        " . nl2br(htmlspecialchars($data['shipping_address'])) . "
                    </div>
                    <div class='detail-row'>
                        <span class='detail-label'>Contact Number:</span> {$data['whatsapp_contact']}
                    </div>
                    <div class='detail-row'>
                        <span class='detail-label'>Email:</span> {$data['email']}
                    </div>
                </div>
                
                <p style='margin-top: 30px;'>
                    <strong>What Happens Next?</strong><br>
                    Our team will prepare your order for shipping and notify you with tracking details within the next 24-48 hours.
                </p>
            </div>
            
            <div class='footer'>
                <p><strong>Batool's Aptitude</strong><br>
                Email: batoolsaptitude@gmail.com<br>
                Lahore, Pakistan</p>
            </div>
        </div>
    </body>
    </html>
    ";
}

/**
 * Plain text version for Shop Order
 */
function getShopOrderPlainText($data) {
    $orderDate = date('F d, Y', strtotime($data['created_at'] ?? 'now'));
    
    $text = "SHOP ORDER CONFIRMATION\n\n";
    $text .= "Dear {$data['customer_name']},\n\n";
    $text .= "Thank you for your order!\n\n";
    $text .= "Order ID: {$data['order_id']}\n";
    $text .= "Order Date: {$orderDate}\n\n";
    $text .= "PRODUCT DETAILS:\n";
    $text .= "Product: {$data['product_name']}\n";
    $text .= "Price: {$data['product_price']}\n\n";
    $text .= "SHIPPING ADDRESS:\n";
    $text .= "{$data['shipping_address']}\n\n";
    $text .= "Contact: {$data['whatsapp_contact']}\n";
    $text .= "Email: {$data['email']}\n\n";
    $text .= "We'll contact you within 24-48 hours with shipping details.\n\n";
    $text .= "Best regards,\nBatool's Aptitude\nbatoolsaptitude@gmail.com";
    
    return $text;
}
