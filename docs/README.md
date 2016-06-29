## WHMCS OAuth 2.0 Add-on Module

With this WHMCS add-on module you can easily allow your users to login with an OAuth 2.0 service such as ItsYou.online.

All source code is available from GitHub: https://github.com/0-complexity/whmcs-oauth2-addon


### Installation

- Copy the file `includes/hooks/hook_custom_oauth2.php` to the directory `includes/hooks/`
- Copy the directory `modules/addons/custom_oauth2` to the directory `modules/addons/`


### Configuration

- Go to Addons page in your admin control panel by clicking **Setup** | **Addon Modules**
- Activate the module called **OAuth 2.0** and then cick **Configure**
- Configure the add-on by filling out all fields, and click **Save Changes** 

If you want your users to be able to to ONLY login using this module:

- Create a new template folder as explained by the [WHMCS Documentation](http://docs.whmcs.com/Client_Area_Template_Files#Creating_a_Custom_Template)
- Make the file `register.php` (located in the root of your WHMCS installation) redirect to `login.php` by overwriting it with the following content (be sure to take a copy of the original file):
  
    ```php
    <?php
        header('Location: login.php');
    ?>
    ```

- Edit `includes/navbar.tpl` and `includes/sidebar.tpl` template files so the 'change password' links are removed

  - See the `templates/itsyouonline/includes` directory and look for comments `{* This if is added *}` to see where exactly
 
- Edit the template file `templates/itsyouonline/header.tpl` so the quick login link is changed
    
    - Look for the comment `{* This part is modified *}`
    
- Edit the template file `templates/itsyouonline/login.tpl` so the form is removed and a link to the auth service is added instead

    - Look for the comment `{* This part is modified *}`


### Troubleshooting

- If you are having any issues, try enabling debug logging by goto **Logs** | **Module Log** on the **Utilities** tab, and clicking **Enable Debug Logging**
- When any errors occurs during the OAuth login, they will be logged here
- Turn off debug logging when you are done by clicking **Disable Debug Logging**


### Adding a new OAuth2 provider

- When you want to add your own OAuth provider, a new class should be added in the file `custom_oauth2/oauth2_providers.php` which implements all of the methods of the generic class `OAuthProvider`
- This class is needed to parse the identity response (containing username, email, address and other things) from the service you plan on using