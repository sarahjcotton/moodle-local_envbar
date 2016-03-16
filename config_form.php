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
 * @author    Grigory Baleevskiy (grigory@catalyst-au.net)
 * @copyright Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->libdir . '/formslib.php');
require_once(dirname(__FILE__).'/locallib.php');

/**
 * Form for editing a Enviromental bar.
 *
 * @copyright Catalyst IT
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_envbar_form extends moodleform {

    /**
     * {@inheritDoc}
     * @see moodleform::definition()
     */
    public function definition() {
        $envbarcolourchoices = array(
            'black' => 'black',
            'white' => 'white',
            'red' => 'red',
            'green' => 'green',
            'seagreen' => 'seagreen',
            'yellow' => 'yellow',
            'brown' => 'brown',
            'blue' => 'blue',
            'slateblue' => 'slateblue',
            'chocolate' => 'chocolate',
            'crimson' => 'crimson',
            'orange' => 'orange',
            'darkorange' => 'darkorange',
        );

        $mform = $this->_form;

        $repeatarray = array();
        $repeatnumber = 3;

        $repeatarray[] = $mform->createElement('select',
            'colourbg',
            get_string('bgcolour', 'local_envbar'),
            $envbarcolourchoices
        );

        $repeatarray[] = $mform->createElement('select',
            'colourtext',
            get_string('textcolour', 'local_envbar'),
            $envbarcolourchoices
        );

        $repeatarray[] = $mform->createElement(
            'text',
            'matchpattern',
            get_string('urlmatch', 'local_envbar'),
            array('placeholder' => get_string('urlmatchplaceholder', 'local_envbar'))
        );

        $repeatarray[] = $mform->createElement(
            'text',
            'showtext',
            get_string('showtext', 'local_envbar'),
            array('placeholder' => get_string('showtextplaceholder', 'local_envbar'))
        );

        $repeatarray[] = $mform->addElement('html', '<br />');

        $repeatoptions = array();
        $repeatoptions['bgcolour']['default'] = 0;
        $repeatoptions['bgcolour']['type'] = PARAM_INT;

        $repeatoptions['textcolour']['default'] = 0;
        $repeatoptions['textcolour']['type'] = PARAM_INT;

        $repeatoptions['matchpattern']['default'] = '';
        $repeatoptions['matchpattern']['type'] = PARAM_URL;

        $repeatoptions['showtext']['default'] = '';
        $repeatoptions['showtext']['type'] = PARAM_TEXT;

        $this->repeat_elements($repeatarray, $repeatnumber, $repeatoptions, 'repeats', 'envbar_add', 1, null, false);

        $this->add_action_buttons();
    }

    /**
     * Returns an array with fields that are invalid while creating a new QR link.
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK (true allowed for backwards compatibility too).
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
    }
}

