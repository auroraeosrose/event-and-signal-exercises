<?php

abstract class Page {
    protected $program;
    protected $vars;
    protected $rendered;
    protected $events;
    protected $handlers = array();
    protected $stack;
    
    public function __construct($program_name) {
        $this->program = $program_name;
        $this->events = array(
                        'render',
                        'display');

        foreach($this->events as $name) {
            $this->handlers[$name] = array();
            if(method_exists($this, $name)) {
                $this->handlers[$name][] = array($this, $name);
            }
        }
        $this->stack = array();
    }

    public function registerEvent($name) {
        $this->events[] = $name;
        $this->handlers[$name] = array();
        if(method_exists($this, $name)) {
            $this->handlers[$name][] = array($this, $name);
        }
    }

    public function attach($event, $callback) {
        array_unshift($this->handlers[$event], $callback);
    }

    public function emit($event) {
        $this->stack[] = $event;
    }

    public function render() {
        if (isset($this->vars)) {
            extract($this->vars);
        }

        // include page
        ob_start();
        include $this->layout;
        $this->rendered = ob_get_clean();
    }

    public function display() {
        echo $this->rendered;
    }

    public function dispatch() {
        while(count($this->stack) > 0) {
            $item = array_shift($this->stack);
            foreach($this->handlers[$item] as $callback) {
                $return_value = call_user_func($callback);
                if ($return_value == true) {
                    break;
                }
            }
        }  
    }

}