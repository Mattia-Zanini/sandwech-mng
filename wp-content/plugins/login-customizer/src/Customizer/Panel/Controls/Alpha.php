<?php
/**
 * Alpha Color Picker Customizer Control
 *
 * This control adds a second slider for opacity to the stock WordPress color picker,
 * and it includes logic to seamlessly convert between RGBa and Hex color values as
 * opacity is added to or removed from a color.
 *
 * This Alpha Color Picker is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this Alpha Color Picker. If not, see <https://www.gnu.org/licenses/>.
 */

namespace LoginCustomizer\Customizer\Panel\Controls;

include_once ABSPATH . 'wp-includes/class-wp-customize-control.php';

/**
 * Alpha Color Control Class for Customizer
 * @author Hardeep Asrani
 * @version 2.2.0 
 */

class Alpha extends \WP_Customize_Control {

	public $type = 'alphacolor';
	public $palette = true;
	public $default = array();

	public function to_json() {
		if ( ! empty( $this->setting->default ) ) {
			$this->json['default'] = $this->setting->default;
		} else {
			$this->json['default'] = false;
		}
		parent::to_json();
	}

	/**
	 * Function to Enqueue styling and scripts
	 *
	 * @return void
	 */
	public function enqueue() {
		wp_enqueue_script( 'logincust-alpha', LOGINCUST_FREE_URL . 'Customizer/Panel/Controls/Assets/JS/alpha-control.js', array( 'jquery' ), null, true );
		wp_enqueue_style( 'logincust-alpha', LOGINCUST_FREE_URL . 'Customizer/Panel/Controls/Assets/CSS/alpha_control.css' );
	}

	public function render_content() { ?>
		<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
		<span class="customize-control-description"><?php echo esc_html( $this->description ); ?></span>
		<label>
			<input type="text" data-palette="<?php echo $this->palette; ?>" data-default-color="<?php echo $this->setting->default; ?>" value="<?php echo intval( $this->value() ); ?>" class="logincust-color-control" <?php $this->link(); ?>  />
		</label>
		<?php
	}
}

