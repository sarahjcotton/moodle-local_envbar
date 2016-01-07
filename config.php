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


class EnvbarConfigSet {
    protected $_params ;
    protected $_db_exists = false;

    const TABLE = 'local_envbar_configset';

    public function __construct($params = array(), $db_exists = false){
        $this->_db_exists = $db_exists;
        $this->_params = array(
            'id' => 0,
            'color_bg' => 'black',
            'color_text' => 'white',
            'match_pattern' => '',
            'show_text' => '',
            'enabled' => 0
        );
        if($params instanceof stdClass){
            foreach(array_keys($this->_params) as $key) {
                $this->_params[$key] = $params->$key;
            }
        } else {
            foreach(array_keys($this->_params) as $key) {
                if(isset($params[$key])) {
                    $this->_params[$key] = $params[$key];
                }
            }
        }

    }

    public function __get($name)
    {
        return $this->_params[$name];
    }

    public function __set($name, $value)
    {
        global $CFG;
        switch($name){
            case 'id':
            case 'enabled':
                $value = intval($value);
                break;
            case 'color_bg':
            case 'color_text':
                if(!in_array($value, $CFG->__ENVBAR_COLOR_CHOICES))
                    return false;
                break;
            case 'match_pattern':
            case 'show_text':
                if(strlen($value) > 255)
                    return false;
                break;
            default:
                return false;
        }
        $this->_params[$name] = $value;
    }

    public function is_valid(){
        return ($this->match_pattern != '' && $this->id > 0);
    }

    public function get_params(){
        return $this->_params;
    }

    public function save($DB){
        try {

            if($this->match_pattern == '' && $this->_db_exists){
                $DB->delete_records(self::TABLE, array('id' => $this->id));
            }
            elseif($this->is_valid()) {
                if ($this->_db_exists) {
                    $DB->update_record(self::TABLE, arr_to_std($this->get_params()));
                } else {
                    $DB->insert_record(self::TABLE, arr_to_std($this->get_params()));

                }
            }
        }
        catch(Exception $e){
            error_log(var_export($e, true));
        }
    }
}

function arr_to_std($array){
    $result = new stdClass();
    foreach($array as $key => $value){
        $result->$key = $value;
    }
    return $result;
}


class EnvbarConfigSetFactory {
    /**
     * @return array records from DB + 3 empty records
     */
    public static function instances(){
        global $DB;
        $result = array();

        $records = $DB->get_records('local_envbar_configset', array(), 'id asc');
        $max_id = 0;

        foreach($records as $id => $set){
            $result [$id]= new EnvbarConfigSet($set, true);
            $max_id = max($max_id, $set->id);
        }

        for($i = 1; $i <= 3; $i++){
            $result [$i + $max_id] = self::newRecord($i + $max_id);
        }


        return $result;
    }

    /**
     * @param int $id attribute of new record
     * @return EnvbarConfigSet empty object
     */
    public static function newRecord($id = 0){
       return new EnvbarConfigSet(array('id' => $id));
    }
}