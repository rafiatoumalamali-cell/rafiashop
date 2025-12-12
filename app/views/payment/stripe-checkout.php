<?php
// Start session to access the stripe data
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get data from session
$stripeData = $_SESSION['stripe_data'] ?? [];
$sessionId = $stripeData['sessionId'] ?? '';
$stripeKey = $stripeData['stripeKey'] ?? '';

// Clear the session data after reading
unset($_SESSION['stripe_data']);

// Debug logging
error_log("Stripe Checkout - Session ID: " . $sessionId);
error_log("Stripe Checkout - Stripe Key: " . ($stripeKey ? 'SET' : 'NOT SET'));
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment Redirect - RafiaShop</title>
    <style>
        body { font-family: Arial; text-align: center; padding: 50px; }
        .box { background: #f0f0f0; padding: 20px; margin: 10px; border-radius: 5px; }
        .error { background: #ffcccc; }
        .success { background: #ccffcc; }
        .debug { background: #e6f7ff; font-family: monospace; font-size: 12px; text-align: left; }
    </style>
</head>
<body>
    <h1>🛍️ RafiaShop Payment</h1>
    
    <div class="box debug">
        <h3>🔧 Debug Information</h3>
        <p><strong>Stripe Key:</strong> <?php echo $stripeKey ? substr($stripeKey, 0, 25) . '...' : 'NOT SET'; ?></p>
        <p><strong>Session ID:</strong> <?php echo $sessionId ? substr($sessionId, 0, 25) . '...' : 'NOT SET'; ?></p>
        <p><strong>Session Data:</strong> <?php echo !empty($_SESSION['stripe_data']) ? 'PRESENT' : 'MISSING'; ?></p>
    </div>

    <?php if ($sessionId && $stripeKey): ?>
    <div class="box success" id="redirect-box">
        <h3>✅ Ready to Redirect</h3>
        <p>Click the button below to proceed to payment:</p>
        <button onclick="redirectToStripe()" style="padding: 15px 30px; font-size: 18px; background: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer;">
            💳 Proceed to Secure Payment
        </button>
        <p><small>You will be redirected to Stripe's secure payment page.</small></p>
    </div>

    <script src="https://js.stripe.com/v3/"></script>
    <script>
        const stripeKey = '<?php echo $stripeKey; ?>';
        const sessionId = '<?php echo $sessionId; ?>';
        
        console.log('Stripe Key:', stripeKey);
        console.log('Session ID:', sessionId);
        
        function redirectToStripe() {
            console.log('Redirecting to Stripe...');
            
            const stripe = Stripe(stripeKey);
            
            stripe.redirectToCheckout({ sessionId: sessionId })
                .then(function(result) {
                    console.log('Stripe redirect result:', result);
                    if (result.error) {
                        alert('Payment Error: ' + result.error.message);
                        console.error('Stripe error:', result.error);
                    }
                })
                .catch(function(error) {
                    alert('Unexpected Error: ' + error.message);
                    console.error('Unexpected error:', error);
                });
        }
        
        // Auto-redirect after 1 second
        setTimeout(function() {
            console.log('Auto-redirecting to Stripe...');
            redirectToStripe();
        }, 1000);
    </script>

    <?php else: ?>
    <div class="box error">
        <h3>❌ Payment Setup Error</h3>
        <p>There was a problem setting up your payment session.</p>
        <p><strong>Possible issues:</strong></p>
        <ul>
            <li>Stripe API keys may be incorrect</li>
            <li>Network connection issue</li>
            <li>Session data lost</li>
        </ul>
        <button onclick="window.location.href='?page=cart'" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; margin: 5px;">
            ← Back to Cart
        </button>
        <button onclick="window.location.reload()" style="padding: 10px 20px; background: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; margin: 5px;">
            🔄 Retry Payment
        </button>
    </div>
    <?php endif; ?>
</body>
</html>