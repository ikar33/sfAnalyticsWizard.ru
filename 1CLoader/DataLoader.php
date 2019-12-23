<?php
/**
 * Created by PhpStorm.
 * User: i.karabadjak
 * Date: 15.09.2018
 * Time: 17:38
 */

class DataLoader
{
    private $url;
    private $login;
    private $password;

    public function __construct($url, $login, $password)
    {
        $this->url = $url;
        $this->login = $login;
        $this->password = $password;
    }

    public function getStatisticData($date){

        $dateString = $date->format("Ymd")."000000";

        $ch = curl_init();
        $url = "http://148.251.184.102/ut_triumf/hs/UpsaleData/" . $dateString;
        $login = "webupsale";
        $password = "112233";

        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_USERPWD, "$login:$password");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	 curl_setopt($ch, CURLOPT_ENCODING ,"UTF-8");

        $server_output = curl_exec($ch);
        $server_output = str_replace("﻿","",str_replace("\r\n","",$server_output));
        $info = curl_getinfo($ch);
        curl_close($ch);

        return ['info'=>$info, "response" =>$server_output];

    }

}