<?php
namespace fuzzy\utils;


use pocketmine\scheduler\CallbackTask;
use pocketmine\Server;

class FuzzyCallbackTask extends CallbackTask{
    private $runs = 0;

    public function __construct(callable $callable, array $args = [], $times = -1){
        parent::__construct($callable, $args);
        $this->runs = $times;
    }

    public function onRun($currentTicks){
        if($this->runs === 0){
            Server::getInstance()->getScheduler()->cancelTask($this->getTaskId());
        }
        $this->runs--;
        parent::onRun($currentTicks);
    }
}