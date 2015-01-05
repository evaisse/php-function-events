php-function-events
===================

events() : A simple function to manage hooks and events on your php application.




```php
<?php

// register events
events('add', 'foo.bar_example', 'my_user_func_cb');
events('add', 'foo.bar_example', array($object, 'methodName'));

// will call previous func
events('trigger', 'foo.bar_example');
```

Another example