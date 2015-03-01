<?php
namespace fuzzy;


use fuzzy\event\fuzzy\FuzzyReadyEvent;
use fuzzy\event\FuzzyEvent;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\CallbackTask;
use pocketmine\Server;

class FuzzyAPI {
    private $server;
    private $plugin;
    private $fuzzy;

    private $isListening;
    private $listenerMethods;
    public function __construct(Server $server, PluginBase $plugin, Fuzzy $fuzzy){
        $this->server = $server;
        $this->plugin = $plugin;
        $this->fuzzy = $fuzzy;
        $this->isAvailable = true;
        $this->isListening = false;
        $this->listenerMethods = [];
        $this->sendFuzz();
    }
    public function init(){
        $this->plugin->setEnabled(true);
        if($this->plugin instanceof Listener){
            $this->server->getPluginManager()->registerEvents($this->plugin, $this->plugin);
            $reflection = new \ReflectionClass($this->plugin);
            foreach($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method){
                $parameters = $method->getParameters();
                if(count($parameters) === 1 and $parameters[0]->getClass() instanceof \ReflectionClass and is_subclass_of($parameters[0]->getClass()->getName(), FuzzyEvent::class)){
                    $this->listenerMethods[$parameters[0]->getClass()->getName()][] = $method->getName();
                }
            }
        }
        $this->callEvent(new FuzzyReadyEvent());
    }
    private function callEvent(FuzzyEvent $event){
        $class = get_class($event);
        if(isset($this->listenerMethods[$class])) {
            foreach ($this->listenerMethods[$class] as $method) {
                $this->plugin->$method($event);
            }
        }
    }
    public function sendFuzz(){
        $this->server->getLogger()->info("Fuzzy ❤  " . $this->plugin->getName());
    }

    public function __call($name, $arguments){
        $reflection = new \ReflectionClass($this->server);
        if($reflection->hasMethod($name)) $method = $reflection->getMethod($name);
        else {
            $this->server->getLogger()->critical($name . " is not a method, using closest match.");
            $best = PHP_INT_MAX;
            $method = null;
            foreach($reflection->getMethods() as $testMethod){
                $diff = levenshtein($name, $testMethod->getName());
                if($diff < $best){
                    $method = $testMethod;
                    $best = $diff;

                }
            }
        }
        $setAccessible = false;
        if($method->isPrivate() || $method->isProtected()){
            $method->setAccessible(true);
            $setAccessible = true;
        }
        $this->server->getLogger()->info("Calling " . $method->getName());
        $name = $method->getName();
        $ret = $this->server->$name(...$arguments);
        if($setAccessible) $method->setAccessible(false);
        return $ret;
    }

    public function __get($name){
        // TODO: Implement __get() method.
    }

    function __set($name, $value){
        // TODO: Implement __set() method.
    }

}