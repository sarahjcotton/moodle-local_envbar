[![Build Status](https://travis-ci.org/nhoobin/moodle-local_envbar.svg?branch=master)](https://travis-ci.org/nhoobin/moodle-local_envbar)

Environmental bar plugin
====================

This plugin can show a fixed div with custom text according to the URL.

This is useful with development and production for identifying which server you currently reside on based on the sites URL.

# Installation

Add the plugin to /local/envbar/

Run the Moodle upgrade.

# Setup

The plugin can be configured via,
    `(Site administration > Development > Environment bar)`

Text, backgound-color and text color can be customised.


# Details 

An extra div will be printed within standard_top_of_body_html function call:
$OUTPUT->standard_top_of_body_html()
