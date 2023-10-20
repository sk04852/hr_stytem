<?php
    namespace App\Traits;
    trait GetOnlyFillablesTrait{
        public function getOnlyFillables($data){
            $array = $this->getFillable();
            $arr = [];
            foreach ($array as $item){
                if( array_key_exists($item, $data) && !isset($ignoreAbles[$item])) {
                    $arr["$item"] = $data["$item"];
                }
            }
            return $arr;
        }
    }
