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
 * Environment bar setup page.
 *
 * @package   local_envbar
 * @author    Grigory Baleevskiy (grigory@catalyst-au.net)
 * @author    Nicholas Hoobin <nicholashoobin@catalyst-au.net>
 * @copyright Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_envbar\local\envbarlib;

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

global $DB;

admin_externalpage_setup('local_envbar_lastrefresh');

$config = get_config('local_envbar');
$form = new \local_envbar\form\lastrefresh(null, array('prodlastcheck' => $config->prodlastcheck));

if ($data = $form->get_data()) {
    set_config('prodlastcheck', $data->lastrefresh, 'local_envbar');

    // Check if we want to ping prod.
    $verbose = isset($data->verbose);
    if (isset($data->pingprod)) {
        ob_start();
        envbarlib::pingprod($verbose);
        $debug = ob_get_contents();
        ob_clean();
    } else {
        redirect(new moodle_url('/local/envbar/last_refresh.php'), get_string('changessaved'));
    }

}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('header_envbar', 'local_envbar'));
echo $form->display();
if (isset($debug)) {
    // The curl debug is a var_dump of the variables, so we need to do a regex to retrieve them.
    $commonstr = "<font color='#888a85'>=&gt;<\/font> <small>string<\/small> <font color='#cc0000'>'";
    $commonint = "<font color='#888a85'>=&gt;<\/font> <small>int<\/small> <font color='#4e9a06'>";
    $matches = array();

    $regex = "/'http_code' {$commonint}(.*?)<\/font>/s";
    $preg = preg_match($regex, $debug, $matches);
    $httpcode = $matches[1];
    echo "<h1>HTTP code</h1>";
    echo "<pre>{$httpcode}</pre>";

    $regex = "/'CURLOPT_URL' {$commonstr}(.*?)'/s";
    $preg = preg_match($regex, $debug, $matches);
    $curlurl = $matches[1];
    $regex = "/'CURLOPT_POSTFIELDS' {$commonstr}(.*?)'/s";
    $preg = preg_match($regex, $debug, $matches);
    $postfields = $matches[1];

    echo "<h1>Curl command</h1>";
    echo "<pre>curl \"{$curlurl}?{$postfields}\"</pre>";

    echo $debug;
}
echo $OUTPUT->footer();

