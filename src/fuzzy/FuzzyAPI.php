<?php
namespace fuzzy;


use fuzzy\event\fuzzy\FuzzyReadyEvent;
use fuzzy\event\FuzzyEvent;
use fuzzy\fuzzied\FuzziedClass;
use fuzzy\fuzzied\FuzziedClassPool;
use fuzzy\utils\FuzzyCommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\event\Event;
use pocketmine\event\EventPriority;
use pocketmine\event\Listener;
use pocketmine\plugin\EventExecutor;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;

class FuzzyAPI implements EventExecutor{
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
    public function runCommand($cmd, $sender = null){
        if($sender == null) $sender = new FuzzyCommandSender();
        return $this->server->dispatchCommand($sender, $cmd);
    }
    public function getPlayers(){
        return $this->__call("getOnlinePlayers", []);
    }
    public function init(){
        $this->plugin->setEnabled(true);
        if($this->plugin instanceof Listener){
            $reflection = new \ReflectionClass($this->plugin);
            foreach($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method){
                $parameters = $method->getParameters();
                if(count($parameters) === 1 and $parameters[0]->getClass() instanceof \ReflectionClass and is_subclass_of($parameters[0]->getClass()->getName(), Event::class)){
                    if(!is_subclass_of($parameters[0]->getClass()->getName(), FuzzyEvent::class)) {
                        $priority = EventPriority::NORMAL;
                        $ignoreCancelled = false;
                        $fuzzy = false;
                        if(preg_match("/^[\t ]*\\* @priority[\t ]{1,}([a-zA-Z]{1,})/m", (string) $method->getDocComment(), $matches) > 0){
                            $matches[1] = strtoupper($matches[1]);
                            if(defined(EventPriority::class . "::" . $matches[1])){
                                $priority = constant(EventPriority::class . "::" . $matches[1]);
                            }
                        }
                        if(preg_match("/^[\t ]*\\* @ignoreCancelled[\t ]{1,}([a-zA-Z]{1,})/m", (string) $method->getDocComment(), $matches) > 0){
                            $matches[1] = strtolower($matches[1]);
                            if($matches[1] === "false"){
                                $ignoreCancelled = false;
                            }elseif($matches[1] === "true"){
                                $ignoreCancelled = true;
                            }
                        }
                        if(preg_match("/^[\t ]*\\* @fuzzy[\t ]{1,}([a-zA-Z]{1,})/m", (string) $method->getDocComment(), $matches) > 0){
                            $matches[1] = strtolower($matches[1]);
                            if($matches[1] === "false"){
                                $fuzzy = false;
                            }elseif($matches[1] === "true"){
                                $fuzzy = true;
                            }
                        }
                        $this->server->getPluginManager()->registerEvent($parameters[0]->getClass()->getName(), $this->plugin, $priority, $this, $this->plugin, $ignoreCancelled);
                        $this->listenerMethods[$parameters[0]->getClass()->getName()][] = [$method->getName(), $fuzzy];

                    }
                    else{
                        $this->listenerMethods[$parameters[0]->getClass()->getName()][] = [$method->getName(), false];
                    }
                }
            }
        }
        $this->callEvent(new FuzzyReadyEvent());
    }

    /**
     * @param Listener $listener
     * @param Event $event
     *
     * @return void
     */
    public function execute(Listener $listener, Event $event){
        if($listener !== $this->plugin) return;
        $this->callEvent($event);

    }

    private function callEvent(Event $event){
        $class = get_class($event);
        if(isset($this->listenerMethods[$class])) {
            foreach ($this->listenerMethods[$class] as $method) {
                $method = $method[0];
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
        return FuzziedClassPool::getFuzzied($ret);
    }
    public function fuzz($object){
        return FuzziedClassPool::getFuzzied($object);
    }
    public function unfuzz($object){
        if($object instanceof FuzziedClass){
            return $object->unfuzz();
        }
        return $object;
    }
    public function __get($name){
        // TODO: Implement __get() method.
    }

    function __set($name, $value){
        // TODO: Implement __set() method.
    }

}