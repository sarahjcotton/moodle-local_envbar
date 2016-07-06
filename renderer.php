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
 * Renderer
 *
 * @package   local_envbar
 * @author    Brendan Heywoody (brendan@catalyst-au.net)
 * @copyright Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.'); // It must be included from a Moodle page.
}

/**
 * Renderer for envbar.
 *
 * @copyright  2016 Brendan Heywoody (brendan@catalyst-au.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_envbar_renderer extends plugin_renderer_base {

    /**
     * Render the envbar
     * @param array $data
     */
    public function render_envbar($match) {

        $html = <<<EOD
<div class="envbar">{$match->showtext}</div>
<style>
.envbar {
    position: fixed;
    padding: 15px;
    width: 100%;
    height: 20px;
    top: 0px;
    left: 0px;
    z-index: 9999;
    text-align: center;
    background: {$match->colourbg};
    color: {$match->colourtext};
}
.navbar.navbar-fixed-top {
    top: 50px;
}
.debuggingmessage {
    padding-top: 50px;
}
.debuggingmessage ~ .debuggingmessage {
    padding-top: 0px;
}
</style>
<div style="height: 50px;">&nbsp;</div>
EOD;

        return $html;
    }

}

