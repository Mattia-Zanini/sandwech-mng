<?php
/**
 * Padding Control in Customizer
 *
 *
* @author 			WPBrigade
* @copyright 		Copyright (c) 2021, WPBrigade
* @link 			https://loginpress.pro/
* @license			https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
namespace LoginCustomizer\Customizer\Panel\Controls;
include_once ABSPATH . 'wp-includes/class-wp-customize-control.php';

/**
 * Padding Control Class
 */
class Padding extends \WP_Customize_Control {

	public $type = 'logincust-padding';

	public function enqueue() {
		wp_enqueue_script( 'logincust-padding', LOGINCUST_FREE_URL . 'Customizer/Panel/Controls/Assets/JS/padding-control.js', '', '', true );
		wp_enqueue_style( 'logincust-padding', LOGINCUST_FREE_URL . 'Customizer/Panel/Controls/Assets/CSS/padding-control.css' );
	}

	public function render_content() { ?>
		<label>
			<div id="<?php echo esc_attr( $this->id ); ?>">
				<?php if ( ! empty( $this->label ) ) : ?>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<?php endif; ?>
				<?php if ( ! empty( $this->description ) ) : ?>
					<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
				<?php endif; ?>
				<div class="field-area">
					<div class="field-icon"><i class="dashicons dashicons-arrow-up"></i><?php _e( 'Top', 'login-customizer' ); ?></div>
					<input type="number" value="10" min="0" max="1000" />
					<div class="field-icon"><i class="dashicons dashicons-arrow-right"></i><?php _e( 'Right', 'login-customizer' ); ?></div>
					<input type="number" value="10" min="0" max="1000" />
				</div>
				<div class="field-area">
					<div class="field-icon"><i class="dashicons dashicons-arrow-down"></i><?php _e( 'Down', 'login-customizer' ); ?></div>
					<input type="number" value="10" min="0" max="1000" />
					<div class="field-icon"><i class="dashicons dashicons-arrow-left"></i><?php _e( 'Left', 'login-customizer' ); ?></div>
					<input type="number" value="10" min="0" max="1000" />
				</div>
				<input type="text" value="<?php echo esc_html( $this->value() ); ?>" <?php $this->link(); ?> />
			</div>
		</label>
		<?php
	}

}
