<?php
namespace App\Http\Interfaces;

interface ImportCollectionInterface {
    public function preProcess($data);
    public function postProcess($data);
}
?>
