<?php

class EventsRegistryTest extends PHPUnit_Framework_TestCase
{

    protected $eventCounter = 0;


    public function testAddRemove()
    {
        
        $eventsArray = events();
        $eventsArray->exchangeArray(array());

        $this->assertEquals(0, count($eventsArray));

        $id = events('add', 'foo.a', array($this, 'inc'));
        $id = events('add', 'foo.b', array($this, 'inc'));
        $id = events('add', 'foo.c', array($this, 'inc'));

        $this->assertEquals(3, count($eventsArray));

        $this->eventCounter = 0;
        events('trigger', 'foo.a');
        events('trigger', 'foo.b');
        events('trigger', 'foo.c');
        $this->assertEquals(3, $this->eventCounter);

        $true = events('remove', $id);
        $this->eventCounter = 0;
        events('trigger', 'foo.c');

        $this->assertEquals(true, $true);
        $this->assertEquals(0, $this->eventCounter);
    }

    public function testPriorityEvents()
    {
        $eventsArray = events();
        $eventsArray->exchangeArray(array());

        $false = events('remove', 'foo.c');

        $this->assertEquals(false, $false);

        events('add', 'foo test ', array($this, "inc"));
        events('add', ' foo.TEST', array($this, "setTo"), 4);
        events('add', 'foo.TEST', array($this, "setTo"), 4);
        events('add', 'foo.test', array($this, "setTo"), 3);
        events('add', 'foo.test', array($this, "inc"), 1);

        events('trigger', 'foo.TEST', array(
            'value' => 100
        ));

        $this->assertEquals(100, $this->eventCounter);

        $this->eventCounter = 0;

        events('trigger', 'foo.test', array(
            'value' => 500
        ));

        $this->assertEquals(500, $this->eventCounter);

    }


    public function testEventsListenerArgumentsReference()
    {
        $eventsArray = events();
        $eventsArray->exchangeArray(array());

        events('add', 'foo.test', array($this, "incParams"));
        events('add', 'foo.test', array($this, "incParams"));
        events('add', 'foo.test', array($this, "incParams"));

        events('trigger', 'foo.test', array(
            'counter' => 1
        ));

        $this->assertEquals(4, $this->eventCounter);
    }

    /**
     * Increment params key to verifiy references
     * 
     * @param  array $params [description]
     */
    public function incParams($params)
    {
        $params['counter']++;
        $this->eventCounter = $params['counter'];
    }
   

    public function inc($event)
    {
        $this->eventCounter++;
    }

    public function dec($event)
    {
        $this->eventCounter--;
    }

    public function setTo($event)
    {
        $this->eventCounter = $event['value'];
    }


}