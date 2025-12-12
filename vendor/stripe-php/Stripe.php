<?php
namespace Stripe;

class Stripe {
    public static $apiKey;
    public static $apiBase = 'https://api.stripe.com';
    
    public static function setApiKey($apiKey) {
        self::$apiKey = $apiKey;
    }
}

// Create the proper namespace structure for Checkout\Session
namespace Stripe\Checkout;

class Session {
    public static function create($params) {
        return self::request('checkout/sessions', $params);
    }
    
    public static function retrieve($id) {
        return self::request("checkout/sessions/$id", null, 'GET');
    }
    
    private static function request($endpoint, $params, $method = 'POST') {
        $url = \Stripe\Stripe::$apiBase . '/v1/' . $endpoint;
        
        $headers = [
            'Authorization: Bearer ' . \Stripe\Stripe::$apiKey,
            'Content-Type: application/x-www-form-urlencoded',
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Added for testing
        
        if ($method === 'POST' && $params) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_error($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new \Exception('cURL error: ' . $error);
        }
        
        curl_close($ch);
        
        if ($httpCode !== 200) {
            throw new \Exception('Stripe API error (HTTP ' . $httpCode . '): ' . $response);
        }
        
        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('JSON decode error: ' . json_last_error_msg());
        }
        
        return $data;
    }
}
?>