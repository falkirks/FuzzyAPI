<?php
namespace fuzzy\utils;


use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionAttachment;
use pocketmine\plugin\Plugin;
use pocketmine\Server;

class FuzzyCommandSender extends ConsoleCommandSender{
    /**
     * @param string $message
     */
    public function sendMessage($message){

    }
    /**
     * @return string
     */
    public function getName(){
        return "FUZZY";
    }

    /**
     * Checks if this instance has a permission overridden
     *
     * @param string|Permission $name
     *
     * @return boolean
     */
    public function isPermissionSet($name){
        return true;
    }

    /**
     * Returns the permission value if overridden, or the default value if not
     *
     * @param string|Permission $name
     *
     * @return mixed
     */
    public function hasPermission($name){
        return true;
    }
}