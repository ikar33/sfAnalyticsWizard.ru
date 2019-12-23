<?php
date_default_timezone_set("Europe/Moscow");

require_once __DIR__ . '/AnalyticsLoader.php';
require_once __DIR__ . '/GoogleBigQuery/BigQueryProject.php';
require_once __DIR__ . '/Utils/Utils.php';
//registr-vl-rep@analytics-common-reports.iam.gserviceaccount.com
$clients_list = array();


$client = array();
$client['analytics_client_name'] = "nir-vanna.ru";
$client['analytics_UI'] = "UA-61747234-1";
$client['analytics_view_id'] = "nir-vanna.ru";
$client['table_name'] = "nir_vanna_ru";
$client['label'] = "nir_vanna_ru";
$clients_list[] = $client;


$PROJECT_ID = "sf-nirvanna-statistic";
$DATA_SET = "googl_analytics";


$analytics = new AnalyticsLoader();
$bqProject = new BigQueryProject($PROJECT_ID);
$optParamsArray = [];

$start_date = $argv[1];
$end_date = $argv[2];


/************************************************************************/

$optParams = array(
    'dimensions' => 'ga:date,ga:campaign,ga:sourceMedium,ga:keyword,ga:adContent',
    'max-results' => '10000',
    'start-index' => 1);
$metrics = 'ga:sessions,ga:users,ga:adCost,ga:goal4Completions,ga:goal3Completions,ga:goal20Completions';
$optParamsArray['table_1']['dimensions'] = $optParams;
$optParamsArray['table_1']['metrics'] = $metrics;


$optParams = array(
    'dimensions' => 'ga:date,ga:hostname,ga:sourceMedium,ga:landingPagePath',
    'max-results' => '10000000');
$metrics = 'ga:sessions,ga:users,ga:goal4Completions,ga:goal3Completions,ga:goal20Completions';
$optParamsArray['table_2']['dimensions'] = $optParams;
$optParamsArray['table_2']['metrics'] = $metrics;


/************************************************************************/


if (!isset($start_date) || !isset($end_date)) {
    $start_date = new DateTime();
    $start_date->sub(new DateInterval('P1D'));
    $end_date = new DateTime();
    $period = new DatePeriod($start_date, new DateInterval('P1D'), $end_date);
} else {
    $period = new DatePeriod(new DateTime($start_date), new DateInterval('P1D'), new DateTime($end_date));
}

foreach ($clients_list as $client) {
    print_r("Client {$client['analytics_client_name']} is processing.." . "\r\n");
    foreach ($period as $key => $value) {
        print_r("Processing Date [{$value->format('Y-m-d')}]" . "\r\n");
        foreach ($optParamsArray as $report_name => $report_options) {
            print_r("Processing report $report_name" . "\r\n");
            $report_date = $value->format('Y-m-d');
            $results = $analytics->loadData($client['analytics_client_name'], $client['analytics_UI'], $client['analytics_view_id'], $report_options['dimensions'], $report_options['metrics'], $report_date, $report_date);
            /*********************************************/

            $newTableName = (isset($client['table_name']) ? $client['table_name'] : $client['analytics_client_name']) . "_" . $report_name . "_" . $value->format('Ymd');// . "_" . 3;
            $newTableName = str_replace(' ', '_', str_replace('@', '_', str_replace('.', '_', $newTableName)));
            $newTableName = str_replace('-', '_', $newTableName);
            $newTableName = Utils::translit($newTableName);

            $schema = array('fields' => $results['FieldsSchema']);

            if ($bqProject->table_exists($DATA_SET, $newTableName)) {
                $bqProject->delete_table($DATA_SET, $newTableName);
                print_r("Table [{$newTableName}] was deleted" . "\r\n");
            }

            $labels = ["service" => 'google_analytics', 'client' => $client['label']];
            $bqProject->create_table($DATA_SET, $newTableName, $schema, $lables);
            print_r("Table [{$newTableName}] was created" . "\r\n");



            while (isset($results) && isset($results['Data']) && sizeof($results['Data']) > 0) {
                $schema = array('fields' => $results['FieldsSchema']);
                $bqProject->insert_rows_DML($DATA_SET, $newTableName, $results);
                print_r("Table [{$newTableName}] was initialized Page# " . $optParamsArray['table_1']['dimensions']['start-index'] . "\r\n");

                $report_options['dimensions']['start-index'] += 10000;
                $results = $analytics->loadData($client['analytics_client_name'], $client['analytics_UI'], $client['analytics_view_id'], $report_options['dimensions'], $report_options['metrics'], $report_date, $report_date);


            }

        }

    }
}


