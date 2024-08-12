<?php  
function entra_id_sso_login_url() {  
    $client_id = get_option('entra_id_sso_client_id');  
    $redirect_uri = urlencode(get_option('entra_id_sso_redirect_uri'));  
    $scope = get_option('entra_id_sso_scope');  
    $tenant_id = get_option('entra_id_sso_tenant_id');  
    $state = wp_create_nonce('entra_id_sso_nonce');  
  
    $url = "https://login.microsoftonline.com/$tenant_id/oauth2/v2.0/authorize?client_id=$client_id&response_type=code&redirect_uri=$redirect_uri&response_mode=query&scope=$scope&state=$state";  
  
    return $url;  
}  
  
function entra_id_sso_handle_callback() {  
    if (isset($_GET['code']) && isset($_GET['state']) && wp_verify_nonce($_GET['state'], 'entra_id_sso_nonce')) {  
        $code = sanitize_text_field($_GET['code']);  
        $redirect_uri = get_option('entra_id_sso_redirect_uri');  
        $client_id = get_option('entra_id_sso_client_id');  
        $client_secret = get_option('entra_id_sso_client_secret');  
        $tenant_id = get_option('entra_id_sso_tenant_id');  
  
        $token_url = "https://login.microsoftonline.com/$tenant_id/oauth2/v2.0/token";  
  
        $response = wp_remote_post($token_url, [  
            'body' => [  
                'grant_type' => 'authorization_code',  
                'code' => $code,  
                'redirect_uri' => $redirect_uri,  
                'client_id' => $client_id,  
                'client_secret' => $client_secret  
            ]  
        ]);  
  
        if (is_wp_error($response)) {  
            wp_die('OAuth token request failed: ' . $response->get_error_message());  
        }  
  
        $body = wp_remote_retrieve_body($response);  
        $data = json_decode($body, true);  
  
        if (isset($data['id_token'])) {  
            $id_token = $data['id_token'];  
            $user_info = decode_id_token($id_token);  
  
            if ($user_info && isset($user_info['preferred_username'])) {  
                $user_email = $user_info['preferred_username'];  
                $user_name = sanitize_user($user_info['name'], true);  
                $user_groups = $user_info['groups'];  
  
                $role = get_role_from_group($user_groups);  
  
                // Ensure the user exists or create a new user  
                $user = get_user_by('email', $user_email);  
  
                if (!$user) {  
                    // Check if the username already exists  
                    if (username_exists($user_name)) {  
                        $user_name .= '_' . uniqid();  
                    }  
  
                    $user_id = wp_create_user($user_name, wp_generate_password(), $user_email);  
                    if (is_wp_error($user_id)) {  
                        wp_die('Failed to create new user: ' . $user_id->get_error_message());  
                    }  
                    $user = get_user_by('id', $user_id);  
                    wp_update_user([  
                        'ID' => $user_id,  
                        'role' => $role  
                    ]);  
                } else {  
                    wp_update_user([  
                        'ID' => $user->ID,  
                        'role' => $role  
                    ]);  
                }  
  
                // Log the user in  
                wp_set_current_user($user->ID);  
                wp_set_auth_cookie($user->ID);  
                do_action('wp_login', $user->user_login, $user);  
  
                wp_redirect(admin_url());  
                exit;  
            } else {  
                wp_die('Invalid id_token or preferred_username not found');  
            }  
        } else {  
            wp_die('OAuth token retrieval failed. Response: ' . print_r($data, true));  
        }  
    }  
}  
  
function decode_id_token($id_token) {  
    $token_parts = explode('.', $id_token);  
  
    if (count($token_parts) === 3) {  
        $token_payload = base64_decode(strtr($token_parts[1], '-_', '+/'));  
  
        return json_decode($token_payload, true);  
    }  
  
    return false;  
}  
  
function get_role_from_group($groups) {  
    $group_role_mappings = json_decode(get_option('entra_id_sso_group_role_mappings'), true);  
  
    if (is_array($group_role_mappings)) {  
        foreach ($groups as $group) {  
            if (array_key_exists($group, $group_role_mappings)) {  
                return $group_role_mappings[$group];  
            }  
        }  
    }  
  
    return 'subscriber'; // Default role if no match found  
}  
  
add_action('init', 'entra_id_sso_handle_callback');  
  
function entra_id_sso_login_button() {  
    $login_url = entra_id_sso_login_url();  
    echo '<p class="entra-id-login-button"><a href="' . esc_url($login_url) . '" class="button button-primary button-large">Login with Microsoft SSO</a></p>';  
}  
  
add_action('login_form', 'entra_id_sso_login_button');  

// Add custom CSS to style the login button  
function entra_id_sso_login_styles() {  
    echo '<style>  
        .entra-id-login-button {  
            margin-bottom: 20px;  
            text-align: center;  
        }  
        .entra-id-login-button .button {  
            width: 100%;  
            text-align: center;  
        }         
        .login form {
        display:flex;
        flex-direction:column;
        align-items:center;
        }

        .login form .forgetmenot {
         margin: 10px 0;
         }
            
    </style>';  
}
add_action('login_head', 'entra_id_sso_login_styles');

?>