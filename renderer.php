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
     *
     * @param stdObj $match And environment to show
     * @param boolean $static should this bar be fixed to the header
     */
    public function render_envbar($match, $fixed = true) {

        $css = <<<EOD
.envbar {
    padding: 15px;
    width: 100%;
    height: 20px;
    text-align: center;
    margin-bottom: 10px;
}
.envbar.env{$match->id},
.envbar.env{$match->id} a {
    background: {$match->colourbg};
    color: {$match->colourtext};
}
.envbar.env{$match->id} a {
    text-decoration: underline;
}
.envbar.fixed {
    position: fixed;
    top: 0px;
    left: 0px;
    z-index: 9999;
}
.debuggingmessage {
    padding-top: 50px;
}
.debuggingmessage ~ .debuggingmessage {
    padding-top: 0px;
}
EOD;
        if ($fixed) {
            $css .= <<<EOD
.navbar.navbar-fixed-top {
    top: 50px;
}
EOD;
        }

        $class = 'env' .  $match->id;
        $class .= $fixed ? ' fixed' : '';

        $showtext = htmlspecialchars($match->showtext);

        $produrl = local_envbar_getprodwwwroot();
        $systemcontext = context_system::instance();
        $canedit = has_capability('moodle/site:config', $systemcontext);
        if ($canedit) {
            if ($produrl) {
                $showtext .= ' - ' . html_writer::link($produrl.'/local/envbar/index.php',
                        get_string('configureinprod', 'local_envbar'), array('target' => 'prod'));
            } else {
                $showtext .= ' - ' . html_writer::link(new moodle_url('/local/envbar/index.php'),
                        get_string('configurehere', 'local_envbar'));
            }
        }

        $html = <<<EOD
<div class="envbar $class">$showtext</div>
<style>
$css
</style>
EOD;
        if ($fixed) {
            $html .= <<<EOD
<div style="height: 50px;">&nbsp;</div>
EOD;
        }

        return $html;
    }

}

