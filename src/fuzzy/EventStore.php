<?php
namespace fuzzy;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockFormEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockSpreadEvent;
use pocketmine\event\block\BlockUpdateEvent;
use pocketmine\event\block\LeavesDecayEvent;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\Event;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerMoveEvent;

class EventStore{
    static private $eventTable = [
        "player.move" => PlayerMoveEvent::class,
        "block.break" => BlockBreakEvent::class,
        "block.form" => BlockFormEvent::class,
        "block.place" => BlockPlaceEvent::class,
        "block.spread" => BlockSpreadEvent::class,
        "block.update" => BlockUpdateEvent::class,
        "leaves.decay" => LeavesDecayEvent::class,
        "sign.change" => SignChangeEvent::class,
        "player.chat" => PlayerChatEvent::class
    ];

    public static function getEventClass($event, $localTable = []){
        if($event instanceof Event) return get_class($event);
        if(is_subclass_of($event, Event::class)) return $event;

        if(isset($localTable[$event])) return $localTable[$event];
        if(isset(EventStore::$eventTable[$event])) return EventStore::$eventTable[$event];

        return null;

    }
}