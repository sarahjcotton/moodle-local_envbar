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
 * Environment bar settings.
 *
 * @package   local_envbar
 * @author    Grigory Baleevskiy (grigory@catalyst-au.net)
 * @author    Nicholas Hoobin <nicholashoobin@catalyst-au.net>
 * @copyright Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if ($hassiteconfig) {

    $ADMIN->add('localplugins', new admin_category('envbar', get_string('pluginname', 'local_envbar')));

    $envsettings = new admin_externalpage('local_envbar_settings',
        get_string('menuenvsettings', 'local_envbar'),
        new moodle_url('/local/envbar/index.php'));

    $lastrefresh = new admin_externalpage('local_envbar_lastrefresh',
        get_string('menulastrefresh', 'local_envbar'),
        new moodle_url('/local/envbar/last_refresh.php'));

    $presentation = new admin_externalpage('local_envbar_presentation',
            get_string('menupresentation', 'local_envbar'),
            new moodle_url('/local/envbar/presentation.php'));

    $ADMIN->add('envbar', $envsettings);
    $ADMIN->add('envbar', $lastrefresh);
    $ADMIN->add('envbar', $presentation);

    $settings = null;
}
