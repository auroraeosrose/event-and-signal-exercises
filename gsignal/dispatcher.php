<?php

class Dispatcher {

    static $objects = array();

    static public function addObject($obj_name, array $events) {
        self::$objects[$obj_name] = $events;
    }

    static public function addEvent($obj_name, $event) {
        self::$objects[$obj_name][] = $event;
    }

    static public function hasEvent($obj_name, $event) {
        return in_array($event, self::$objects[$obj_name]);
    }

    static public function start($object) {
        $object->emit('construct');
    }
}