<?php

namespace salyangoz\pazaryeriparasut;
use Storage;

class LocalStorage
{

    private $currentData;
    private $dataTemplate   =   [
        "customer"          =>  [],
        "order"             =>  [],
        "product"           =>  [],
        "waiting_einvoice"  =>  []
    ];


    /**
     * Verilen keye ait data varsa onu döndürür
     * @param $key
     * @return bool|mixed
     */
    public function get($key)
    {
        LocalStorage::checkFile();
        if($value = array_get($this->currentData,$key,false))
        {
            return $value;
        }

        return false;
    }

    /**
     * Güncel datayı dosyaya kaydeder
     */
    public function save()
    {
        Storage::disk('local')->put('parasut-data.json', json_encode($this->currentData));
    }

    /**
     * Verilen key ve datayı ilgili store'a kaydeder
     * @param $store
     * @param $key
     * @param $value
     */
    public function set($store,$key,$value)
    {
        $this->currentData[$store][$key] = $value;
    }

    /**
     * Storedaki keyi siler
     * @param $store
     * @param $key
     */
    public function delete($store,$key)
    {
        unset($this->currentData[$store][$key]);

        return;
    }

    /**
     * Veri dosyasının boş olup olmadığına bakar. Boş ise template datayı ekler.
     */
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