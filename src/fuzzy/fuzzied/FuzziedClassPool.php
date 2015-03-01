<?php
namespace fuzzy\fuzzied;


use pocketmine\Player;

class FuzziedClassPool {
    private static $packetPool = [];

    private static function registerClass($class, $fuzzy){
        FuzziedClassPool::$packetPool[$class] = $fuzzy;
    }
    static public function getFuzzied($object){
        if(is_array($object)){
            $out = [];
            foreach($object as $key => $item){
                $out[$key] = FuzziedClassPool::getFuzzied($item);
            }
            return $out;
        }
        else {
            if (empty(FuzziedClassPool::$packetPool)) FuzziedClassPool::registerClasses();
            if (is_object($object) && isset(FuzziedClassPool::$packetPool[get_class($object)])) {
                $class = FuzziedClassPool::$packetPool[$object];
                return new $class($object);
            }

            return $object;
        }
    }
    private static function registerClasses(){
        FuzziedClassPool::registerClass(Player::class, FuzziedPlayer::class);
    }
}