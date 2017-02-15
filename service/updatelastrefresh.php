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
 * Page that is pinged to update an env lastrefresh time
 *
 * @package   local_envbar
 * @author    Rossco Hellmans <rosscohellmans@catalyst-au.net>
 * @copyright Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_envbar\local\envbarlib;

require_once(dirname(__FILE__) . '/../../../config.php');

$wwwroot = isset($_POST['wwwroot']) ? $_POST['wwwroot'] : null;
$lastrefresh = isset($_POST['lastrefresh']) ? $_POST['lastrefresh'] : null;
$secretkey = isset($_POST['secretkey']) ? $_POST['secretkey'] : null;
$config = get_config('local_envbar');

$response = array();

if ($secretkey !== $config->secretkey) {
    $response['result'] = 'invalid_secretkey';
    $response['message'] = get_string('invalid_secretkey', 'local_envbar');
    echo json_encode($response);
    die;
}

if (is_null($wwwroot) || is_null($lastrefresh)) {
    $response['result'] = 'missing_required_parameter';
    $response['message'] = get_string('missing_required_parameter', 'local_envbar');
    echo json_encode($response);
    die;
}

// Check if any environments match the wwwroot passed.
$records = envbarlib::get_records();
foreach ($records as $env) {
    if (envbarlib::is_match($wwwroot, $env->matchpattern)) {
        $data = $env;
        break;
    }
}

if (isset($data)) {
    $data->lastrefresh = $lastrefresh;
} else {
    $data = new stdClass();
    // ID, $data->id not set.
    $data->matchpattern = $wwwroot;
    $data->lastrefresh = $lastrefresh;
    $data->colourtext = 'white';
    $data->colourbg = 'red';
}

envbarlib::update_envbar($data);
$response['result'] = 'success';
$response['message'] = get_string('lastrefresh_success', 'local_envbar');

echo json_encode($response);
