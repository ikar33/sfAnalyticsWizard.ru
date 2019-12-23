<?php

require 'GoogleApi/vendor/autoload.php';

use Google\Cloud\Core\ServiceBuilder;
use Google\Cloud\BigQuery\BigQueryClient;
use Google\Cloud\Core\ExponentialBackoff;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\Credentials\UserRefreshCredentials;

require_once "/../Utils/Logger.php";

chdir(dirname(__FILE__));
$token_path = realpath("../Data/token.json");
putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $token_path);


class BigQueryProject
{

    private $bigQuery;

    function __construct($projectId)
    {
        try {
            $this->bigQuery = new BigQueryClient([
                'projectId' => $projectId,
            ]);
        } catch (Exception $ex) {
            $message = "[Epool Direct Reports] " . get_class($this) . " constructor Error: " . $ex->getMessage();
            echo $message;
            //Logger::getLogger()->log($message);

        }

    }

    public function clearTable($dataSet, $tableName, $startDate = false)
    {
        try {
            if (!$startDate) {
                $query = "delete from $dataSet.$tableName where true";
            } else {
                $query = "delete from $dataSet.$tableName where Date >= cast('$startDate' as date)";
            }
            //Logger::getLogger()->log("[Epool Direct Reports] strting query: delete from $dataSet.$tableName where Date >= cast('$startDate' as date)");


            $options = ['useLegacySql' => true];

            $queryConfig = $this->bigQuery->query($query, $options);
            $queryResults = $this->bigQuery->runQuery($queryConfig);
            $isComplete = $queryResults->isComplete();
            while (!$isComplete) {
                sleep(1); // small delay between requests
                $queryResults->reload();
                $isComplete = $queryResults->isComplete();
            }
            if ($isComplete) {
                Logger::getLogger()->log("[Epool Direct Reports] the table $dataSet.$tableName cleared from date: $startDate");
            }
        } catch (Exception $ex) {
            $message = "[Epool Direct Reports] " . get_class($this) . " " . __FUNCTION__ . "  Error: " . $ex->getMessage();
            Logger::getLogger()->log($message);
            Logger::getLogger()->log($ex->getTraceAsString());
            echo $message;
        }
    }

    public function insert_rows_DML_named($dataSet, $tableName, $dataSource, $part = 100)
    {
        try {
            $ind_ = 0;

            $fields_list = [];
            foreach ($dataSource["FieldsSchema"] as $field) {
                $fields_list[] = $field['name'];
            }


            $query_table_name = "INSERT $dataSet.$tableName(" . implode(",", $fields_list) . ") VALUES";

            foreach (array_chunk($dataSource["Data"], $part, true) as $ind=>$dataResult) {
                $query = $query_table_name;
                echo 'part ' . $ind;
                //if($ind < 3) continue;
                $i = 0;
                $len = count($dataResult);
                foreach ($dataResult as $row) {
                    $query .= "(";
                    $columns_count = count($row);

                    $index = 0;
                    foreach ($row as $columnName => $columnValue) {
                        $value = $this->fields_value_DML_wraper($columnValue, $dataSource["FieldsSchema"][$columnName]['type']);
                        $query .= $value;
                        if ($index < $columns_count - 1) {
                            $query .= ",";
                        }
                        $index++;
                    }

                    $query .= ")";
                    if ($i++ != $len - 1) {
                        $query .= ",";
                    }
                }
                //echo("index=".$ind_); if($ind_++ < 177) continue;print_r($query."\n\r");
                $options = ['useLegacySql' => true];

                $queryConfig = $this->bigQuery->query($query, $options);
                $queryResults = $this->bigQuery->runQuery($queryConfig);
                $isComplete = $queryResults->isComplete();
                print_r("insert_rows_DML Complete state:[" . $isComplete . "]");
                sleep(2);
                while (!$isComplete) {
                    print_r("insert_rows_DML delay......");
                    sleep(100);
                    $queryResults->reload();
                    $isComplete = $queryResults->isComplete();
                    print_r("insert_rows_DML Complete state:[" . $isComplete . "]");
                }

                if ($isComplete) {
                    Logger::getLogger()->log("[Epool Direct Reports][insert_rows_DML] the table $dataSet.$tableName was updated");
                }
            }
        }catch (Exception $ex) {
            $message = "[Epool Direct Reports] " . get_class($this) . " " . __FUNCTION__ . "  Error: " . $ex->getMessage();
            Logger::getLogger()->log($message);
            echo $message;
            die();
        }
    }

    public function insert_rows_DML($dataSet, $tableName, $dataSource)
    {
        try {

            $fields_list = [];
            foreach ($dataSource["FieldsSchema"] as $field) {
                $fields_list[] = $field['name'];
            }

            $query_table_name = "INSERT $dataSet.$tableName(" . implode(",", $fields_list) . ") VALUES";

            foreach (array_chunk($dataSource["Data"], 500, true) as $ind=>$dataResult) {
                $query = $query_table_name;
                echo 'part ' . $ind;
                $i = 0;
                $len = count($dataResult);
                foreach ($dataResult as $row) {
                    $query .= "(";
                    $columns_count = count($row);

                    foreach ($row as $index => $columnValue) {
                        $value = $this->fields_value_DML_wraper($columnValue, $dataSource["FieldsSchema"][$index]['type']);
                        $query .= $value;
                        if ($index < $columns_count - 1) {
                            $query .= ",";
                        }
                    }

                    $query .= ")";
                    if ($i++ != $len - 1) {
                        $query .= ",";
                    }
                }

                $options = ['useLegacySql' => true];

                $queryConfig = $this->bigQuery->query($query, $options);
                $queryResults = $this->bigQuery->runQuery($queryConfig);
                $isComplete = $queryResults->isComplete();
                print_r("insert_rows_DML Complete state:[" . $isComplete . "]");
                while (!$isComplete) {
                    print_r("insert_rows_DML delay......");
                    sleep(100);
                    $queryResults->reload();
                    $isComplete = $queryResults->isComplete();
                    print_r("insert_rows_DML Complete state:[" . $isComplete . "]");
                }

                if ($isComplete) {
                    Logger::getLogger()->log("[Epool Direct Reports][insert_rows_DML] the table $dataSet.$tableName was updated");
                }
            }
        }catch (Exception $ex) {
            $message = "[Epool Direct Reports] " . get_class($this) . " " . __FUNCTION__ . "  Error: " . $ex->getMessage();
            Logger::getLogger()->log($message);
            echo $message;
        }
    }

    public function insertRows($dataSet, $table, $rows)
    {
        try {
            $sourceTable = $this->bigQuery->dataset($dataSet)->table($table);
            $insertResponse = $sourceTable->insertRows($rows);

            echo "responce " . $insertResponse->isSuccessful();
            /*        if (!$insertResponse->isSuccessful()) {
                        $row = $insertResponse->failedRows()[0];

                        print_r($row['rowData']);

                        foreach ($row['errors'] as $error) {
                            echo $error['reason'] . ': ' . $error['message'] . PHP_EOL;
                        }
                    }    */

        } catch (Exception $ex) {
            Logger::getLogger()->log("[Epool Direct Reports] " . get_class($this) . " " . __FUNCTION__ . "  Error: " . $ex->getMessage());
        }
    }

    function delete_table($datasetId, $tableId)
    {
        $dataset = $this->bigQuery->dataset($datasetId);
        $table = $dataset->table($tableId);
        $table->delete();
    }

    function table_exists($datasetId, $tableId)
    {
        $dataset = $this->bigQuery->dataset($datasetId);
        return $dataset->table($tableId)->exists();
    }

    function create_table($dataSetId, $tableId, $schema, $labels = [])
    {
        $dataSet = $this->bigQuery->dataset($dataSetId);
        $options = [];
        if(isset($schema) && sizeof($schema) > 0){
            $options['schema'] = $schema;
        }
        if(isset($labels) && sizeof($labels) > 0){
            $options['labels'] = $labels;
        }

        $table = $dataSet->createTable($tableId, $options);
        return $table;
    }

    function fields_value_DML_wraper($value, $type)
    {
        if ("STRING" == $type) {
            $str_value = "'" . str_replace("'", '', addslashes($value)) . "'";
            $str_value = str_replace('\\','\\\\',$str_value);
            return $str_value;
        }else if("DATE" == $type){
            if ($value instanceof DateTime) {
                return "cast('" . date_format($value, "Y-m-d") . "' as date)";
            }else{
                $date = DateTime::createFromFormat('Ymd', $value);
                return "cast('" . date_format($date, "Y-m-d") . "' as date)";
            }
        }else if("BOOLEAN" == $type){
            if(!isset($value) &&  $value == ""){
                return "false";
            }else if(strtoupper($value) == 'TRUE'){
                return "true";
            }else{
                return "false";
            }

        }
        return $value;
    }
}