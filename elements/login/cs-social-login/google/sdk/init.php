<?php
echo '9999999999999';
die;
require_once plugin_dir_url( __FILE__ ) . 'apiClient.php';
require_once plugin_dir_url( __FILE__ ) . 'contrib/apiOauth2Service.php';
global $foodbakery_theme_options;

$client = new apiClient();
$client->setClientId($foodbakery_theme_options['foodbakery_google_client_id']);
$client->setClientSecret($foodbakery_theme_options['foodbakery_google_client_secret']);
$client->setDeveloperKey($foodbakery_theme_options['foodbakery_google_api_key']);
$client->setRedirectUri(foodbakery_google_login_url());
$client->setApprovalPrompt('auto');

$oauth2 = new apiOauth2Service($client);
