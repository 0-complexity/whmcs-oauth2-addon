<?php

if (!defined("WHMCS"))
	die("This file cannot be accessed directly");

use Illuminate\Database\Capsule\Manager as DB;

require_once 'oauth2_users.php';
require_once 'exceptions.php';


function custom_oauth2_config() {
	$default_redirect_url = 'https://' . $_SERVER['HTTP_HOST'] . '/index.php?m=custom_oauth2';
	$config_array = array(
		'name'        => 'OAuth 2.0',
		'description' => 'This addon allows your users to login using an OAuth2 service.',
		'version'     => '1.0',
		'author'      => 'Green IT Globe',
		'language'    => 'english',
		'fields'      => array(
			'url'               => array(
				'FriendlyName' => 'OAuth service base URL',
				'Type'         => 'text',
				'Size'         => '75',
				'Description'  => 'The base URL for all OAuth2 requests. Example: https://example.com',
			), 'authorize_path' => array(
				'FriendlyName' => 'Authorize path',
				'Type'         => 'text',
				'Size'         => '75',
				'Description'  => 'The path used to authorize a user. Example: /oauth/authorize',
			), 'token_path'     => array(
				'FriendlyName' => 'Token path',
				'Type'         => 'text',
				'Size'         => '75',
				'Description'  => 'The path used to get the access token. Example: /oauth/access_token',
			), 'identity_path'  => array(
				'FriendlyName' => 'Identity path',
				'Type'         => 'text',
				'Size'         => '75',
				'Description'  => 'The path used to get user information (email, address, phone, etc). Example: /me',
			), 'jwt_path'       => array(
				'FriendlyName' => 'JSON Web Token path',
				'Type'         => 'text',
				'Size'         => '75',
				'Description'  => '(optional) Path to JSON Web Token. Example: /oauth/jwt. When set, a JWT will be
				 fetched after logging in and will be set in the session with key "jwt"',
			), 'scope'          => array(
				'FriendlyName' => 'Scope',
				'Type'         => 'text',
				'Size'         => '75',
				'Description'  => 'OAUth2 scope . Example: read,write',
			), 'client_id'      => array(
				'FriendlyName' => 'Client id',
				'Type'         => 'text',
				'Size'         => '75',
				'Description'  => 'OAuth2 client id.',
			), 'client_secret'  => array(
				'FriendlyName' => 'Client Secret',
				'Type'         => 'password',
				'Size'         => '75',
				'Description'  => 'OAuth2 client secret',
			), 'redirect_uri'   => array(
				'FriendlyName' => 'Redirect URI',
				'Type'         => 'text',
				'Size'         => '75',
				'Description'  => 'Set your OAuth2 Redirect URI to this in your OAuth2 application. You may modify this
				 if it doesn\'t match your WHMCS base url (e.g. if your base url is http://example.com/whmcs instead of
				 just https://example.com). Default: ' . $default_redirect_url,
				'Default'      => $default_redirect_url,
			),
			'provider'          => array(
				'FriendlyName' => 'OAuth provider name',
				'Type'         => 'radio',
				'Options'      => get_oauth_providers(),
				'Description'  => 'The OAuth2 provider. This will be used to parse the identity of the user.',
				'Default'      => PROVIDER_ITSYOU_ONLINE,
			),
			'admin_user'        => array(
				'FriendlyName' => 'Admin user',
				'Type'         => 'text',
				'Size'         => '75',
				'Description'  => 'The admin user which will be used to create new users',
			),
		)
	);
	return $config_array;
}

function custom_oauth2_activate() {
	try {
		DB::statement("CREATE TABLE `mod_custom_oauth2_tokens`
		(`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
		 `access_token` VARCHAR(200),
		 `refresh_token` VARCHAR(200),
		 `token_type` VARCHAR(50),
		 `expires_in` INT,
		 `scope` VARCHAR(200),
		 `created` INT,
		 `client_id` INT, 
		 `external_username` VARCHAR(50),
		  INDEX idx_mod_custom_oauth2_tokens_external_username(external_username),
		  INDEX idx_mod_custom_oauth2_tokens_client_id_scope(client_id, scope),
		  INDEX idx_mod_custom_oauth2_tokens_external_username_scope(external_username, scope)
		  )"
		);
		return array('status' => 'success', 'description' => 'OAuth 2.0 Activated');
	} catch (Exception $e) {
		logModuleCall('custom_oauth2', __FUNCTION__, null, $e->getMessage(), $e->getTraceAsString());
		return array('status' => 'error', 'description' => "Could not activate Custom OAuth2: " . $e->getMessage());
	}

}

function custom_oauth2_deactivate() {
	// Remove Custom DB table
	try {
		DB::statement("DROP TABLE `mod_custom_oauth2_tokens`");
		return array('status' => 'success', 'description' => 'OAuth 2.0 Deactivated.');
	} catch (Exception $e) {
		logModuleCall('custom_oauth2', __FUNCTION__, null, $e->getMessage(), $e->getTraceAsString());
		return array('status' => 'error', 'description' => 'Could not deactivate Custom OAuth2: ' . $e->getMessage());
	}

}


function custom_oauth2_clientarea($vars) {
	$error = null;
	try {
		if (!(isset($_GET['state']) && isset($_GET['code']))) {
			throw new BusinessException('Bad request');
		}
		$state = $_GET['state'];
		if ($state !== $_SESSION['state']) {
			logModuleCall('custom_oauth2', __FUNCTION__, 'state does not match: ' . $state . ' != ' . $_SESSION['state']);
			throw new BusinessException('Bad request');
		}
		$access_token_url = $vars['url'] . $vars['token_path'];

		$token = get_oauth_access_token($access_token_url, $vars['client_id'], $vars['client_secret'], $_GET['code'],
			$vars['redirect_uri'], $state);

		// Request user information - This only works with itsyou.online and saves us a request when the user isn't new.
		$identity = null;
		$username = isset($token['info']) && isset($token['info']['username']) ? $token['info']['username'] : null;
		if (!$username) {
			$identity = get_identity($username, $token['access_token'], $vars['url'], $vars['identity_path']);
		}
		$oauth_provider = get_oauth_provider($vars['provider']);
		$authorized = $oauth_provider->isAuthorized($vars['scope'], $token);
		if (!$authorized) {
			throw new BusinessException('You do not have permission to login.');
		}
		$client_id = get_client_id($username, $vars['admin_user']);
		$new_user = false;
		if ($client_id === false) {
			$new_user = true;
			if ($identity === null) {
				$identity = get_identity($username, $token['access_token'], $vars['url'], $vars['identity_path']);
			}
			if ($identity === false) {
				header("Location: index.php");
				exit();
			}
			$oauth_provider->setIdentity($identity);
			$client_id = create_user($token['access_token'], $vars, $oauth_provider);
		}
		else {
			update_user_password($client_id, $token['access_token'], $vars);
		}
		set_user_access_token($client_id, $username, $token['access_token'], $token['refresh_token'],
			$token['expires_in'], $token['scope'], $token['token_type']);
		$expires_in = intval($token['expires_in']);
		$expires_in = $expires_in == 0 ? 3600 : $expires_in;
		validate_login($client_id, $token['access_token'], $expires_in, $vars['admin_user']);
		$_SESSION['external_username'] = $username;
		if ($vars['jwt_path']) {
			try {
				$_SESSION['jwt'] = get_client_jwt_token($vars['url'] . $vars['jwt_path'],
					$_SESSION['oauth2_access_token'], '');
			} catch (BusinessException $e) {
				logModuleCall('custom_oauth2', __FUNCTION__, 'failed to get JWT token: ' . $e->getMessage());
			}
		}
		// Successfully logged in, remove temporary state from session.
		unset($_SESSION['state']);

		// Redirect existing users to homepage, redirect new users to their profile page so they can fill in missing info.
		if ($new_user) {
			header("Location: clientarea.php?action=details");
		}
		else {
			$redirect_url = isset($_SESSION['loginurlredirect']) ? $_SESSION['loginurlredirect'] : 'login.php';
			header(sprintf("Location: %s", $redirect_url));
		}
		exit();
	} catch (BusinessException $e) {
		$error = $e->getMessage();
		logModuleCall('custom_oauth2', __FUNCTION__, $e, null, null);
	}
	return array(
		'pagetitle'    => 'Login - error',
		'breadcrumb'   => array(
			'index.php?m=custom_oauth2' => 'error',
		),
		'templatefile' => 'authorize.tpl',
		'requirelogin' => false,
		'vars'         => array(
			'error' => $error,
		),
	);
}

function custom_oauth2_output($vars) {
	echo '<p>The OAuth2 redirect URI for this module is: ' . $vars['redirect_uri'] . '</p>';
	echo '<p>More information about this module can be found on <a href="https://github.com/gig-projects/whmcs-oauth2">GitHub</a></p>';
}