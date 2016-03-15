[![Build Status](https://travis-ci.org/nhoobin/moodle-local_envbar.svg?branch=master)](https://travis-ci.org/nhoobin/moodle-local_envbar)

Environment bar - Moodle local plugin
====================

This plugin can show a fixed div with custom text according to the URL.

This is useful with development and production for identifying which server you currently reside on based on the URL.

Principals
----------

Showing what environment you are in needs to be reliable. If it doesn't work
for any reason then you may as well not have it. The way this plugin works is
that in your production system you specify what your different environments
are. Then after a refresh of production data back to a staging environment it
can auto detect that it is no longer in production and warn the end user.
Further more if there isn't any config at all, then it will assume you are in
a development environment.

By doing it this way, by saying 'Are we not in production' vs 'Are we in a
dev environment' the logic because much more resiliant to mistakes, refreshed
databases, and still works even if you forget to do something. Ie it is the
only near perfect fail safe way to detect an environment.

Summary of situations covered:

* In prod, with config, env bar doesn't show

* In non-prod, with refreshed config, env bar shows custom bar

* In non-prod, with zero config, env bar shows generic bar


Installation
------------

Add the plugin to /local/envbar/

Run the Moodle upgrade.

# Setup

The plugin can be configured via,
    `(Site administration > Development > Environment bar)`

Text, backgound-color and text color can be customised.


# Details

An extra div will be printed within standard_top_of_body_html function call:
$OUTPUT->standard_top_of_body_html()
