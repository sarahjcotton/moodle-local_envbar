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

global $CFG;

define('PLUGIN_NAME_ENVBAR', 'local_envbar');

$CFG->__ENVBAR_COLOR_CHOICES = array(
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


class envbar_config_set {
    protected $params;
    protected $dbexists = false;

    const DB_TABLE = 'local_envbar_configset';

    public function __construct($params = array(), $dbexists = false) {
        $this->dbexists = $dbexists;
        $this->params = array(
            'id' => 0,
            'colorbg' => 'black',
            'colortext' => 'white',
            'matchpattern' => '',
            'showtext' => '',
            'enabled' => 0
        );
        if ($params instanceof stdClass) {
            foreach (array_keys($this->params) as $key) {
                $this->params[$key] = $params->$key;
            }
        } else {
            foreach (array_keys($this->params) as $key) {
                if (isset($params[$key])) {
                    $this->params[$key] = $params[$key];
                }
            }
        }
    }

    public function __get($name) {
        return $this->params[$name];
    }

    public function __set($name, $value) {
        global $CFG;
        switch($name) {
            case 'id':
            case 'enabled':
                $value = intval($value);
                break;
            case 'colorbg':
            case 'colortext':
                if (!in_array($value, $CFG->__ENVBAR_COLOR_CHOICES)) {
                    return false;
                }
                break;
            case 'matchpattern':
            case 'showtext':
                if (strlen($value) > 255) {
                    return false;
                }
                break;
            default:
                return false;
        }
        $this->params[$name] = $value;
    }

    public function is_valid() {
        return ($this->matchpattern != '' && $this->id > 0);
    }

    public function get_params() {
        return $this->params;
    }

    public function save($DB) {
        if ($this->matchpattern == '' && $this->dbexists) {
            $DB->delete_records(self::DB_TABLE, array('id' => $this->id));
        } else if ($this->is_valid()) {
            if ($this->dbexists) {
                $DB->update_record(self::DB_TABLE, (object) $this->get_params());
            } else {
                $DB->insert_record(self::DB_TABLE, (object) $this->get_params());
            }
        }
    }
}

class envbar_config_set_factory {
    /**
     * @return array records from DB + 3 empty records
     */
    public static function instances() {
        global $DB;
        $result = array();

        $records = $DB->get_records('local_envbar_configset', array(), 'id asc');
        $maxid = 0;

        foreach ($records as $id => $set) {
            $result [$id] = new envbar_config_set($set, true);
            $maxid = max($maxid, $set->id);
        }

        for ($i = 1; $i <= 3; $i++) {
            $result [$i + $maxid] = self::new_record($i + $maxid);
        }
        return $result;
    }

    /**
     * @param int $id attribute of new record
     * @return envbar_config_set empty object
     */
    public static function new_record($id = 0) {
        return new envbar_config_set(array('id' => $id));
    }
}