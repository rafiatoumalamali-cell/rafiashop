<?php
class CurrencyHelper {
    
    // Currency settings
    private static $currency = 'CFA';
    private static $symbol = 'CFA';
    private static $position = 'after'; // 'before' or 'after'
    private static $decimals = 0; // CFA typically doesn't use decimals
    private static $thousands_separator = ' ';
    private static $decimal_separator = ',';
    
    /**
     * Format amount as currency
     */
    public static function format($amount, $showSymbol = true) {
        $formatted = number_format(
            $amount,
            self::$decimals,
            self::$decimal_separator,
            self::$thousands_separator
        );
        
        if ($showSymbol) {
            if (self::$position === 'before') {
                return self::$symbol . ' ' . $formatted;
            } else {
                return $formatted . ' ' . self::$symbol;
            }
        }
        
        return $formatted;
    }
    
    /**
     * Get currency symbol
     */
    public static function symbol() {
        return self::$symbol;
    }
    
    /**
     * Get currency code
     */
    public static function currency() {
        return self::$currency;
    }
    
    /**
     * Convert from previous currency to CFA (if needed)
     * Example: if you were using INR and want to convert
     */
    public static function convertToCFA($amount, $fromCurrency = 'INR') {
        // Conversion rates (update these as needed)
        $rates = [
            'INR' => 0.89,    // 1 INR = 0.89 CFA (example rate)
            'USD' => 600,     // 1 USD ≈ 600 CFA
            'EUR' => 655.96,  // 1 EUR ≈ 655.96 CFA
            'GBP' => 760.50,  // 1 GBP ≈ 760.50 CFA
        ];
        
        if (isset($rates[$fromCurrency])) {
            return $amount * $rates[$fromCurrency];
        }
        
        return $amount; // No conversion if rate not found
    }
}
?>