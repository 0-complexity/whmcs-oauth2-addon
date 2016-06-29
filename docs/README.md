## WHMCS OAuth2 module

With this module you can easily allow your users to login with an OAuth2 service.

### Installation

- Place the file [hooks/hook_custom_oauth2.php](src/hooks/hook_custom_oauth2.php) in the directory `includes/hooks/`
- Copy the folder [custom_oauth2](src/custom_oauth2) directory to the directory `modules/addons/`

### Configuration

- Go to Addons page in your admin control panel by clicking Setup -> Addon modules
- Activate the module called `OAuth 2.0`
- Fill in every configuration parameter by clicking `configure` and filling in the fields, and pressing `Save changes` when you are done. 
- If you want your users to be able to to ONLY login using this module, modify the templates as follows (Create a new template folder for this as stated by the [WHMCS docs](http://docs.whmcs.com/Client_Area_Template_Files#Creating_a_Custom_Template))

Make the file `register.php` (located in the root of your whmcs installation) redirect to `login.php` by overwriting it with the following content (be sure to take a copy of the original file):
  
```php
<?php
    header('Location: login.php');
?>
```
Edit `includes/navbar.tpl` and `includes/sidebar.tpl` templates so the 'change password' links are removed.
See [the templates folder](src/whmcs/templates/itsyouonline/includes) and look for comments `{* This if is added *}` to see where exactly.
 
Edit the template [header.tpl](src/whmcs/templates/itsyouonline/header.tpl) so the quick login link is changed. (look for the comment `{* This part is modified *}`)


Edit the [login.tpl](src/whmcs/templates/itsyouonline/login.tpl) template so the form is removed and a link to the auth service is added instead. (look for the comment `{* This part is modified *}`)

### Troubleshooting

- If you are having any issues, try enabling debug logging by going to the page Utilities -> Logs -> Module Log and clicking `Enable Debug Logging`
- When any errors occur during the OAuth login, they will be logged here.
- Turn off debug logging when you are done by clicking `Disable Debug Logging`.

### Adding a new OAuth2 provider

- When you want to add your own OAuth provider, a new class should be added in the file [oauth2_providers.php](src/custom_oauth2/oauth2_providers.php) which implements all of the methods of the generic class `OAuthProvider`
- This class is needed to parse the identity response (containing username, email, address and other things) from the service you plan on using.