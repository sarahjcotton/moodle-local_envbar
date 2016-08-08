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
 * @copyright  2016 Brendan Heywood (brendan@catalyst-au.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_envbar_renderer extends plugin_renderer_base {

    /**
     * Render the envbar
     *
     * @param stdObj $match And environment to show
     * @param boolean $static should this bar be fixed to the header
     */
    public function render_envbar($match, $fixed = true, $envs = array()) {

        $css = <<<EOD
.envbar {
    padding: 15px;
    width: 100%;
    height: 50px;
    text-align: center;
    margin-bottom: 10px;
    box-sizing: border-box;
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
EOD;

        // If passed a list of env's, then for any env in the list which
        // isn't the one we are on, and which isn't production, add some
        // css which highlights broken links which jump between env's.
        foreach ($envs as $env) {
            if ($env->matchpattern != $match->matchpattern) {
                $css .= <<<EOD

a[href^="{$env->matchpattern}"] {
    outline: 2px solid {$env->colourbg};
}
a[href^="{$env->matchpattern}"]::before {
    content: '{$env->showtext}';
    background-color: {$env->colourbg};
    color: white;
    padding: 1px 4px;
}
EOD;
            }
        }


        if ($fixed) {
            $css .= <<<EOD
.navbar.navbar-fixed-top {
    top: 50px;
}
EOD;
        }

        $class = 'env' .  $match->id;
        $class .= $fixed ? ' fixed' : '';

        // Show the configured env message.
        $showtext = htmlspecialchars($match->showtext);

        // Just show the biggest time unit instead of 2.
        $config = get_config('local_envbar');
        if (isset($config->prodlastcheck)) {
            $show = format_time(time() - $config->prodlastcheck);
            $num = strtok($show, ' ');
            $unit = strtok(' ');
            $show = "$num $unit";
            $showtext .= get_string('refreshedago', 'local_envbar', $show);
        } else {
            $showtext .= get_string('refreshednever', 'local_envbar');
        }

        // Optionally also show the config links for admins.
        $produrl = local_envbar_getprodwwwroot();
        $systemcontext = context_system::instance();
        $canedit = has_capability('moodle/site:config', $systemcontext);
        if ($canedit) {
            if ($produrl) {
                $editlink = html_writer::link($produrl.'/local/envbar/index.php',
                        get_string('configureinprod', 'local_envbar'), array('target' => 'prod'));
            } else {
                $editlink = html_writer::link(new moodle_url('/local/envbar/index.php'),
                        get_string('configurehere', 'local_envbar'));
            }
            $showtext .= '<nobr> - ' . $editlink . '</nobr>';
        }

        $html = <<<EOD
<div class="envbar $class">$showtext</div>
<style>
$css
</style>
<script>
document.body.className += ' local_envbar';
</script>
EOD;
        if ($fixed) {
            $html .= <<<EOD
<div style="height: 50px;">&nbsp;</div>
EOD;
        }

        return $html;
    }

}

