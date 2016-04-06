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
 * Extracted from locallib.php, as it is not called first.
 * @param array $array array of query parameters
 * @return array $result array of records
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
    if (isset($CFG->local_envbar_items)) {
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

    if (isset($array)) {
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
        $records = envbar_get_records(array('enabled' => 1));
    } catch (Exception $e) {
        return;
    }
    foreach ($records as $set) {
        $showtext = htmlspecialchars($set->showtext);
        $additionalhtml = <<<EOD
<div style="position:fixed; padding:15px; width:100%; top:0px; left:0px; z-index:9999;background-color:{$set->colourbg}; color:{$set->colourtext}">{$showtext}</div>
<style>
.navbar-fixed-top {
    top:50px !important;
}
.debuggingmessage {
    padding-top:50px;
}
.debuggingmessage ~ .debuggingmessage {
    padding-top:0px;
}
</style>
<div style="height:50px;"> &nbsp;</div>
EOD;
        if (!empty($set->matchpattern)) {
            if (false !== (strpos($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], $set->matchpattern))) {
                $CFG->additionalhtmltopofbody .= $additionalhtml;
                break;
            }
        }
    }
}

/**
 * lib.php isn't always called, we need to hook something to ensue it runs.
 * @param object $navigation
 * @param object $course
 * @param object $module
 * @param object $cm
 */
function local_envbar_extend_navigation($navigation, $course = null, $module = null, $cm = null) {
    local_envbar_inject();
}

