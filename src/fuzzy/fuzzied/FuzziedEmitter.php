<?php
namespace fuzzy\fuzzied;

use fuzzy\EventStore;
use pocketmine\event\Event;

class FuzziedEmitter extends FuzziedClass{
    private $listeners = [];
    private $runOnce = [];

    protected $localTable = [];

    public function on($event, callable $callback){
        $e = EventStore::getEventClass($event, $this->localTable);
        if($e !== null){
            if($this->listeners[$event] === null){
                $this->listeners[$event] = [];
            }
            $this->listeners[$event][] = $callback;
        }
        else{
            throw new \EventNotKnownException();
        }
    }
    public function once($event, callable $callback){
        $e = EventStore::getEventClass($event, $this->localTable);
        if($e !== null){
            if($this->runOnce[$event] === null){
                $this->runOnce[$event] = [];
            }
            $this->runOnce[$event][] = $callback;
        }
        else{
            throw new \EventNotKnownException();
        }
    }

    /**
     * Returns true if the $event concerns this emitter
     * @param Event $event
     * @return bool
     */
    public function pertainsTo(Event $event) : bool {
        $reflect = new \ReflectionClass($this);
        foreach($reflect->getMethods(\ReflectionMethod::IS_PUBLIC) as $method){
            if(strpos($method->getName(), "get") === 0 && !$method->isStatic()){
                $ret = $method->getClosure($event)->call($event);
                if($ret === $this->getObject()){
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Passes the event on to listeners if there are any
     * @param Event $event
     */
    public function notifyEvent(Event $event){
        if($this->pertainsTo($event)){
            $class = get_class($event);
            if($this->listeners[$class] !== null){
                foreach ($this->listeners[$class] as $c){
                    $c($event);
                }
            }

            if($this->runOnce[$class] !== null){
                foreach ($this->runOnce[$class] as $c){
                    $c($event);
                }
                $this->runOnce[$class] = [];
            }
        }
    }
}