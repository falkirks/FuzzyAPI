<?php
namespace fuzzy\fuzzied;


class FuzziedClass {
    protected $object;
    public function __construct($object){
        $this->object = $object;
    }

    function __call($name, $arguments){
        $reflection = new \ReflectionClass($this->object);
        if($reflection->hasMethod($name)) {
            $method = $reflection->getMethod($name);
            $setAccessible = false;
            if ($method->isPrivate() || $method->isProtected()) {
                $method->setAccessible(true);
                $setAccessible = true;
            }
            $ret = $this->object->$name(...$arguments);
            if ($setAccessible) $method->setAccessible(false);
            return $ret;
        }
        return false;
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