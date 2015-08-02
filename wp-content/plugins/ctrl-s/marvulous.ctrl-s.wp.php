<?php
/*
Plugin Name: Ctrl-S
Plugin URI: http://signpostmarv.name/ctrl-s/
Description: Use Ctrl-S to save a blog post!
Version: 1.0.2
Author: SignpostMarv Martin
Author URI: http://signpostmarv.name/
 Copyright 2009 SignpostMarv Martin  (email : embed-wave.wp@signpostmarv.name)
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
class Marvulous_Ctrl_S
{
	public function plugins_loaded()
	{
		wp_register_script('Marvulous_Ctrl_S',$this->plugin_file('js'),array('jquery'));
		add_action('admin_print_scripts',array($this,'print_scripts'));
	}
	public function plugin_file($file)
	{
		return untrailingslashit(trailingslashit(trailingslashit( get_bloginfo('wpurl') ).PLUGINDIR.'/'. dirname( plugin_basename(__FILE__) )) . 'marvulous.ctrl-s.wp.' . $file);
	}
	public function print_scripts()
	{
		wp_enqueue_script('Marvulous_Ctrl_S');
	}
}
$Marvulous_Ctrl_S = new Marvulous_Ctrl_S;
add_action('plugins_loaded',array($Marvulous_Ctrl_S,'plugins_loaded'));