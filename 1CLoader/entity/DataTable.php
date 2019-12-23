<?php
/**
 * Created by PhpStorm.
 * User: i.karabadjak
 * Date: 15.09.2018
 * Time: 19:51
 */

class DataTable
{
    protected $Call_Stat_Fields = [];
    protected $data_set;


    public function getFieldsNamesList()
    {
        return array_keys($this->Call_Stat_Fields);
    }

    public function getFieldsType($fieldsName)
    {
        return $this->Call_Stat_Fields[$fieldsName];
    }

    public function getTablesSchema()
    {
        $fields = [];
        foreach ($this->Call_Stat_Fields as $fieldsName => $fields_params) {
            $field = [];
            $field['name'] = $fields_params["name"];
            $field['type'] = $fields_params["type"];
            $fields[] = $field;
        }
        return $fields;
    }

    public function loadDataSet($data)
    {
        $result = [];
        $result["Data"] = [];
        $result["FieldsSchema"] = [];

        foreach ($this->Call_Stat_Fields as $fields_params) {
            $result["FieldsSchema"][$fields_params["name"]] = array('name' => $fields_params["name"], 'type' => $fields_params["type"]);
        }
        foreach ($data as $data_row) {
            $row = [];
            foreach ($this->Call_Stat_Fields as $fields_name => $fields_params) {
                if ($fields_params["type"] == "DATE") {
                    $date = DateTime::createFromFormat('Y-m-d\TH:i:s', $data_row->{$fields_name});
                    $row[$fields_params["name"]] = $date;
                } else {
                    if(isset($data_row->{$fields_name}) && strlen($data_row->{$fields_name}) > 0) {
                        $row[$fields_params["name"]] = $data_row->{$fields_name};
                    }else{
                        $row[$fields_params["name"]] = "";
                    }
                }
            }
            $result["Data"][] = $row;
        }
        $this->data_set = $result;
    }

    public function getDataSet(){
        return $this->data_set;
    }
}