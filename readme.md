```markdown
# Entra ID SSO

**Entra ID SSO** is a WordPress plugin that enables Single Sign-On (SSO) using OAuth with Entra ID. It allows users to sign in to your WordPress site using their Entra ID credentials.

## Features

- OAuth-based Single Sign-On with Entra ID
- Automatic user creation on first login
- Role assignment based on user group
- Configurable group-to-role mappings via the settings page

## Installation

1. Download the plugin and upload it to your WordPress site's `wp-content/plugins` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Configure the plugin settings under `Settings -> Entra ID SSO`.

## Configuration

1. **Redirect URI:** Enter the redirect URI for your application.
2. **Client ID:** Enter the client ID from your Entra ID OAuth application.
3. **Client Secret:** Enter the client secret from your Entra ID OAuth application.
4. **Tenant ID:** Enter the tenant ID associated with your Entra ID OAuth application.
5. **Scope:** Enter the scopes required for your application, e.g., `openid profile email`.
6. **Group to Role Mappings:** Enter the group-to-role mappings in JSON format. Example:
    ```json
    {
        "group-id": "administrator",
        "another-group-id": "editor"
    }
    ```

## Usage

1. Navigate to the WordPress login page.
2. Click the "Login with Entra ID" button.
3. Follow the OAuth flow to log in using your Entra ID credentials.
4. On successful login, you will be authenticated and assigned a role based on your group.

## Example

Here's an example of how to configure the group-to-role mappings in the settings page:

```json
{
    "group-id": "administrator",
    "another-group-id": "editor"
}
```

## Hooks and Filters

- **Actions:**
  - `init` - Handles the OAuth callback and authentication.

- **Filters:**
  - `login_form` - Adds the "Login with Entra ID" button to the WordPress login form.

## Troubleshooting

- Ensure that the Redirect URI in the plugin settings matches the one configured in your Entra ID OAuth application.
- Verify that the Client ID, Client Secret, Tenant ID, and Scope are correctly entered in the plugin settings.
- Check the JSON format for group-to-role mappings to ensure it is correctly structured.

## Contributing

1. Fork the repository.
2. Create a new branch for your feature or bugfix.
3. Commit your changes.
4. Push to the branch.
5. Create a new Pull Request.

## License

This plugin is licensed under the MIT License. See the `LICENSE` file for more details.

## Acknowledgements

Special thanks to the contributors and the open-source community for their support and contributions.

## Contact

For support or inquiries, please contact [Your Name] at [your.email@example.com].

```

Feel free to customize the `README.md` file as needed to fit your specific requirements.# entra-id-sso
# entra-id-sso
