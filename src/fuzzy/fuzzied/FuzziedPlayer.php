<?php
namespace fuzzy\fuzzied;


use pocketmine\Player;
use pocketmine\scheduler\CallbackTask;

class FuzziedPlayer extends FuzziedClass{
    /** @var  Player */
    protected $object;
    public function __construct(Player $player){
        parent::__construct($player);
    }
    public function sayHello(){
        $this->__call("sendMessage", ["Hello " . $this->object->getName()]);
    }
}