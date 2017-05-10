<?php
namespace fuzzy\utils;


use pocketmine\plugin\Plugin;
use pocketmine\scheduler\PluginTask;
use pocketmine\Server;

class FuzzyCallbackTask extends PluginTask {
    private $runs = 0;
    /** @var  callable */
    private $callable;
    private $args;

    public function __construct(Plugin $plugin, callable $callable, array $args = [], $times = -1){
        parent::__construct($plugin);
        $this->runs = $times;
        $this->callable = $callable;
        $this->args = $args;
    }

    public function onRun($currentTicks){
        if($this->runs === 0){
            Server::getInstance()->getScheduler()->cancelTask($this->getTaskId());
        }
        $this->runs--;
        $c = $this->callable;
        $c(...$this->args);
    }
}