[![Build Status](https://travis-ci.org/CatalystIT-AU/moodle-local_envbar.svg?branch=master)](https://travis-ci.org/CatalystIT-AU/moodle-local_envbar)

Environment bar - Moodle local plugin
====================

This displays a prominment header across across the top of your NON PROD Moodle
environments which can be configured to have different colors and messages for
each environent, and also automatically detects and show you when the DB was
last refreshed.

It's very useful when working with lots of different environments to avoid
confusion around where you are, especially when env's can contain hard coded
links and you accidentally jump between environments.

Principals
----------

Showing what environment you are in needs to be reliable and fail safe.

If it doesn't work for any reason then you may as well not have it. The way
this plugin works is that in your production system you specify what your
different environments are. Then after a refresh of production data back to a
staging environment it can auto detect that it is no longer in production and
warn the end user. Further more if there isn't any config at all, then it will
assume you are in a fresh development environment that hasn't been refreshed
and show a default fail safe warning.

It will also automatically detect and show you when the environment was last
refreshed from production, which is a common question testers ask.

Installation
------------

Add the plugin to /local/envbar/

Run the Moodle upgrade.

# Setup

The plugin can be configured via,
    `(Site administration > Plugins > Local Plugins > Environment bar)`

Or you can manually configure the bars and prodwwwroot in config.php,

```php
    $CFG->local_envbar_prodwwwroot = 'http://moodle.prod';
    $CFG->local_envbar_items = array(
        array(
            'colourbg' => 'black',
            'colourtext' => 'white',
            'matchpattern' => 'http://moodle.local',
            'showtext' => 'Localhost environment',
        ),
        array(
            'colourbg' => 'green',
            'colourtext' => 'black',
            'matchpattern' => 'http://moodle.staging',
            'showtext' => 'Staging environment',
        ),
    );
```

The colours available are,

    black
    white
    red
    green
    seagreen
    yellow
    brown
    blue
    slateblue
    chocolate
    crimson
    orange
    darkorange

# Details

Upon first installation you will see a notification across the screen that prodwwwroot has not been set.

Please set this value to be exactly what your production ```$CFG->wwwroot``` is.

An extra div will be printed within standard_top_of_body_html function call:

```
$OUTPUT->standard_top_of_body_html()
```

