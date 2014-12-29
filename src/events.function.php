<?php
/**
 * A simple function to help to get a DEAD-SIMPLE global hook system 
 *
 * @author Emmanuel VAISSE
 * @internal changelog:
 *     Emmanuel VAISSE - 2015-12-29 16:51:30
 *         
 */
if (!function_exists('events')) {

    /**
     * A dead simple events dispatching method that can 
     * 
     *     - registering listeners
     *     - removing events
     *     - trigger events with custom data payload
     *     - stop event propagation
     *     - get raw event list
     *
     * @see http://symfony.com/doc/current/components/event_dispatcher/introduction.html#naming-conventions
     * 
     * @param  string  $action    action, add, remove, trigger, once
     * @param  string  $eventName A string event identifier, following symfony events guidelines
     * @param  array   $params    A array of parameters context to pass to the listeners
     * @param  integer $priority  Only in "add" or "once" case, a priority index from 0 to 10.
     * @return mixed
     */
    function events($action = null, $eventName = null, $params = null, $priority = 5) {

        /**
         * array registry for events
         * @var array
         */
        static $events;
        /**
         * uid helper counter 
         * @var integer
         */
        static $eventsId;

        if (!$events) {
            $events = new ArrayObject();
            $eventsId = 0;
        }

        $argsNum = func_num_args();

        if (!$argsNum) {
            return $events;
        }

        $actions = array(
            'add',
            'remove',
            'once',
            'trigger',        
        );

        if (!in_array($action, $actions, true)) {
            trigger_error("[events()] invalid action provided");
            return false;
        }

        /*
         * Remove callback
         */
        if ($action === "remove" && $argsNum == 2) {
            foreach ($events as $name => $callbacks) {
                foreach ($callbacks as $key => $value) {
                    if ($key === $eventName) {
                        unset($events[$name][$key]);
                        return true;
                    }
                }
            }
            return false;
        }

        $events[$eventName] = isset($events[$eventName]) ? $events[$eventName] : array();

        $priority = (int)$priority;
        $priority = ($priority < 11 && $priority > -1) ? $priority : 5;

        /*
         * Add action
         */
        if (($action === "add" || $action === "once") && strlen($eventName)) {
            $k = sprintf("%02s.%s", $priority, $eventsId++);
            $events[$eventName][$k] = array(
                'callable' => $params,
                'once'     => $action === "once",
            );
            ksort($events[$eventName]);
            return $k;
        }

        /*
         * trigger event
         */
        if ($action === "trigger") {

            $params = is_array($params) ? new ArrayObject($params) : new ArrayObject();

            $args = new ArrayObject();
            $args['name'] = $eventName; 
            $args['context'] = $params;
            ob_start();
            debug_print_backtrace();
            $args['trace'] = ob_get_clean(); 

            foreach ($events[$eventName] as $key => $value) {
                $break = call_user_func($value['callable'], $args);

                if ($value['once']) {
                    unset($events[$eventName][$key]); 
                }

                if ($break === false) {
                    break; // stopped by user
                }
            }
        }

        return false;
        
    }

}