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

require_once(dirname(__FILE__) . '/../config.php');
require_once($CFG->dirroot . '/local/envbar/lib.php');
require_once($CFG->dirroot . '/local/envbar/config.php');
require_once($CFG->dirroot . '/local/envbar/config_form.php');

class local_envbar_test extends advanced_testcase{

    public function test_factory_empty_records() {
        $this->assertCount(3, envbar_config_set_factory::instances(), 'Factory does not return 3 records');
    }

    public function test_configmodel(){
        global $ENVBAR_COLOR_CHOICES;

            $set = new envbar_config_set();
        foreach(array(
            'id' => array(4,5),
            'colorbg' => $ENVBAR_COLOR_CHOICES,
            'colortext' => $ENVBAR_COLOR_CHOICES,
            'matchpattern' => array('some text', 'another text'),
            'showtext' => array('some text', 'another text'),
            'enabled' => array(0, 1, 2)
                ) as $attr => $goodValues) {
            foreach($goodValues as $testValue) {
                $set->$attr = $testValue;
                $this->assertEquals($testValue, $set->$attr);
            }
        }

        foreach(array(
                    'id' => array('qwe', 'zxc', -14),
                    'colorbg' => array('#qwe', '#cccccc'),
                    'colortext' => array('#qwe', '#cccccc'),
                ) as $attr => $badValues) {
            foreach($badValues as $testValue) {
                $set->$attr = $testValue;
                $this->assertNotEquals($testValue, $set->$attr, $attr);
            }
        }

    }


}
