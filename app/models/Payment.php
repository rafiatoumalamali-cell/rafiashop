<?php
// app/models/Payment.php - MINIMAL WORKING VERSION

class Payment {
    // ✅ YOUR STRIPE TEST KEYS
    
    public static function createStripeSession($orderId, $amount, $currency = 'usd') {
        error_log("=== SIMPLE STRIPE CALL ===");
        error_log("Order: #$orderId, Amount: $$amount");
        
        try {
            // SIMPLE DIRECT CALL TO STRIPE API
            $ch = curl_init();
            
            // Stripe API endpoint
            $url = 'https://api.stripe.com/v1/checkout/sessions';
            
            // Build parameters
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
            $host = $_SERVER['HTTP_HOST'];
            $scriptPath = dirname($_SERVER['SCRIPT_NAME']);
            $baseUrl = rtrim($protocol . $host . $scriptPath, '/');
            
            $params = [
                'payment_method_types[]' => 'card',
                'line_items[0][price_data][currency]' => $currency,
                'line_items[0][price_data][product_data][name]' => 'Order #' . $orderId,
                'line_items[0][price_data][unit_amount]' => round($amount * 100),
                'line_items[0][quantity]' => 1,
                'mode' => 'payment',
                'success_url' => $baseUrl . '/?page=checkout&action=payment-success&session_id={CHECKOUT_SESSION_ID}&order_id=' . $orderId,
                'cancel_url' => $baseUrl . '/?page=checkout&action=payment-cancel&order_id=' . $orderId,
                'metadata[order_id]' => $orderId
            ];
            
            // Build query string
            $postFields = http_build_query($params);
            
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $postFields,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . self::$stripeSecretKey,
                    'Content-Type: application/x-www-form-urlencoded',
                ],
                CURLOPT_SSL_VERIFYPEER => false, // For testing only
            ]);
            
            $response = curl_exec($ch);
            $error = curl_error($ch);
            curl_close($ch);
            
            if ($error) {
                error_log("CURL Error: " . $error);
                throw new Exception('Payment connection failed');
            }
            
            $data = json_decode($response, true);
            
            if (isset($data['error'])) {
                error_log("Stripe API Error: " . print_r($data['error'], true));
                throw new Exception('Payment error: ' . ($data['error']['message'] ?? 'Unknown'));
            }
            
            if (!isset($data['id'])) {
                error_log("No session ID in response: " . $response);
                throw new Exception('Payment setup failed');
            }
            
            error_log("✅ Stripe session created: " . $data['id']);
            return $data['id'];
            
        } catch (Exception $e) {
            error_log("Payment error: " . $e->getMessage());
            throw new Exception('Payment failed: ' . $e->getMessage());
        }
    }
    
    public static function verifyStripePayment($sessionId) {
        error_log("Verifying payment: " . $sessionId);
        
        try {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => 'https://api.stripe.com/v1/checkout/sessions/' . $sessionId,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . self::$stripeSecretKey,
                ],
                CURLOPT_SSL_VERIFYPEER => false,
            ]);
            
            $response = curl_exec($ch);
            curl_close($ch);
            
            $data = json_decode($response, true);
            
            if (isset($data['payment_status']) && $data['payment_status'] === 'paid') {
                error_log("✅ Payment verified as PAID");
                return true;
            }
            
            error_log("Payment not paid: " . ($data['payment_status'] ?? 'unknown'));
            return false;
            
        } catch (Exception $e) {
            error_log("Verification error: " . $e->getMessage());
            return false;
        }
    }
    
    public static function getPublishableKey() {
        return self::$stripePublishableKey;
    }
}
?>