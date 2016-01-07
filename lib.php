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
 *
 * @package local_envbar
 * @copyright 2016 Catalyst
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__).'/config.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    global $DB;
    $records = $DB->get_records(envbar_config_set::TABLE, array('enabled' => 1));
    foreach ($records as $set) {
        if (false !== (strpos($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], $set->matchpattern))) {
            $CFG->additionalhtmltopofbody .=
                '<div style="position:fixed; padding:15px; width:100%; top:0px; left:0px; z-index:9999;background-color:'
                .$set->colorbg.'; color:'.$set->colortext.'">'
                .htmlspecialchars($set->showtext).'</div>'
                .'<style>.navbar-fixed-top {top:50px !important;}</style>'
                .'<div style="height:50px;"> &nbsp;</div>';
            break;
        }
    }
}


function local_envbar_extends_navigation() {

}

function local_envbar_extends_settings_navigation($settingsnav, $context) {
}

