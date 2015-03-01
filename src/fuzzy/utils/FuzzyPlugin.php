<?php
namespace fuzzy\utils;


use fuzzy\FuzzyAPI;
use pocketmine\plugin\PluginBase;

class FuzzyPlugin extends PluginBase{
    /** @var  FuzzyAPI */
    public $fuzzy;
    public final function getFuzzy(){
        return $this->fuzzy;
    }
}