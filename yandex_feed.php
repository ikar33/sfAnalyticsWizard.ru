<?php

$xml =  file_get_contents("http://www.mfcomfort.ru/bitrix/catalog_export/yandex_52171.php");
$xml = str_replace("days=\"5-8\"","days=\"2-4\"",$xml);
$xml = str_replace("days=\"2-10\"","days=\"2-4\"",$xml);
echo mb_convert_encoding($xml, "UTF-8", "auto");;
