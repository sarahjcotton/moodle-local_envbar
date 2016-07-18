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
 * Environment bar config.
 *
 * @package   local_envbar
 * @author    Grigory Baleevskiy (grigory@catalyst-au.net)
 * @author    Nicholas Hoobin <nicholashoobin@catalyst-au.net>
 * @copyright Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.'); // It must be included from a Moodle page.
}

/**
 * Helper function to update data, or insert if it does not exist.
 * @param stdClass $data
 * @return boolean|number return value for the update
 */
function update_envbar($data) {
    global $DB;

    $data = base64_encode_record($data);

    if (isset($data->id)) {
        $ret = $DB->update_record('local_envbar', $data);
    } else {
        // No id exists, lets insert it!
        $ret = $DB->insert_record('local_envbar', $data);
        $data->id = $ret;
    }

    $cache = cache::make('local_envbar', 'records');
    $cache->delete('records');

    return $ret;
}

/**
 * Helper function to delete data.
 * @param int $id
 * @return boolean|number return value for the delete
 */
function delete_envbar($id) {
    global $DB;

    // The cache is assumed to be initialised as it is created in envbar_get_records.
    $cache = cache::make('local_envbar', 'records');
    $cache->delete('records');

    $ret = $DB->delete_records('local_envbar', array('id' => $id));
    return $ret;
}

/**
 * Helper function to base64 decode the matchpattern and showtext fields.
 * @param array $data
 * @return array $data
 */
function base64_decode_records($data) {
    foreach ($data as $record) {
        $record->matchpattern = base64_decode($record->matchpattern);
        $record->showtext = base64_decode($record->showtext);
    }
    return $data;
}

/**
 * Helper function to base64 encode the matchpattern and showtext fields.
 * @param array $data
 * @return array $data
 */
function base64_encode_record($data) {
    $data->matchpattern = base64_encode($data->matchpattern);
    $data->showtext = base64_encode($data->showtext);

    return $data;
}

/**
 * Find all configured environment sets
 *
 * @return array of env set records
 */
function envbar_get_records() {
    global $DB, $CFG;

    try {
        $cache = cache::make('local_envbar', 'records');
    } catch (Exception $e) {
        throw $e;
    }

    if (!$result = $cache->get('records')) {
        $result = $DB->get_records('local_envbar');
        // The data for the records is obfuscated using base64 to avoid the chance
        // of the data being 'cleaned' using either the core DB replace script, or
        // the local_datacleaner plugin, which would render this plugin useless.
        $result = base64_decode_records($result);
        $cache->set('records', $result);
    }

    // Add forced local envbar items from config.php.
    if (!empty($CFG->local_envbar_items)) {
        $items = $CFG->local_envbar_items;

        // Converting them to stdClass and adding a local flag.
        foreach ($items as $key => $value) {
            $value['local'] = true;
            $items[$key] = (object) $value;
        }

        $result = array_merge($items, $result);
    }

    return $result;
}

/**
 * Check if provided value matches provided pattern.
 *
 * @param string $value A value to check.
 * @param string $pattern A pattern to check matching against.
 *
 * @return bool True or false.
 */
function local_envbar_is_match($value, $pattern) {

    if (empty($pattern)) {
        return false;
    }

    $keywords = array('\\', '/', '-', '.', '?', '*', '+', '^', '$');

    foreach ($keywords as $keyword) {
        // Escape special a keyword to treat it as a part of the string.
        $pattern = str_replace($keyword, '\\' . $keyword, $pattern);
    }

    if (preg_match('/' . $pattern . '/', $value)) {
        return true;
    }

    return false;
}

/**
 * Helper inject function that is used in local_envbar_extend_navigation.
 */
function local_envbar_inject() {
    global $CFG, $PAGE;

    require_once('renderer.php');

    // During the initial install we don't want to break the admin gui.
    try {
        $envs = envbar_get_records();
    } catch (Exception $e) {
        return;
    }

    // Are we on the production env?
    if (local_envbar_getprodwwwroot() === $CFG->wwwroot) {
        return;
    }

    $match = null;

    $here = (new moodle_url('/'))->out();

    // Which env matches?
    foreach ($envs as $env) {
        if (local_envbar_is_match($here, $env->matchpattern)) {
            $match = $env;
            break;
        }
    }

    // If we stil don't have a match then show a default warning.
    if (empty($match)) {
        $match = (object) array(
            'id' => 0,
            'showtext' => get_string('notconfigured', 'local_envbar'),
            'colourtext' => 'white',
            'colourbg' => 'red',
        );

    }

    // TODO When run from unit tests we can't use a renderer.
    if (!PHPUNIT_TEST) {
        $renderer = $PAGE->get_renderer('local_envbar');
        $html = $renderer->render_envbar($match);
        $CFG->additionalhtmltopofbody .= $html;
    }

}

/**
 * Gets the prodwwwroot.
 * This also base64_dencodes the value to obtain it.
 *
 * @return string $prodwwwroot if it is set either in plugin config via UI or
 *         in config.php. Returns nothing if prodwwwroot is net set anywhere.
 */
function local_envbar_getprodwwwroot() {
    global $CFG;

    $prodwwwroot = base64_decode(get_config("local_envbar", "prodwwwroot"));

    if (!empty($CFG->local_envbar_prodwwwroot)) {
        $prodwwwroot = $CFG->local_envbar_prodwwwroot;
    }

    if ($prodwwwroot) {
        return $prodwwwroot;
    }
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

