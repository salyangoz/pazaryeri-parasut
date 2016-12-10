<?php

namespace salyangoz\pazaryeriparasut;
use Storage;

class LocalStorage
{

    private $currentData;
    private $dataTemplate   =   [
        "customer" =>   [],
        "order"    =>   [],
        "product"  =>   []
    ];


    public function get($key)
    {
        LocalStorage::checkFile();
        if($value = array_get($this->currentData,$key,false))
        {
            return $value;
        }

        return false;
    }

    public function save()
    {
        Storage::disk('local')->put('parasut-data.json', json_encode($this->currentData));
    }

    public function set($store,$key,$value)
    {
        $this->currentData[$store][$key] = $value;
    }

    private function checkFile()
    {
        $data = Storage::get('parasut-data.json', 'Contents');
        if(strlen($data) == 0)
        {
            $this->currentData    =   $this->dataTemplate;
            $this->save();
        }
        else
        {
            $this->currentData  =   json_decode($data,true);
        }
    }
}