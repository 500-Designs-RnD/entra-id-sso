<?php  
function entra_id_sso_settings_page() {  
    ?>  
    <div class="wrap">  
        <h1>Entra ID SSO Settings</h1>  
        <form method="post" action="options.php">  
            <?php  
            settings_fields('entra_id_sso_settings_group');  
            do_settings_sections('entra_id_sso_settings_group');  
            ?>  
            <table class="form-table">  
                <tr valign="top">  
                    <th scope="row">Redirect URI</th>  
                    <td><input type="text" name="entra_id_sso_redirect_uri" value="<?php echo esc_attr(get_option('entra_id_sso_redirect_uri')); ?>" /></td>  
                </tr>  
  
                <tr valign="top">  
                    <th scope="row">Client ID</th>  
                    <td><input type="text" name="entra_id_sso_client_id" value="<?php echo esc_attr(get_option('entra_id_sso_client_id')); ?>" /></td>  
                </tr>  
  
                <tr valign="top">  
                    <th scope="row">Client Secret</th>  
                    <td><input type="password" name="entra_id_sso_client_secret" value="<?php echo esc_attr(get_option('entra_id_sso_client_secret')); ?>" /></td>  
                </tr>  
  
                <tr valign="top">  
                    <th scope="row">Tenant ID</th>  
                    <td><input type="text" name="entra_id_sso_tenant_id" value="<?php echo esc_attr(get_option('entra_id_sso_tenant_id')); ?>" /></td>  
                </tr>  
  
                <tr valign="top">  
                    <th scope="row">Scope</th>  
                    <td><input type="text" name="entra_id_sso_scope" value="<?php echo esc_attr(get_option('entra_id_sso_scope')); ?>" /></td>  
                </tr>  
  
                <tr valign="top">  
                    <th scope="row">Group to Role Mappings</th>  
                    <td>  
                        <textarea name="entra_id_sso_group_role_mappings" rows="10" cols="50"><?php echo esc_textarea(get_option('entra_id_sso_group_role_mappings')); ?></textarea>  
                        <p class="description">Enter group ID and role mappings in JSON format. Example: {"group_id_1": "administrator", "group_id_2": "editor"}</p>  
                    </td>  
                </tr>  
            </table>  
            <?php submit_button(); ?>  
        </form>  
    </div>  
    <?php  
}  
  
function entra_id_sso_settings() {  
    add_options_page('entra ID SSO Settings', 'Entra ID SSO', 'manage_options', 'entra-id-sso', 'entra_id_sso_settings_page');  
}  
  
add_action('admin_menu', 'entra_id_sso_settings');  
  
function entra_id_sso_register_settings() {  
    register_setting('entra_id_sso_settings_group', 'entra_id_sso_redirect_uri');  
    register_setting('entra_id_sso_settings_group', 'entra_id_sso_client_id');  
    register_setting('entra_id_sso_settings_group', 'entra_id_sso_client_secret');  
    register_setting('entra_id_sso_settings_group', 'entra_id_sso_tenant_id');  
    register_setting('entra_id_sso_settings_group', 'entra_id_sso_scope');  
    register_setting('entra_id_sso_settings_group', 'entra_id_sso_group_role_mappings');  
}  
  
add_action('admin_init', 'entra_id_sso_register_settings');  
?>