<?php
namespace fuzzy\fuzzied;


use pocketmine\scheduler\CallbackTask;
use pocketmine\Server;

class FuzziedClass {
    protected $object;
    protected $delay;
    protected $repeat;
    public function __construct($object){
        $this->object = $object;
        $this->delay = false;
        $this->repeat = false;
    }

    function __call($name, $arguments){
        if($this->delay !== false && $this->repeat !== false){
            $callbackTask = new CallbackTask([$this, "__call"], [$name, $arguments]);
            $task = Server::getInstance()->getScheduler()->scheduleDelayedRepeatingTask($callbackTask, $this->delay, $this->repeat);
            $this->delay = false;
            $this->repeat = false;
            return $task;
        }
        elseif($this->delay !== false){
            $callbackTask = new CallbackTask([$this, "__call"], [$name, $arguments]);
            $task = Server::getInstance()->getScheduler()->scheduleDelayedTask($callbackTask, $this->delay);
            $this->delay = false;
            return $task;
        }
        elseif($this->repeat !== false){
            $callbackTask = new CallbackTask([$this, "__call"], [$name, $arguments]);
            $task = Server::getInstance()->getScheduler()->scheduleRepeatingTask($callbackTask, $this->repeat);
            $this->repeat = false;
            return $task;
        }
        else {
            $reflection = new \ReflectionClass($this->object);
            if ($reflection->hasMethod($name)) {
                $method = $reflection->getMethod($name);
                $setAccessible = false;
                if ($method->isPrivate() || $method->isProtected()) {
                    $method->setAccessible(true);
                    $setAccessible = true;
                }
                $ret = $this->object->$name(...$arguments);
                if ($setAccessible) $method->setAccessible(false);
                return FuzziedClassPool::getFuzzied($ret);
            }
            return false;
        }
    }
    public function delay($ticks = 20){
        $this->delay = $ticks;
        return $this;
    }
    public function repeat($ticks = 20){
        $this->repeat = $ticks;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getObject(){
        return $this->object;
    }
    public function unfuzz(){
        return $this->object;
    }
}