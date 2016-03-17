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
 * @author    Nicholas Hoobin <nicholashoobin@catalyst-au.net>
 * @copyright Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->libdir . "/formslib.php");
require_once(dirname(__FILE__)."/locallib.php");

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
            "black" => "black",
            "white" => "white",
            "red" => "red",
            "green" => "green",
            "seagreen" => "seagreen",
            "yellow" => "yellow",
            "brown" => "brown",
            "blue" => "blue",
            "slateblue" => "slateblue",
            "chocolate" => "chocolate",
            "crimson" => "crimson",
            "orange" => "orange",
            "darkorange" => "darkorange",
        );

        $mform = $this->_form;
        $records = $this->_customdata["records"];
        $rcount = count($records);

        foreach ($records as $record) {
            $id = $record->id;

            $mform->addElement(
                "hidden",
                "id[{$id}]",
                $id
            );

            $backgroundcolour = $mform->addElement(
                "select",
                "colourbg[{$id}]",
                get_string("bgcolour", "local_envbar"),
                $envbarcolourchoices
            );

            $textcolour = $mform->addElement(
                "select",
                "colourtext[{$id}]",
                get_string("textcolour", "local_envbar"),
                $envbarcolourchoices
            );

            $mform->addElement(
                "text",
                "matchpattern[{$id}]",
                get_string("urlmatch", "local_envbar"),
                array("placeholder" => get_string("urlmatchplaceholder", "local_envbar"))
            );

            $mform->addElement(
                "text",
                "showtext[{$id}]",
                get_string("showtext", "local_envbar"),
                array("placeholder" => get_string("showtextplaceholder", "local_envbar"))
            );

            $mform->addElement(
                "advcheckbox",
                "enabled[{$id}]",
                get_string("setenabled", "local_envbar"),
                get_string("setenabledtext", "local_envbar"),
                array(),
                array(0, 1)
            );

            $mform->addElement(
                "advcheckbox",
                "delete[{$id}]",
                get_string("setdeleted", "local_envbar"),
                get_string("setdeletedtext", "local_envbar"),
                array(),
                array(0, 1)
            );

            $mform->addElement("html", "<br />");

            $mform->setType("id[{$id}]", PARAM_INT);
            $mform->setType("matchpattern[{$id}]", PARAM_URL);
            $mform->setType("showtext[{$id}]", PARAM_TEXT);

            $mform->setDefault("id[{$id}]", $record->id);
            $mform->setDefault("matchpattern[{$id}]", $record->matchpattern);
            $mform->setDefault("showtext[{$id}]", $record->showtext);
            $mform->setDefault("enabled[{$id}]", $record->enabled ? 1 : 0);
            $mform->setDefault("delete[{$id}]", 0);

            $textcolour->setSelected($record->colourtext);
            $backgroundcolour->setSelected($record->colourbg);
        }

        // Now we set up the same fields to repeat and add elements.
        if ($rcount == 0) {
            $repeatnumber = 1;
        } else {
            $repeatnumber = 0;
        }

        $repeatarray = array();

        $repeatarray[] = $mform->createElement(
            "hidden",
            "id"
        );

        $repeatarray[] = $mform->createElement(
            "select",
            "colourbg",
            get_string("bgcolour", "local_envbar"),
            $envbarcolourchoices
        );

        $repeatarray[] = $mform->createElement(
            "select",
            "colourtext",
            get_string("textcolour", "local_envbar"),
            $envbarcolourchoices
        );

        $repeatarray[] = $mform->createElement(
            "text",
            "matchpattern",
            get_string("urlmatch", "local_envbar"),
            array("placeholder" => get_string("urlmatchplaceholder", "local_envbar"))
        );

        $repeatarray[] = $mform->createElement(
            "text",
            "showtext",
            get_string("showtext", "local_envbar"),
            array("placeholder" => get_string("showtextplaceholder", "local_envbar"))
        );

        $repeatarray[] = $mform->createElement(
            "advcheckbox",
            "enabled",
            get_string("setenabled", "local_envbar"),
            get_string("setenabledtext", "local_envbar"),
            array(),
            array(0, 1)
        );

        $repeatarray[] = $mform->createElement(
            "advcheckbox",
            "delete",
            get_string("setdeleted", "local_envbar"),
            get_string("setdeletedtext", "local_envbar"),
            array(),
            array(0, 1)
        );

        $repeatarray[] = $mform->addElement("html", "<br />");

        $repeatoptions = array();
        $repeatoptions["id"]["default"] = "{no}";
        $repeatoptions["id"]["type"] = PARAM_INT;

        $repeatoptions["colourbg"]["default"] = "black";
        $repeatoptions["colourtext"]["default"] = "white";

        $repeatoptions["matchpattern"]["default"] = "";
        $repeatoptions["matchpattern"]["type"] = PARAM_URL;

        $repeatoptions["showtext"]["default"] = "";
        $repeatoptions["showtext"]["type"] = PARAM_TEXT;

        $this->repeat_elements($repeatarray, $repeatnumber, $repeatoptions, "repeats", "envbar_add", 1, null, false);

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

