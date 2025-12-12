<?php
// Add this function
function admin_url($action, $params = []) {
    $url = '?page=admin&action=' . $action;
    
    if (!empty($params)) {
        foreach ($params as $key => $value) {
            $url .= '&' . $key . '=' . urlencode($value);
        }
    }
    
    return $url;
}

?>

