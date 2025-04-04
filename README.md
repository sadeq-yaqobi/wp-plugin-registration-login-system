# WordPress Plugin: Registration and Login System

**Version:** 1.0.0  
**Author:** Sadeq Yaqobi  
**License:** GPL-2.0-or-later

## Description

The Registration and Login System is a custom WordPress plugin designed to provide enhanced user registration and login functionalities. It offers user-friendly interfaces and improved security features to manage user authentication seamlessly within your WordPress site.

## Features

- **Custom Registration Form:** Allows users to register with additional custom fields.
- **Custom Login Form:** Provides a tailored login experience separate from the default WordPress login.
- **Shortcode Support:** Easily embed registration and login forms on any page or post using shortcodes.
- **AJAX Validation:** Implements real-time form validation for a smoother user experience.
- **Security Enhancements:** Includes measures to protect against common security threats during authentication.

## Installation

1. **Download the Plugin:**
   - Clone the repository:
     ```bash
     git clone https://github.com/sadeq-yaqobi/wp-plugin-registration-login-system.git
     ```
   - Or [download the ZIP file](https://github.com/sadeq-yaqobi/wp-plugin-registration-login-system/archive/refs/heads/main.zip) and extract it.

2. **Upload to WordPress:**
   - Upload the extracted plugin folder to the `/wp-content/plugins/` directory of your WordPress installation.

3. **Activate the Plugin:**
   - Log in to your WordPress admin dashboard.
   - Navigate to **Plugins** > **Installed Plugins**.
   - Locate **Registration and Login System** and click **Activate**.

## Usage

- **Embedding Forms:**
  - **Registration Form:** Use the shortcode `[custom_registration_form]` to display the custom registration form on any page or post.
  - **Login Form:** Use the shortcode `[custom_login_form]` to display the custom login form.

- **Customization:**
  - Modify the templates located in the `view` directory to customize the appearance of the forms.
  - Adjust styles by editing the CSS files in the `assets` directory.

## File Structure

- **`_inc/`**: Contains PHP files for form processing and validation.
- **`assets/`**: Includes CSS and JavaScript files for styling and client-side functionality.
- **`view/`**: Holds template files for the registration and login forms.
- **`core.php`**: Core plugin functionalities and shortcode definitions.
- **`index.php`**: Initializes the plugin.
