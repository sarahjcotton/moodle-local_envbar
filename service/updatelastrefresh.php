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

$hash = isset($_POST['hash']) ? $_POST['hash'] : null;
$lastrefresh = isset($_POST['lastrefresh']) ? $_POST['lastrefresh'] : null;

$response = array();

if (is_null($hash) || is_null($lastrefresh)) {
	$response['result'] = 'missing_required_parameter';
	$response['message'] = 'A required parameter was missing.';
	echo json_encode($response);
	return;
}

// Check if any environments match the hash passed.
$records = envbarlib::get_records();

foreach ($records as $env) {
    $envhash = md5($env->matchpattern . $env->showtext);

    if ($envhash === $hash) {
        $data = $env;
        break;
    }
}

if (isset($data)) {
    $data->lastrefresh = $lastrefresh;
    envbarlib::update_envbar($data);

    $response['result'] = 'success';
	$response['message'] = 'The lastrefresh time has been updated.';
} else {
    $response['result'] = 'no_match';
	$response['message'] = 'The hash provided did not match an existing env.';
}

echo json_encode($response);