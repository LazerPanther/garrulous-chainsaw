<?php
/**
 * Redux Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Redux Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Redux Framework. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package     ReduxFramework
 * @subpackage  Field_Divide
 * @author      Daniel J Griffiths (Ghost1227)
 * @author      Dovy Paukstys
 * @version     3.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// Don't duplicate me!
if( !class_exists( 'ReduxFramework_url' ) ) {
	class ReduxFramework_url {
		function __construct( $field = array(), $value ='', $parent ) {
			//parent::__construct( $parent->sections, $parent->args );
			$this->parent = $parent;
			$this->field = $field;
			$this->value = $value;
		}
		public function render() {
			$defaults = array(
				'urlhint' => '',
				'url_text' => '',
				'href' => '',
			);
			$this->field = wp_parse_args( $this->field, $defaults );

			$qtip_title = isset($this->field['title']) ? 'qtip-title="' . $this->field['title'] . '" ' : '';
			$qtip_text  = isset($this->field['urlhint']) ? 'qtip-content="' . $this->field['urlhint'] . '" ' : '';
			echo '<a ' . $qtip_title . $qtip_text . ' data-id="'.$this->field['id'].'" id="'.$this->field['id'].'-url" href="' . $this->field['href'] . '"/>' . $this->field['url_text'] . '</a>';
		}
	}
}