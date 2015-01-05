php-function-events
===================

events() : A simple function to manage hooks and events on your php application.


Quickstart
-----


```php
<?php

// register events
events('add', 'foo.bar_example', 'my_user_func_cb');
events('add', 'foo.bar_example', array($object, 'methodName'));

// will call previous func
events('trigger', 'foo.bar_example');
```



Events priority
------


```php
<?php

// register events and adding a event priority from 0 to 10, 
// 0 will be executed first, 10 last
events('add', 'foo.bar_example', 'ill_be_second', 8);
events('add', 'foo.bar_example', 'ill_be_first', 1);

// will call previous func
events('trigger', 'foo.bar_example');
```


Removing events
------

```php
<?php

$eventId = events('add', 'foo.bar_example', 'ill_be_second', 8);
$true = events('remove', $eventId);
```


Events bubbleing
------


When an events listener return false, this will prevent events to be propagated

```php
<?php


function one()
{
    return false;
}

function two()
{
    // this will not be executed
}

events('add', 'foo', 'one');
events('add', 'foo', 'two');
events('trigger', 'foo');

```

Events helpers
------


```php
<?php

// retrieve all resgitered events;
$eventsList = events();

```


Events parameters references
------

```php
<?php



```