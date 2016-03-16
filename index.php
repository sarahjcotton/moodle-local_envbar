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

require_once(dirname(__FILE__) . '/../../config.php');
require_once(dirname(__FILE__).'/locallib.php');
require_once(dirname(__FILE__) . '/config_form.php');
require_once($CFG->libdir . '/adminlib.php');

admin_externalpage_setup('local_envbar');

$form = new local_envbar_form();

if ($data = $form->get_data()) {
    $repeats = $data->repeats;

    for ($id = 0; $id < $repeats; $id++) {
        $colourbg = $data->colourbg[$id];
        $colourtext = $data->colourtext[$id];
        $matchpattern = $data->matchpattern[$id];
        $showtext = $data->showtext[$id];
    }

    redirect(new moodle_url('/local/envbar/index.php'));
}

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('header_envbar', 'local_envbar'));

echo $form->display();

echo $OUTPUT->footer();
