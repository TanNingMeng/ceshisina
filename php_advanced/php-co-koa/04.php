<?php

class Gen
{
    public $isfirst = true;
    public $generator;

    public function __construct(\Generator $generator)
    {
        $this->generator = $generator;
    }

    public function valid()
    {
        return $this->generator->valid();
    }

    public function send($value = null)
    {
        if ($this->isfirst) {
            $this->isfirst = false;
            return $this->generator->current();
        } else {
            return $this->generator->send($value);
        }
    }
}


final class AsyncTask
{
    public $gen;

    public function __construct(\Generator $gen)
    {
        $this->gen = new Gen($gen);
    }
    
    public function begin()
    {
        return $this->next();
    }

    public function next($result = null)
    {
        $value = $this->gen->send($result);

        if ($this->gen->valid()) {
            if ($value instanceof \Generator) {
                $value = (new self($value))->begin();
            }
            return $this->next($value);

        } else {
            return $result;
        }
    }
}

function newSubGen()
{
    yield 0;
    yield 1;
}

function newGen()
{
    $r1 = (yield newSubGen());
    $r2 = (yield 2);
    echo $r1, $r2;
    yield 3;
}
$task = new AsyncTask(newGen());
$r = $task->begin(); // output: 12
echo $r; // output: 3