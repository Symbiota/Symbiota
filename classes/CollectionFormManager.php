<?php
include_once($SERVER_ROOT . '/classes/Manager.php');

class CollectionFormManager extends Manager {

    public function __construct(){
        parent::__construct(null,'write');
    }

    public function generateCodeStr($collectionArr){
        if(!(array_key_exists('instcode', $collectionArr) || array_key_exists('collcode', $collectionArr))){
            return null;
        }
        $codeStr = '(';
        if(array_key_exists('instcode', $collectionArr)){
            $codeStr .= $collectionArr['instcode'];
        }
        if(array_key_exists('collcode', $collectionArr)){
            $codeStr .= '-' . $collectionArr['collcode'];
        } 
        $codeStr .= ')';
        return $codeStr;
    }
}
?>