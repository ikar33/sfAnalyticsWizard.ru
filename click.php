<?php

$url = "https://api.sendpulse.com/oauth/access_token";
$data = array(
    'grant_type' => 'client_credentials',
    'client_id' => "45d6120abd5f277f60e81bcc10aa710b",
    'client_secret' => "133dc21877a10e99c46e2d467eb11037",
);
$ch = curl_init();
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$server_output = curl_exec($ch);
$info = curl_getinfo($ch);
curl_close($ch);
