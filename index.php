<?php
require_once __DIR__ . '/GoogleAnalytics/vendor/autoload.php';



session_start();

$client = new Google_Client();
$client->setAuthConfig('D:\WebServers/home/sfAnalyticsWizard.ru/www/Data/oauth2token.json');

$client->setAccessType("offline");        // offline access
$client->setIncludeGrantedScopes(true);   // incremental auth
//$client->addScope(Google_Service_Drive::DRIVE_METADATA_READONLY);
$client->addScope(['https://www.googleapis.com/auth/analytics.readonly']);

if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
    $client->setAccessToken($_SESSION['access_token']);
    $drive = new Google_Service_Drive($client);
    $files = $drive->files->listFiles(array())->getItems();
    echo json_encode($files);
} else {
    $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback.php';
    header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}