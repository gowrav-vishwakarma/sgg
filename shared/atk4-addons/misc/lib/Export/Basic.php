<?php

namespace misc;
class Export_Basic extends \AbstractController {
    function init(){
        parent::init();
        $this->api->addHook("pre-render-output", array($this, "export"));
    }
    function export(){
        /* so export will work in the following way:
         * 1) take dq from parent
         * 2) take model from parent 
         * 3) fetch data
         * 4) use export_parser to prepare output required 
         */
        $data = array();
        $keys = null;
        foreach ($this->owner->dq as $k => $row){
            if (!$keys){
                $keys = array_keys($row);
            }
            $data[] = $row;
        }
        $captions = array();
        if ($keys){
            if ($m=$this->owner->getModel()){
                foreach ($keys as $key){
                    try {
                        if ($o=$m->getField($key)){
                            $captions[$key] = is_object($o)?$o->caption()?:$key:$key;
                        } else {
                            $captions[$key] = $key;
                        }
                    } catch (\Exception $e){
                        $captions[$key] = $key;
                    }
                }
            } else {
                foreach ($keys as $key){
                    $captions[$key] = $key;
                } 
            }
        }
        $this->captions = $captions;
        $this->data = $data;
    }
}
