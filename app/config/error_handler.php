// Create app/config/error_handler.php
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    error_log("ERROR [$errno] $errstr in $errfile on line $errline");
    
    if (ini_get('display_errors')) {
        echo "<div class='alert alert-danger'>";
        echo "<strong>Error:</strong> $errstr<br>";
        echo "<small>File: $errfile (Line: $errline)</small>";
        echo "</div>";
    }
    
    return true;
});