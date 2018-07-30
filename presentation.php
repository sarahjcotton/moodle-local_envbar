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
 * @copyright Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_envbar\local\envbarlib;

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

global $DB;

admin_externalpage_setup('local_envbar_presentation');

$config = get_config('local_envbar');
$form = new \local_envbar\form\presentation();

if ($data = $form->get_data()) {

    set_config('extracss', $data->extracss, 'local_envbar');
    set_config('menuselector', $data->menuselector, 'local_envbar');
    set_config('dividerselector', $data->dividerselector, 'local_envbar');
    set_config('highlightlinks', $data->highlightlinks, 'local_envbar');
    set_config('highlightlinksenvbar', $data->highlightlinksenvbar, 'local_envbar');

    redirect(new moodle_url('/local/envbar/presentation.php'));
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('header_envbar', 'local_envbar'));
echo $form->display();
echo $OUTPUT->footer();

