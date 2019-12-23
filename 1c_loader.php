<?php
require_once __DIR__ . '/AnalyticsLoader.php';
require_once __DIR__ . '/GoogleBigQuery/BigQueryProject.php';
require_once __DIR__ . '/Utils/Utils.php';


require_once __DIR__ . '/1CLoader/entity/DataTable.php';
require_once __DIR__ . '/1CLoader/entity/Order.php';
require_once __DIR__ . '/1CLoader/entity/OrderProduct.php';
require_once __DIR__ . '/1CLoader/DataLoader.php';

/***********************************/
function insert_bigquery_data($bqProject, $BIGQUEY_DATA_SET, $data_object, $tableName, $report_date)
{
    if ($bqProject->table_exists($BIGQUEY_DATA_SET, $tableName)) {
        $bqProject->delete_table($BIGQUEY_DATA_SET, $tableName);
        print_r("Table $tableName was successefully delteted\n\r");
    }

    $orderTableSchema = ['fields' => $data_object->getTablesSchema()];
    $bqProject->create_table($BIGQUEY_DATA_SET, $tableName, $orderTableSchema);
    print_r("Table $tableName was successefully created!\n\r");

    $tbl_dataset = $data_object->getDataSet();

    if (sizeof($tbl_dataset["Data"]) > 0) {
        $bqProject->insert_rows_DML_named($BIGQUEY_DATA_SET, $tableName, $tbl_dataset);
        print_r("Table $tableName was successefully updated from date:" . $report_date . "\n\r");
    } else {
        print_r("Table $tableName: data was not found \n\r");
    }
}

/***********************************/
$PROJECT_ID = "sf-nirvanna-statistic";
$ORDER_DATA_SET = "nirvanna_business_statistic";
$PRODUCT_DATA_SET = "nirvanna_order_products";
/********************************/
$url = "http://148.251.184.102/ut_triumf/hs/UpsaleData/";
$login = "webupsale";
$password = "112233";
/*****************************/

$start_date = $argv[1];
$end_date = $argv[2];

if (!isset($start_date) || !isset($end_date)) {
    $start_date = new DateTime();
    $start_date->sub(new DateInterval('P1D'));
    $end_date = new DateTime();
    $period = new DatePeriod($start_date, new DateInterval('P1D'), $end_date);
} else {
    $period = new DatePeriod(new DateTime($start_date), new DateInterval('P1D'), new DateTime($end_date));
}
/*****************************************/
$dataLoader = new DataLoader($url, $login, $password);
$analytics = new AnalyticsLoader();
$bqProject = new BigQueryProject($PROJECT_ID);
$optParamsArray = [];
/*******************************************/
foreach ($period as $key => $currentDate) {
    print_r("Processing Date [{$currentDate->format('Y-m-d')}]" . "\r\n");
    $statisticQuery = $dataLoader->getStatisticData($currentDate);


    $report_date = $currentDate->format('Ymd');

    if (isset($statisticQuery["info"]) && $statisticQuery["info"]['http_code'] == 200) {

        $json_array = isset($statisticQuery["response"]) && $statisticQuery["response"] != "Нет данных" ? json_decode($statisticQuery["response"]) : [];

        $ordersBQTableName = "nirvanna_orders_" . $currentDate->format('Ymd');
        $order = new Order();
        $order->loadDataSet($json_array);
        insert_bigquery_data($bqProject, $ORDER_DATA_SET, $order, $ordersBQTableName, $report_date);

        $productsBQTableName = "nirvanna_products_" . $currentDate->format('Ymd');
        $products = new OrderProduct();
        $products->loadDataSet($json_array);

        insert_bigquery_data($bqProject, $ORDER_DATA_SET, $products, $productsBQTableName, $report_date);

    }
}


die();

