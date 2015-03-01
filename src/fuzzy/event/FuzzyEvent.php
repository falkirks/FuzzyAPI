<?php
namespace fuzzy\event;


use pocketmine\event\Event;

class FuzzyEvent extends Event{
    public static $handlerList = null;
    public static $eventPool = [];
    public static $nextEvent = 0;

}