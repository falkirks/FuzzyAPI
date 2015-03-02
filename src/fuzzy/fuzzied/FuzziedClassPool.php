<?php
namespace fuzzy\fuzzied;


use pocketmine\Player;

class FuzziedClassPool {
    private static $classPool = [];

    private static function registerClass($class, $fuzzy){
        FuzziedClassPool::$classPool[$class] = $fuzzy;
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
            if (empty(FuzziedClassPool::$classPool)) FuzziedClassPool::registerClasses();
            if (is_object($object) && isset(FuzziedClassPool::$classPool[get_class($object)])) {
                $class = FuzziedClassPool::$classPool[get_class($object)];
                return new $class($object);
            }

            return (is_object($object) ? new FuzziedClass($object) : $object);
        }
    }
    private static function registerClasses(){
        FuzziedClassPool::registerClass(Player::class, FuzziedPlayer::class);
    }
}