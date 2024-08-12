<?php  
function intra_id_sso_login_url() {  
    $client_id = get_option('intra_id_sso_client_id');  
    $redirect_uri = urlencode(get_option('intra_id_sso_redirect_uri'));  
    $scope = get_option('intra_id_sso_scope');  
    $tenant_id = get_option('intra_id_sso_tenant_id');  
    $state = wp_create_nonce('intra_id_sso_nonce');  
  
    $url = "https://login.microsoftonline.com/$tenant_id/oauth2/v2.0/authorize?client_id=$client_id&response_type=code&redirect_uri=$redirect_uri&response_mode=query&scope=$scope&state=$state";  
  
    return $url;  
}  
  
function intra_id_sso_handle_callback() {  
    if (isset($_GET['code']) && isset($_GET['state']) && wp_verify_nonce($_GET['state'], 'intra_id_sso_nonce')) {  
        $code = sanitize_text_field($_GET['code']);  
        $redirect_uri = get_option('intra_id_sso_redirect_uri');  
        $client_id = get_option('intra_id_sso_client_id');  
        $client_secret = get_option('intra_id_sso_client_secret');  
        $tenant_id = get_option('intra_id_sso_tenant_id');  
  
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
  
            if ($user_info) {  
                $user_email = $user_info['email'];  
                $user_name = $user_info['name'];  
                $user_groups = $user_info['groups'];  
  
                $role = get_role_from_group($user_groups);  
  
                $user = get_user_by('email', $user_email);  
  
                if (!$user) {  
                    $user_id = wp_create_user($user_name, wp_generate_password(), $user_email);  
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
  
                wp_set_current_user($user->ID);  
                wp_set_auth_cookie($user->ID);  
                wp_redirect(home_url());  
                exit;  
            } else {  
                wp_die('Invalid id_token');  
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
    $group_role_mappings = json_decode(get_option('intra_id_sso_group_role_mappings'), true);  
  
    if (is_array($group_role_mappings)) {  
        foreach ($groups as $group) {  
            if (array_key_exists($group, $group_role_mappings)) {  
                return $group_role_mappings[$group];  
            }  
        }  
    }  
  
    return 'subscriber'; // Default role if no match found  
}  
  
add_action('init', 'intra_id_sso_handle_callback');  
  
function intra_id_sso_login_button() {  
    $login_url = intra_id_sso_login_url();  
    echo '<a href="' . esc_url($login_url) . '" class="button button-primary">Login with Intra ID</a>';  
}  
  
add_action('login_form', 'intra_id_sso_login_button');  
?>