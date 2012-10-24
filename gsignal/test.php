<?php
error_reporting(-1);

// we need our event library /dispatcher
include 'page.php';

class Test extends Page {

    public function __construct($program_name) {
        $this->layout = 'test.tpl.php';
        
        parent::__construct($program_name);

        $this->registerEvent('tweak_display');
        $this->registerEvent('show_page');
        $this->attach('render', array($this, 'arg_parse'));

    }

    public function show_page() {
        $this->emit('render');
        $this->emit('tweak_display');
        $this->emit('display');
    }

    // our custom object signal = store our argv/c stuff
    public function arg_parse() {
        if ($_SERVER['argc'] > 1) {
            $temp = $_SERVER['argv'];
            unset($temp[0]);
            $this->vars['argument'] = implode(' ', $temp);
        }
    }

    public function tweak_display() {
        $this->vars['bork'] = $this->program;
    }
    
}

$program = new Test('testprogram');
$program->emit('show_page');
$program->dispatch();