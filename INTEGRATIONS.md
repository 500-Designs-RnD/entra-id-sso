# Integrations

This document outlines the external service integrations used by the Entra ID SSO WordPress plugin.

## Microsoft Entra ID (formerly Azure AD)

### Integration Details
- **Service**: Microsoft Entra ID Identity Provider
- **Purpose**: Single Sign-On (SSO) authentication for WordPress

### Authentication Methods
- **Protocol**: OAuth 2.0 with OpenID Connect
- **Flow Type**: Authorization Code Flow
- **Token Type**: JWT (JSON Web Token)

### Endpoints
- **Authorization Endpoint**: `https://login.microsoftonline.com/{tenant_id}/oauth2/v2.0/authorize`
- **Token Endpoint**: `https://login.microsoftonline.com/{tenant_id}/oauth2/v2.0/token`

### Authentication Parameters
- **Client ID**: Application identifier registered in Entra ID
- **Client Secret**: Secret key for application authentication
- **Redirect URI**: Callback URL where authorization code is sent
- **Scope**: Required permissions (typically `openid profile email`)
- **Tenant ID**: Directory ID for the Entra ID tenant

### Data Exchange
1. User is redirected to Microsoft's login page
2. After successful authentication, authorization code is returned to WordPress
3. WordPress exchanges code for tokens using client credentials
4. ID token is decoded to extract user information including:
   - Email address (`preferred_username`)
   - Display name (`name`)
   - Group memberships (`groups`)

### User Provisioning
- Automatic user creation in WordPress on first login
- Role assignment based on group membership mappings
- Group-to-role mappings are configured in the plugin settings

### Security Features
- State parameter with nonce verification to prevent CSRF attacks
- Token validation and secure handling

## Security To-Do Items

### Digital Certificate Usage

#### Required Improvements
1. **JWT Signature Validation**
   - **Issue**: The current plugin only decodes JWT tokens without cryptographically validating their signatures
   - **Solution**: Add validation using Microsoft's public certificates from their JWKS endpoint
   - **Certificate Type**: Microsoft's signing certificates (you don't need to create these)
   - **Purpose**: Verify token authenticity and prevent token forgery
   - **Implementation**: Retrieve Microsoft's certificates and use them to validate JWT signatures

#### Already Handled
1. **TLS/SSL Certificate Verification**
   - The plugin uses WordPress's HTTP API which relies on the system's certificate store
   - HTTPS connections to Microsoft endpoints are automatically secured
   - No additional certificates need to be managed manually

#### Optional Enhancements
1. **Certificate Pinning**
   - **Purpose**: Add extra protection against man-in-the-middle attacks
   - **Implementation**: Configure the plugin to verify Microsoft's specific certificate fingerprints
   - **Note**: Uses existing certificates, no new certificates needed

2. **Client Certificate Authentication**
   - **Purpose**: Optional additional layer to authenticate WordPress to Microsoft
   - **Certificate Type**: Custom client certificate (would need to be created)
   - **Note**: Not required for standard OAuth 2.0/OIDC implementation