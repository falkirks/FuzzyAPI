<?php
namespace fuzzy\fuzzied;


use pocketmine\Player;

class FuzziedPlayer extends FuzziedClass{
    /** @var  Player */
    protected $object;
    public function __construct(Player $player){
        parent::__construct($player);
    }
    public function sayHello(){
        $this->object->sendMessage("Hello " . $this->object->getName());
    }
}