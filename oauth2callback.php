
<?php
require_once __DIR__ . '/GoogleAnalytics/vendor/autoload.php';

session_start();

$client = new Google_Client();
$client->setAuthConfig('D:\WebServers/home/sfAnalyticsWizard.ru/www/Data/oauth2token.json');
$client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback.php');
$client->addScope(['https://www.googleapis.com/auth/analytics.readonly']);
//$client->addScope(Google_Service_Drive::DRIVE_METADATA_READONLY);

if (! isset($_GET['code'])) {
    $auth_url = $client->createAuthUrl();
    header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
} else {
    $client->authenticate($_GET['code']);
    $_SESSION['access_token'] = $client->getAccessToken();
    $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/';
    header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}

$a=1;
die();