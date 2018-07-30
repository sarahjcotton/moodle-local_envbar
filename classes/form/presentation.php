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

namespace local_envbar\form;

use local_envbar\local\envbarlib;
use moodleform;

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.'); // It must be included from a Moodle page.
}

/**
 * Form for configuring the presentation of the envbar.
 *
 * @copyright Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class presentation extends moodleform {

    /**
     * {@inheritDoc}
     * @see moodleform::definition()
     */
    public function definition() {
        global $CFG, $PAGE;

        $mform = $this->_form;

        $config = get_config('local_envbar');

        $mform->addElement('textarea', 'extracss', get_string('extracss', 'local_envbar'), 'wrap="virtual" rows="5" cols="50"');
        $mform->addHelpButton('extracss', 'extracss', 'local_envbar');
        if (isset($config->extracss)) {
            $mform->setDefault('extracss', $config->extracss);
        } else {
            $mform->setDefault('extracss', envbarlib::get_default_extra_css());
        }

        $mform->addElement('text', 'menuselector', get_string('menuselector', 'local_envbar'),
                array('placeholder' => '.usermenu .menu'));
        $mform->setType("menuselector", PARAM_RAW);
        $mform->addHelpButton('menuselector', 'menuselector', 'local_envbar');
        if (isset($config->menuselector)) {
            $mform->setDefault('menuselector', $config->menuselector);
        } else {
            $mform->setDefault('menuselector', '.usermenu .menu');
        }

        $mform->addElement('text', 'dividerselector', get_string('dividerselector', 'local_envbar'),
                array('placeholder' => 'filler'));
        $mform->setType("dividerselector", PARAM_RAW);
        $mform->addHelpButton('dividerselector', 'dividerselector', 'local_envbar');
        if (isset($config->dividerselector)) {
            $mform->setDefault('dividerselector', $config->dividerselector);
        } else {
            $mform->setDefault('dividerselector', 'filler');
        }

        $mform->addElement('advcheckbox',
                'highlightlinks',
                get_string('highlightlinks', 'local_envbar'),
                get_string('enable', 'core'));
        $mform->addHelpButton('highlightlinks', 'highlightlinks', 'local_envbar');
        if (isset($config->highlightlinks)) {
            $mform->setDefault('highlightlinks', $config->highlightlinks);
        } else {
            $mform->setDefault('highlightlinks', true);
        }

        $mform->addElement('advcheckbox',
                'highlightlinksenvbar',
                get_string('highlightlinksenvbar', 'local_envbar'),
                get_string('enable', 'core'));
        $mform->addHelpButton('highlightlinksenvbar', 'highlightlinksenvbar', 'local_envbar');
        if (isset($config->highlightlinksenvbar)) {
            $mform->setDefault('highlightlinksenvbar', $config->highlightlinksenvbar);
        } else {
            $mform->setDefault('highlightlinksenvbar', true);
        }
        $mform->disabledIf('highlightlinksenvbar', 'highlightlinks');

        $mform->addElement('advcheckbox',
                'showconfiglink',
                get_string('showconfiglink', 'local_envbar'),
                get_string('enable', 'core'));
        $mform->addHelpButton('showconfiglink', 'showconfiglink', 'local_envbar');
        if (isset($config->showconfiglink)) {
            $mform->setDefault('showconfiglink', $config->showconfiglink);
        } else {
            $mform->setDefault('showconfiglink', true);
        }

        $mform->addElement('text', 'stringseparator', get_string('stringseparator', 'local_envbar'),
                array('placeholder' => '-'));
        $mform->setType("stringseparator", PARAM_CLEANHTML);
        $mform->addHelpButton('stringseparator', 'stringseparator', 'local_envbar');
        if (isset($config->stringseparator)) {
            $mform->setDefault('stringseparator', $config->stringseparator);
        } else {
            $mform->setDefault('stringseparator', '-');
        }

        $this->add_action_buttons();
    }
}

