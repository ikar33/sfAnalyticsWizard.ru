<?php

require_once __DIR__ . '/GoogleAnalytics/vendor/autoload.php';

class AnalyticsLoader
{

    private $PROJECT_ID = "sixth-episode-99111";
    private $DATA_SET = "GoogleAnalytics";
    private $TABLE_NAME = "Epool_test_table";

    private $analytics;

    function __construct()
    {
        $this->analytics = $this->initializeAnalytics();
    }


    public function loadData($clientName, $GUA, $ViewName, $optParams, $metrics, $startDate, $endDate)
    {

        //$profile = 164462574;
        //vvt2011@mail.ru
        //UA-21131257-23
        //164462574
        $profile = $this->getProfileIdByAccountsName($clientName, $GUA, $ViewName);

        $dataSource = $this->getResults($profile, $optParams, $metrics, $startDate, $endDate);

        $resultData = [];
        $resultData["Data"] = $dataSource->getRows(); //$this->getRowData($dataSource);
        $resultData["FieldsSchema"] = $this->getFieldsSchema($dataSource);

        return $resultData;
    }


    function initializeAnalytics()
    {

        $KEY_FILE_LOCATION = __DIR__ . '/Data/token.json';
        $client = new Google_Client();
        $client->setApplicationName("Hello Analytics Reporting");
        $client->setAuthConfig($KEY_FILE_LOCATION);
        $client->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);
        $analytics = new Google_Service_Analytics($client);

        return $analytics;
    }

    function getProfileIdByAccountsName($accountName, $guaId, $viewName)
    {
        // Get the user's first view (profile) ID.

        // Get the list of accounts for the authorized user.
        $accounts = $this->analytics->management_accounts->listManagementAccounts();

        if (count($accounts->getItems()) > 0) {
            $items = $accounts->getItems();
            foreach ($items as $account) {
                if ($accountName == $account->getName()) {
                    $AccountId = $account->getId();
                    $properties = $this->analytics->management_webproperties->listManagementWebproperties($AccountId);

                    if (count($properties->getItems()) > 0) {
                        $items = $properties->getItems();
                        foreach ($items as $properties) {
                            if ($guaId == $properties->getId()) {
                                $profiles = $this->analytics->management_profiles->listManagementProfiles($AccountId, $properties->getId());
                                if (count($profiles->getItems()) > 0) {
                                    $items = $profiles->getItems();
                                    foreach ($items as $view) {
                                        if ($viewName == $view->getName()) {
                                            return $view->getId();
                                        }
                                    }
                                } else {
                                    throw new Exception('No views (profiles) found for this user.');
                                }
                            }
                        }
                    } else {
                        throw new Exception('No properties found for this user.');
                    }
                }
            }
        } else {
            throw new Exception('No accounts found for this user.');
        }
    }

    function getFirstProfileId()
    {
        // Get the user's first view (profile) ID.

        // Get the list of accounts for the authorized user.
        $accounts = $this->analytics->management_accounts->listManagementAccounts();

        if (count($accounts->getItems()) > 0) {
            $items = $accounts->getItems();
            $firstAccountId = $items[0]->getId();

            // Get the list of properties for the authorized user.
            $properties = $this->analytics->management_webproperties
                ->listManagementWebproperties($firstAccountId);

            if (count($properties->getItems()) > 0) {
                $items = $properties->getItems();
                $firstPropertyId = $items[0]->getId();

                // Get the list of views (profiles) for the authorized user.
                $profiles = $this->analytics->management_profiles->listManagementProfiles($firstAccountId, $firstPropertyId);

                if (count($profiles->getItems()) > 0) {
                    $items = $profiles->getItems();

                    // Return the first view (profile) ID.
                    return $items[0]->getId();

                } else {
                    throw new Exception('No views (profiles) found for this user.');
                }
            } else {
                throw new Exception('No properties found for this user.');
            }
        } else {
            throw new Exception('No accounts found for this user.');
        }
    }

    function getResults($profileId, $optParams, $metrics,$startDate, $endDate)
    {
        return $this->analytics->data_ga->get('ga:' . $profileId, $startDate, $endDate, $metrics, $optParams);
    }

    function getRowData($dataSource)
    {
        $arrayResult = array();

        if (count($dataSource->getRows()) > 0) {
            $rows = $dataSource->getRows();

            foreach ($rows as $row) {
                $arrayResult[] = array("Date" => date_format(DateTime::createFromFormat('Ymd', $row[0]), "Y-m-d"), "Sessions" => $row[1]);
            }
        }
        return $arrayResult;
    }

    function getFieldsSchema($dataSource)
    {
        $fields = [];
        foreach($dataSource->columnHeaders as $columnHeader){
            $field = array('name' => str_replace(':', '_', $columnHeader->name), 'type' => $this->actualBigQueryType($columnHeader->name ,$columnHeader->dataType));
            $fields[] = $field;
        }
        return $fields;

    }

    function actualBigQueryType($name, $type){
        if("ga:date" == $name){
            return "DATE";
        };
        if("CURRENCY" == $type){
            return "FLOAT";
        }else if("TIME" == $type){
            return "FLOAT64";
        }else if("TIME" == $type){
            return "FLOAT64";
        }
        return $type;
    }


}