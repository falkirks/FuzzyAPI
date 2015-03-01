<?php
namespace fuzzy;

use fuzzy\event\fuzzy\FuzzyReadyEvent;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\CallbackTask;

class Fuzzy extends PluginBase implements Listener{
    public function onEnable(){
        foreach($this->getServer()->getPluginManager()->getPlugins() as $plugin){
            if($plugin instanceof PluginBase) {
                if (in_array("FuzzyAPI", $plugin->getDescription()->getDepend())) {
                    $plugin->fuzzy = new FuzzyAPI($this->getServer(), $plugin, $this);
                    $plugin->fuzzy->init();
                }
            }
        }
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        //$this->getServer()->getScheduler()->scheduleDelayedTask(new CallbackTask([$this, "callFuzzyEvent"], []), 1);
    }
    public function onFuzzyReady(FuzzyReadyEvent $event){
        $this->getServer()->getLogger()->info("FuzzyAPI loaded.");
    }
    public function callFuzzyEvent(){
        $this->getServer()->getPluginManager()->callEvent(new FuzzyReadyEvent($this));

    }
}