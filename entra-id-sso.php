<?php  
/*  
Plugin Name: Entra ID SSO  
Description: Enables OAuth-based SSO via Entra ID  
Version: 1.0  
Author: 500Designs (Aurora)
*/  

// Prevent direct access to the file  
if (!defined('ABSPATH')) {  
    exit;  
}  

// Include necessary files  
require_once plugin_dir_path(__FILE__) . 'includes/settings.php';  
require_once plugin_dir_path(__FILE__) . 'includes/oauth.php';  
?>