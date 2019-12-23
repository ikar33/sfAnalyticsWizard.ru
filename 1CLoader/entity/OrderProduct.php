<?php
/**
 * Created by PhpStorm.
 * User: i.karabadjak
 * Date: 12.09.2018
 * Time: 11:04
 */

class OrderProduct
{
    private $Call_Stat_Fields = [];

    function __construct()
    {
        $this->Call_Stat_Fields["ГУИДЗаявки"] = ["type" => "STRING", "name" => "order_guid"];
        $this->Call_Stat_Fields["ГУИДНоменклатуры"] = ["type" => "STRING", "name" => "guid"];
        $this->Call_Stat_Fields["Наименование"] = ["type" => "STRING", "name" => "name"];
        $this->Call_Stat_Fields["Бренд"] = ["type" => "STRING", "name" => "brand"];
        $this->Call_Stat_Fields["ГУИДКатегория"] = ["type" => "STRING", "name" => "category_guid"];
        $this->Call_Stat_Fields["Категория"] = ["type" => "STRING", "name" => "category"];
        $this->Call_Stat_Fields["Себестоимость"] = ["type" => "FLOAT", "name" => "cost_price"];
        $this->Call_Stat_Fields["Сумма"] = ["type" => "FLOAT", "name" => "amount"];
        $this->Call_Stat_Fields["Количество"] = ["type" => "FLOAT", "name" => "quantity"];
        $this->Call_Stat_Fields["ДатаЗаявки"] = ["type" => "DATE", "name" => "order_date"];

    }

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

    public function loadDataSet($data_row)
    {
        $result = [];
        $result["Data"] = [];
        $result["FieldsSchema"] = [];

        foreach ($this->Call_Stat_Fields as $fields_params) {
            $result["FieldsSchema"][$fields_params["name"]] = array('name' => $fields_params["name"], 'type' => $fields_params["type"]);
        }
        foreach ($data_row as $order) {
            $data = $order->СоставЗаказа;
            $order_guid = $order->ГУИД;
            $order_date = $order->ДатаПрисвоения;

            foreach ($data as $data_row) {
                $row = [];
                foreach ($this->Call_Stat_Fields as $fields_name => $fields_params) {
                    if ($fields_name == "ГУИДЗаявки") {
                        $row[$fields_params["name"]] = $order_guid;
                    } else if ($fields_name == "ДатаЗаявки") {
                        $date = DateTime::createFromFormat('Y-m-d\TH:i:s', $order_date);
                        $row[$fields_params["name"]] = $date;
                    } else if ($fields_params["type"] == "DATE") {
                        $date = DateTime::createFromFormat('Y-m-d\TH:i:s', $data_row->{$fields_name});
                        $row[$fields_params["name"]] = $date;
                    } else {
                        if (isset($data_row->{$fields_name}) && strlen($data_row->{$fields_name}) > 0) {
                            $row[$fields_params["name"]] = $data_row->{$fields_name};
                        } else {
                            $row[$fields_params["name"]] = "";
                        }
                    }
                }
                $result["Data"][] = $row;
            }
        }
        $this->data_set = $result;
    }

    public function getDataSet()
    {
        return $this->data_set;
    }
}