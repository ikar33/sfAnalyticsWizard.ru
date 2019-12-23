<?php

class Order extends DataTable
{
    function __construct()
    {
        $this->Call_Stat_Fields["ГУИД"] = ["type" => "STRING", "name" => "guid"];
        $this->Call_Stat_Fields["НомерЗаказа"] = ["type" => "STRING", "name" => "order_number"];
        $this->Call_Stat_Fields["СтатусЗаказа"] = ["type" => "STRING", "name" => "status"];
        $this->Call_Stat_Fields["Источник"] = ["type" => "STRING", "name" => "source"];
        $this->Call_Stat_Fields["ДатаПрисвоения"] = ["type" => "DATE", "name" => "acquisition_date"];
        $this->Call_Stat_Fields["Канал"] = ["type" => "STRING", "name" => "channel"];
        $this->Call_Stat_Fields["Регион"] = ["type" => "STRING", "name" => "region"];
        $this->Call_Stat_Fields["КаналПриема"] = ["type" => "BOOLEAN", "name" => "receive_channel"];
        $this->Call_Stat_Fields["СуммаЗаказа"] = ["type" => "FLOAT", "name" => "order_amount"];
    }

}