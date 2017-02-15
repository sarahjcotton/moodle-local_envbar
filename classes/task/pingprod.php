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
namespace local_envbar\task;

use local_envbar\local\envbarlib;
use moodle_url;

/**
 * Task for updating prod with the env lastrefresh.
 *
 * @copyright Catalyst IT
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class pingprod extends \core\task\scheduled_task {

    /**
     * Get task name
     */
    public function get_name() {
        return get_string('pluginname', 'local_envbar');
    }

    /**
     * Execute task
     */
    public function execute() {
        global $CFG;

        $config = get_config('local_envbar');
        $prodwwwroot = envbarlib::getprodwwwroot();

        // Skip if prodwwwroot hasn't been set.
        if (empty($prodwwwroot)) {
            return;
        }

        // Skip if we are we on the production env.
        if ($prodwwwroot === $CFG->wwwroot) {
            return;
        }

        // Skip if we've already pinged prod after the last refresh.
        $lastrefresh = isset($config->prodlastcheck) ? $config->prodlastcheck : 0;
        $lastping = isset($config->prodlastping) ? $config->prodlastping : 0;
        if ($lastrefresh < $lastping) {
            return;
        }

        // Ping prod with the env and lastrefresh.
        $url = $prodwwwroot."/local/envbar/service/updatelastrefresh.php";
        $params = "wwwroot=".urlencode($CFG->wwwroot)."&lastrefresh=".urlencode($lastrefresh)."&secretkey=".urlencode($config->secretkey);

        require_once($CFG->dirroot . "/lib/filelib.php");
        $curl = new \curl();

        try {
            $response = $curl->post($url, $params);
        } catch (Exception $e) {
            mtrace("Error contacting production, error returned was: ".$e->getMessage());
            return;
        }

        $response = json_decode($response);

        if ($resposne->result === 'success') {
            mtrace($response->message);
            set_config('prodlastping', time(), 'local_envbar');
        } else {
            mtrace("Error, the lastrefresh was no updated: ".$response->message);
        }
    }
}
