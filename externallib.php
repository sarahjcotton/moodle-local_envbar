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
 * Form for editing a configuration of the status bar
 *
 * @package   local_envbar
 * @author    Rossco Hellmans <rosscohellmans@catalyst-au.net>
 * @copyright Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_envbar\local\envbarlib;

require_once($CFG->libdir . "/externallib.php");
 
/**
 * Web service API to update env lastrefresh
 *
 * @copyright Catalyst IT
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_envbar_external extends external_api {
 
    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function update_last_refresh_parameters() {
        return new external_function_parameters(
                array(
                        'hash' => new external_value(PARAM_ALPHANUM, 'A hash of the matchpattern and showtext.'),
                        'lastrefresh' => new external_value(PARAM_INT, 'An epoch lastrefresh time.'),
                ) 
        );
    }
 
    /**
     * Update the lastrefresh time for the environment.
     * @return string A successful message if the lastrefresh was updated
     */
    public static function update_last_refresh($hash, $lastrefresh) {
        global $DB;

        // Parameters validation.
        $params = self::validate_parameters(
                self::update_last_refresh_parameters(),
                array(
                        'hash' => $hash,
                        'lastrefresh' => $lastrefresh,
                )
        );

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
            $ret = $DB->update_record('local_envbar', $data); 
            
            $message = "gotit" 
        } else {
            $message = "nomatch";
        }
        
        return $message;
    }
 
    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function update_last_refresh_returns() {
        return new external_value(PARAM_TEXT, 'A successful message if the lastrefresh was updated.');
    }
 
}