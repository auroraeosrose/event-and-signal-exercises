<?php

abstract class Object {
    protected $signals = array();

    public function __construct()
    {
        $ref   = new ReflectionClass($this);
        
        foreach ($ref->getConstants() as $key => $val)
        {
            if (method_exists($this, $val))
            {
                $this->signals[$val] = array();
            }
        }
    }

    public function connect($signal, $slot)
    {     
        $this->signals[$signal][] = $slot;
    }
   
    public function disconnect($signal, $slot)
    {
        foreach ($this->signals[$signal] as $id => $receiver)
        {
            if ($receiver === $slot)
            {
                unset($this->signals[$signal][$id]);
                return true;
            }
        }
        return false;
    }

    public function emit($signal /*, ... */)
    {
        $return = null;
        $args = array_slice(func_get_args(), 1);
        foreach ($this->signals[$signal] as $receiver)
        {
            $return = call_user_func_array($receiver, $args);
            
        }
        return $return;
    }

}


class MacFanboi extends Object
{
    const OS_ATTACK = 'attack';

    public function defend()
    {
        echo "Mac's have a better build quality.  PC's are cheap, and when I say 'cheap', I mean 'cheap'\n";
    }

    public function attack()
    {
        echo "Microsoft steal ideas.  That's why your OS sucks so much because Windows is a stolen idea.\n";
        $this->emit(self::OS_ATTACK);
    }
}

class PCFanboi extends Object
{
    const OS_DEFEND = 'defend';

    public function defend()
    {
        echo "Bill Gates gives millions to Microsoft, Apple is a soul sucking money hoarding corporation bend on subjecting the free world\n";
        $this->emit(self::OS_DEFEND);
    }
}

$i_am_a_mac = new MacFanboi();
$i_am_a_pc = new PCFanboi();

$i_am_a_mac->connect(MacFanboi::OS_ATTACK, array($i_am_a_pc, 'defend'));
$i_am_a_pc->connect(PCFanboi::OS_DEFEND, array($i_am_a_mac, 'defend'));

$i_am_a_mac->attack();
