<?php
// app/controllers/PaymentController.php
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Cart.php';

class PaymentController {
    
    public static function paymentSuccess() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $sessionId = $_GET['session_id'] ?? '';
        $orderId = $_GET['order_id'] ?? 0;
        
        // Debug logging
        error_log("Payment Success - Session ID: " . $sessionId);
        error_log("Payment Success - Order ID: " . $orderId);
        
        if (Payment::verifyStripePayment($sessionId)) {
            // Payment successful
            Order::updateStatus($orderId, 'confirmed');
            
            // 🔥 HYBRID SYSTEM: Reduce stock immediately for Stripe payments
            require_once __DIR__ . '/../models/Inventory.php';
            $inventory = new Inventory();
            $stockReduced = $inventory->reduceStockForOrder($orderId);
            
            if (!$stockReduced) {
                error_log("WARNING: Stock reduction failed for order #" . $orderId);
                // Store warning for admin
                $_SESSION['stock_warning'] = "Stock reduction may need manual review for order #$orderId";
            }
            
            Cart::clear();
            unset($_SESSION['pending_order']);
            
            // Redirect to order confirmation
            header('Location: ?page=order-confirmation&id=' . $orderId . '&payment=stripe');
            exit;
        } else {
            // Payment verification failed
            error_log("Payment verification failed for session: " . $sessionId);
            header('Location: ?page=payment-failed&order_id=' . $orderId);
            exit;
        }
    }
    
    public static function paymentCancel() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $orderId = $_GET['order_id'] ?? 0;
        
        // If payment cancelled, release any reserved stock (for COD scenarios)
        if ($orderId) {
            require_once __DIR__ . '/../models/Inventory.php';
            $inventory = new Inventory();
            $inventory->releaseReservedStock($orderId);
        }
        
        $_SESSION['error'] = 'Payment was cancelled. Please try again.';
        header('Location: ?page=cart');
        exit;
    }
    
    public static function paymentFailed() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $orderId = $_GET['order_id'] ?? 0;
        
        // Release reserved stock if payment failed
        if ($orderId) {
            require_once __DIR__ . '/../models/Inventory.php';
            $inventory = new Inventory();
            $inventory->releaseReservedStock($orderId);
        }
        
        $_SESSION['error'] = 'Payment failed. Please try again or contact support.';
        header('Location: ?page=checkout');
        exit;
    }
    
    public static function stripeWebhook() {
        // Handle Stripe webhook events
        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $endpoint_secret = 'your_webhook_secret'; // Set in Stripe dashboard
        
        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
            
            switch ($event->type) {
                case 'payment_intent.succeeded':
                    $paymentIntent = $event->data->object;
                    // Handle successful payment
                    break;
                    
                case 'payment_intent.payment_failed':
                    $paymentIntent = $event->data->object;
                    // Handle failed payment
                    break;
            }
            
            http_response_code(200);
            
        } catch(\UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(400);
            exit();
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            http_response_code(400);
            exit();
        }
    }
}
?>