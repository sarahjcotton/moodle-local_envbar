<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Environment bar library.
 *
 * @package   local_envbar
 * @author    Grigory Baleevskiy (grigory@catalyst-au.net)
 * @author    Nicholas Hoobin <nicholashoobin@catalyst-au.net>
 * @copyright Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Find all configured environment sets
 *
 * @param  array $array array of query parameters
 * @return array of env set records
 */
function envbar_get_records($array = null) {
    global $DB, $CFG;

    try {
        $cache = cache::make('local_envbar', 'records');
    } catch (Exception $e) {
        throw $e;
    }

    if (!$result = $cache->get('records')) {
        $result = $DB->get_records('local_envbar');
        $cache->set('records', $result);
    }

    // Adding manual local envbar items from config.php.
    if (!empty($CFG->local_envbar_items)) {
        $items = $CFG->local_envbar_items;

        // Converting them to stdClass and adding a local flag.
        foreach ($items as $key => $value) {
            $value['local'] = true;
            $value['showtext'] = base64_encode($value['showtext']);
            $value['matchpattern'] = base64_encode($value['matchpattern']);
            $items[$key] = (object) $value;
        }

        $result = array_merge($items, $result);
    }

    foreach ($result as $record) {
        $record->matchpattern = base64_decode($record->matchpattern);
        $record->showtext = base64_decode($record->showtext);
    }

    if (!empty($array)) {
        $query = array();

        foreach ($result as $record) {
            foreach ($array as $key => $value) {
                if (isset($record->{$key})) {
                    if ($record->{$key} == $value) {
                        $query[] = $record;
                    }
                }
            }
        }
        $result = $query;
    }

    return $result;
}

/**
 * Helper inject function that is used in local_envbar_extend_navigation.
 */
function local_envbar_inject() {
    global $DB, $CFG;

    // During the initial install we don't want to break the admin gui.
    try {
        $envs = envbar_get_records(array('enabled' => 1));
    } catch (Exception $e) {
        return;
    }

    $here = (new moodle_url('/'))->out();

    // Are we on the production env?
    if (local_envbar_getprodwwwroot() === $CFG->wwwroot) {
        return;
    }

    $match = null;

    // If not yet configured then show warning:
    if (empty($envs)) {
        $match = (object) array(
            'showtext' => get_string('notconfigured', 'local_envbar'),
            'colourtext' => 'white',
            'colourbg' => 'red',
        );
        $systemcontext = context_system::instance();
        $canedit = has_capability('moodle/site:config', $systemcontext);
        if ($canedit) {
            $match->showtext .= ' - ' . html_writer::link(new moodle_url('/local/envbar/index.php'),
                get_string('configure', 'local_envbar'), array('style' => 'color: white; text-decoration: underline'));
        }

    } else {

        // Which env matches?
        foreach ($envs as $env) {
            if (!empty($env->matchpattern) && strpos($here, $env->matchpattern) == true) {
                $match = $env;
                break;
            }
        }
        if (!$match) {
            return;
        }
        $match->showtext = htmlspecialchars($match->showtext);
    }

    $additionalhtml = <<<EOD
<div class="envbar">{$match->showtext}</div>
<style>
.envbar {
    position: fixed;
    padding: 15px;
    width: 100%;
    height: 20px;
    top: 0px;
    left: 0px;
    z-index: 9999;
    text-align: center;
    background: {$match->colourbg};
    color: {$match->colourtext};
}
.navbar-fixed-top {
    top: 50px !important;
}
.debuggingmessage {
    padding-top: 50px;
}
.debuggingmessage ~ .debuggingmessage {
    padding-top: 0px;
}
</style>
<div style="height: 50px;">&nbsp;</div>
EOD;

    $CFG->additionalhtmltopofbody .= $additionalhtml;

}

/**
 * lib.php isn't always called, we need to hook something to ensure it runs.
 *
 * @param object $navigation
 * @param object $course
 * @param object $module
 * @param object $cm
 */
function local_envbar_extend_navigation($navigation, $course = null, $module = null, $cm = null) {
    local_envbar_inject();
}

/**
 * Gets the prodwwwroot.
 * This also base64_dencodes the value to obtain it.
 *
 * @return string $prodwwwroot
 */
function local_envbar_getprodwwwroot() {
    $prodwwwroot = base64_decode(get_config("local_envbar", "prodwwwroot"));

    if (!empty($CFG->local_envbar_prodwwwroot)) {
        $prodwwwroot = $CFG->local_envbar_prodwwwroot;
    }

    return $prodwwwroot;
}

/**
 * Sets the prodwwwroot.
 * This also base64_encodes the value to prevent datawashing from removing the values.
 *
 * @param string $prodwwwroot
 */
function local_envbar_setprodwwwroot($prodwwwroot) {
    $root = base64_encode($prodwwwroot);
    set_config('prodwwwroot', $root, 'local_envbar');
}

