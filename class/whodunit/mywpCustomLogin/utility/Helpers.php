<?php
namespace whodunit\mywpCustomLogin\utility;

use MatthiasMullie\Minify\CSS;

class Helpers{

	//Tailwind field helper
	protected $valid_html = [
		'div'      => [ 'class' => true, 'id'  => true, 'aria-required' => true, 'data-*' => true ],
		'label'    => [ 'class' => true, 'for' => true ],
		'button'   => [ 'type' => true, 'class' => true, 'id'  => true ],
		'input'    => [ 'class' => true, 'type' => true, 'id' => true, 'name' => true, 'value' => true, 'checked' => [], 'aria-required' => true, 'multiple' => true ],
		'select'   => [ 'class' => true, 'type' => true, 'id' => true, 'name' => true, 'aria-required' => true , 'multiple' => true ],
		'option'   => [ 'class' => true, 'value' => true, 'selected' => true ],
		'textarea' => [ 'class' => true, 'type' => true,'id' => true, 'name' => true ],
	];


	public function tailwind_field( $name, $id = null, $type = 'text', $value = null, $options = [], $echo = false ){
		$id = sanitize_title( ( ! is_null( $id ) && ! empty( $id ) ) ? $id : $name );
		if( method_exists( $this, 'tailwind_field_'.$type ) ){
			if( false === $echo ){ ob_start(); }
			$this->{'tailwind_field_'.$type}( $name, $id, $value, $options );
			if( false === $echo ){
				$output = ob_get_contents();
				ob_end_clean();
				return $output;
			}
		}
	}

	public function tailwind_label( $for, $label ){
		if( ! $label || empty( $label ) || ! $for || empty( $for ) ){ return; }
		return '<label class="" for="'.esc_attr( $for ).'">'.esc_html( $label ).'</label>';
	}

	public function tailwind_field_text( $name, $id, $value = null, $options = [] ){
		$label = ( isset( $options[ 'label' ] ) ) ? $options[ 'label' ] : false;
		$description = ( isset( $options[ 'description' ] ) ) ? $options[ 'description' ] : false;
		$unit  = ( isset( $options[ 'unit' ] ) ) ? $options[ 'unit' ] : false;
		$input = [
			'class' => '',
			'type'  => 'text',
			'name'  => $name,
			'id'    => $id,
		];
		if( ! is_null( $value ) ){ $input[ 'value' ] = $value; }
		$input_attrs = array_map( function( $attr, $value ){
			return $attr.'="'.esc_attr( $value ).'"';
		}, array_keys( $input ), $input );

		echo '<div>';
			echo wp_kses( $this->tailwind_label( $id, $label ), $this->valid_html );
			if( $description ){ echo '<p>'.wp_kses( $description, $this->valid_html ).'</p>'; }
			echo '<div class="field mywp-admin-line">';
				echo wp_kses( '<input '.implode( ' ', $input_attrs ).'>', $this->valid_html );
			echo '</div>';
		echo '</div>';
	}

	public function tailwind_field_number( $name, $id, $value = null, $options = [] ){
		$label = ( isset( $options[ 'label' ] ) ) ? $options[ 'label' ] : false;
		$description = ( isset( $options[ 'description' ] ) ) ? $options[ 'description' ] : false;
		$unit  = ( isset( $options[ 'unit' ] ) ) ? $options[ 'unit' ] : false;
		$input = [
			'class' => '',
			'type'  => 'number',
			'min'   => '0',
			'step'  => '1',
			'name'  => $name,
			'id'    => $id,
		];
		if( ! is_null( $value ) ){ $input[ 'value' ] = $value; }
		$input_attrs = array_map( function( $attr, $value ){
			return $attr.'="'.esc_attr( $value ).'"';
		}, array_keys( $input ), $input );

		echo '<div>';
			echo wp_kses( $this->tailwind_label( $id, $label ), $this->valid_html );
			if( $description ){ echo '<p>'.wp_kses( $description, $this->valid_html ).'</p>'; }
			echo '<div class="field mywp-admin-line">';
				echo wp_kses( '<input '.implode( ' ', $input_attrs ).'>', $this->valid_html );
				if( $unit ){ echo wp_kses( '<code>'.esc_html( $unit ).'</code>', $this->valid_html ); }
			echo '</div>';
		echo '</div>';
	}

	public function tailwind_field_select( $name, $id, $value = null, $options = [] ){
		if( ! isset( $options[ 'options' ] ) || ! is_array( $options[ 'options' ] ) ){ return; };
		$label       = ( isset( $options[ 'label' ] ) ) ? $options[ 'label' ] : false;
		$description = ( isset( $options[ 'description' ] ) ) ? $options[ 'description' ] : false;
		$input       = [
			'class' => '',
			'name'  => $name,
			'id'    => $id,
		];
		$input_attrs = array_map( function( $attr, $value ){
			return $attr.'="'.esc_attr( $value ).'"';
		}, array_keys( $input ), $input );
		$options = array_map( function( $default_value, $label )use( $value ){
			return '<option value="'.esc_attr( $default_value ).'"'.( ( $value === $default_value ) ? ' selected' : '' ).'>'.esc_html( $label ).'</option>';
		}, array_keys( $options[ 'options' ] ), $options[ 'options' ] );

		echo '<div>';
			echo wp_kses( $this->tailwind_label( $id, $label ), $this->valid_html );
			if( $description ){ echo '<p>'.wp_kses( $description, $this->valid_html ).'</p>'; }
			echo '<div class="field mywp-admin-line">';
				echo wp_kses( '<select '.implode( ' ', $input_attrs ).'>', $this->valid_html );
					echo wp_kses( implode( '', $options ), $this->valid_html );
				echo '<select>';
			echo '</div>';
		echo '</div>';
	}

	public function tailwind_field_checkbox( $name, $id, $value = null, $options = [] ){
		$label       = ( isset( $options[ 'label' ] ) ) ? $options[ 'label' ] : false;
		$description = ( isset( $options[ 'description' ] ) ) ? $options[ 'description' ] : false;
		$input       = [
			'class' => '',
			'type'  => 'checkbox',
			'role'  => 'switch',
			'name'  => $name,
			'id'    => $id,
			'value' => '1',
		];
		if( $value === 'true' ){ $input[ 'checked' ] = 'checked'; }
		$input_attrs = array_map( function( $attr, $val ){
			return $attr.'="'.esc_attr( $val ).'"';
		}, array_keys( $input ), $input );

		echo '<div>';
			echo '<div class="field">';
                echo wp_kses( '<input '.implode( ' ', $input_attrs ).'>', $this->valid_html );
				echo wp_kses( $this->tailwind_label( $id, $label ), $this->valid_html );
			echo '</div>';
			if( $description ){ echo '<p>'.wp_kses( $description, $this->valid_html ).'</p>'; }
		echo '</div>';
	}

	public function tailwind_field_colorpicker( $name, $id, $value = null, $options = [] ){
		$label       = ( isset( $options[ 'label' ] ) ) ? $options[ 'label' ] : false;
		$description = ( isset( $options[ 'description' ] ) ) ? $options[ 'description' ] : false;
		$input       = [
			'class'      => 'color-picker-field',
			'type'       => 'text',
			'name'       => $name,
			'id'         => $id,
			'data-alpha' => 'true'
		];
		if( ! is_null( $value ) ){ $input[ 'value' ] = $value; }
		$input_attrs = array_map( function( $attr, $value ){
			return $attr.'="'.esc_attr( $value ).'"';
		}, array_keys( $input ), $input );

		echo '<div>';
			echo wp_kses( $this->tailwind_label( $id, $label ), $this->valid_html );
			if( $description ){ echo '<p>'.wp_kses( $description, $this->valid_html ).'</p>'; }
			echo '<div class="field mywp-admin-line">';
				echo wp_kses( '<input '.implode( ' ', $input_attrs ).'>', $this->valid_html );
			echo '</div>';
		echo '</div>';
	}

	public function tailwind_field_media( $name, $id, $value = null, $options = [] ){
		$label       = ( isset( $options[ 'label' ] ) ) ? $options[ 'label' ] : false;
		$description = ( isset( $options[ 'description' ] ) ) ? $options[ 'description' ] : false;
		$input = [
			'class' => '',
			'type'  => 'text',
			'name'  => $name,
			'id'    => $id,
		];
		if( ! is_null( $value ) ){ $input[ 'value' ] = $value; }
		$input_attrs = array_map( function( $attr, $value ){
			return $attr.'="'.esc_attr( $value ).'"';
		}, array_keys( $input ), $input );

		echo '<div>';
			echo wp_kses( $this->tailwind_label( $id, $label ), $this->valid_html );
			if( $description ){ echo '<p>'.wp_kses( $description, $this->valid_html ).'</p>'; }
			echo '<div class="field media-field  mywp-admin-line is-mywp-flex with-gap-s">';
				echo wp_kses( '<input '.implode( ' ', $input_attrs ).'>', $this->valid_html );
				echo '<button class="upload-button button button-alternative" type="button">'.esc_html__( 'Add media', 'mywp-custom-login' ).'</button>';
				echo '<button class="clear-button button button-alternative" type="button"><span class="dashicons dashicons-no"></span></button>';
			echo '</div>';
		echo '</div>';
	}

	public function dashboard_settings( $settings ) {
		$left_side_text  = isset( $settings['data_left'] ) ? stripslashes( $settings['data_left'] ) : '';
		$right_side_text = isset( $settings['data_right'] ) ? stripslashes( $settings['data_right'] ) : '';

		?>
		<section class="">
			<h3 class=""><?php esc_html_e( 'Dashboard Settings', 'mywp-custom-login' ); ?></h3>
			<div class="">
				<?php
					$this->tailwind_field( 'options[login_settings][data_left]', 'data_left', 'text', $left_side_text, [
						'label' => __( 'Footer Text', 'mywp-custom-login' ),
					], true );
					$this->tailwind_field('options[login_settings][data_right]', 'data_right', 'text', $right_side_text, [
						'label' => __( 'Version Text', 'mywp-custom-login' ),
					], true);
				?>
			</div>
		</section>
		<?php
	}

	//deprecated
	public function misc_settings( $settings ) {
		$clean_deactivation = isset( $settings['delete_db'] ) ? $settings['delete_db'] : 0;
		$clean_deactivation = ( 'yes' === strtolower( $clean_deactivation ) ) ? 1 : 0;

		?>
		<section class="">
			<h3 class=""><?php esc_html_e( 'Misc', 'mywp-custom-login' ); ?></h3>
			<div class="">
				<?php
				$this->tailwind_field( 'options[login_settings][delete_db]', 'delete_db', 'checkbox', $clean_deactivation, [
					'label' => __( 'Remove data on uninstall', 'mywp-custom-login' ),
					'description' => __( 'If checked, all data will be removed on plugin deactivation.', 'mywp-custom-login' ),
				], true );
				?>
			</div>
		</section>
		<?php
	}

	public function logo_settings( $settings ){

		$logo_image_url = isset( $settings['image_logo'] ) && ! empty( $settings['image_logo'] ) ? $settings['image_logo'] : '';
		$logo_width     = isset( $settings['image_logo_width'] ) && ! empty( $settings['image_logo_width'] ) ? $settings['image_logo_width'] : '';
		$logo_height    = isset( $settings['image_logo_height'] ) && ! empty( $settings['image_logo_height'] ) ? $settings['image_logo_height'] : '';
		$logo_hint_text = isset( $settings['power_text'] ) && ! empty( $settings['power_text'] ) ? $settings['power_text'] : '';

		?>
		<section class="is-grid-span-2">

			<h3 class="is-mywp-title-underlined"><?php esc_html_e( 'Logo', 'mywp-custom-login' ); ?></h3>

            <div class="is-mywp-grid with-grid-x2 with-gap-xl">

                <div>
                <?php

                    $this->tailwind_field( 'options[login_settings][power_text]', 'power_text', 'text', $logo_hint_text, [
                        'label' => __( 'Logo Title', 'mywp-custom-login' ),
                    ], true );

                    $this->tailwind_field( 'options[login_settings][image_logo]', 'image_logo', 'media', $logo_image_url, [
                        'label' => __( 'Logo URL', 'mywp-custom-login' ),
                    ], true );
                ?>
                </div>

                <div>
                <?php

                    $this->tailwind_field( 'options[login_settings][image_logo_width]', 'image_logo_width', 'number', $logo_width, [
                        'label' => __( 'Logo Width', 'mywp-custom-login' ),
                        'unit'  => __( 'px', 'mywp-custom-login' ),
                    ], true );
                    $this->tailwind_field( 'options[login_settings][image_logo_height]', 'image_logo_height', 'number', $logo_height, [
                        'label' => __( 'Logo Height', 'mywp-custom-login' ),
                        'unit'  => __( 'px', 'mywp-custom-login' ),
                    ], true );
                ?>
                </div>
            </div>

		</section>
		<?php

	}

	public function bg_settings( $settings ){
		$bg_color       = isset( $settings['top_bg_color'] ) && ! empty( $settings['top_bg_color'] ) ? $settings['top_bg_color'] : '';
		$bg_image_url   = isset( $settings['top_bg_image'] ) && ! empty( $settings['top_bg_image'] ) ? $settings['top_bg_image'] : '';
		$bg_repeat      = isset( $settings['top_bg_repeat'] ) && ! empty( $settings['top_bg_repeat'] ) ? $settings['top_bg_repeat'] : '';
		$horizontal_pos = isset( $settings['top_bg_xpos'] ) && ! empty( $settings['top_bg_xpos'] ) ? $settings['top_bg_xpos'] : '';
		$vertical_pos   = isset( $settings['top_bg_ypos'] ) && ! empty( $settings['top_bg_ypos'] ) ? $settings['top_bg_ypos'] : '';
		$bg_size        = isset( $settings['top_bg_size'] ) && ! empty( $settings['top_bg_size'] ) ? $settings['top_bg_size'] : '';

		?>
		<section class="">

			<h3 class="is-mywp-title-underlined with-underline-color-white"><?php esc_html_e( 'Background', 'mywp-custom-login' ); ?></h3>

			<div class="is-mywp-grid with-grid-x3 with-gap-xl">
				<?php
				$this->tailwind_field( 'options[login_settings][top_bg_color]', 'top_bg_color', 'colorpicker', $bg_color, [
					'label' => __( 'Background Color', 'mywp-custom-login' ),
				], true );
				$this->tailwind_field( 'options[login_settings][top_bg_image]', 'top_bg_image', 'media', $bg_image_url, [
					'label' => __( 'Background Image', 'mywp-custom-login' ),
				], true );
				$this->tailwind_field( 'options[login_settings][top_bg_repeat]', 'top_bg_repeat', 'select', $bg_repeat, [
					'label'   => __( 'Background Repeat', 'mywp-custom-login' ),
					'options' => [
						'no-repeat' => __( 'no-repeat', 'mywp-custom-login' ),
						'repeat'    => __( 'repeat', 'mywp-custom-login' ),
						'repeat-x'  => __( 'repeat-x', 'mywp-custom-login' ),
						'repeat-y'  => __( 'repeat-y', 'mywp-custom-login' ),
					],
				], true );
				$this->tailwind_field( 'options[login_settings][top_bg_size]', 'top_bg_size', 'text', $bg_size, [
					'label'       => __( 'Background Size', 'mywp-custom-login' ),
					'description' => __( 'Possible values: <code>auto</code>, <code>cover</code>, <code>contain</code> or numeric value. If a numeric value is provided, a unit (<code>px</code>, <code>%</code>, etc.) must be defined.', 'mywp-custom-login' ),
				], true );
				$this->tailwind_field( 'options[login_settings][top_bg_xpos]', 'top_bg_xpos', 'text', $horizontal_pos, [
					'label'       => __( 'Background Horizontal Position', 'mywp-custom-login' ),
					'description' => __( 'Possible values: <code>left</code>, <code>center</code>, <code>right</code> or numeric value. If a numeric value is provided, a unit (<code>px</code>, <code>%</code>, etc.) must be defined.', 'mywp-custom-login' ),
				], true );
				$this->tailwind_field( 'options[login_settings][top_bg_ypos]', 'top_bg_ypos', 'text', $vertical_pos, [
					'label'       => __( 'Background Vertical Position', 'mywp-custom-login' ),
					'description' => __( 'Possible values: <code>left</code>, <code>center</code>, <code>right</code> or numeric value. If a numeric value is provided, a unit (<code>px</code>, <code>%</code>, etc.) must be defined.', 'mywp-custom-login' ),
				], true );
				?>
			</div>
		</section>
		<?php

	}

	public function form_layout_settings( $settings ){
		$form_width    = isset( $settings['login_width'] ) && ! empty( $settings['login_width'] ) ? $settings['login_width'] : '';
		$border_radius = isset( $settings['login_radius'] ) && ! empty( $settings['login_radius'] ) ? $settings['login_radius'] : '';
		$border_width  = isset( $settings['border_thick'] ) && ! empty( $settings['border_thick'] ) ? $settings['border_thick'] : '';
		$border_style  = isset( $settings['login_border'] ) && ! empty( $settings['login_border'] ) ? $settings['login_border'] : '';
		$border_color  = isset( $settings['border_color'] ) && ! empty( $settings['border_color'] ) ? $settings['border_color'] : '';
		$enable_shadow = isset( $settings['check_form_shadow'] ) ? $settings['check_form_shadow'] : 0;
		$enable_shadow = 'yes' === strtolower( $enable_shadow ) ? 1 : $enable_shadow;
		$enable_shadow = 'no' === strtolower( $enable_shadow ) ? 0 : $enable_shadow;
		$shadow_color  = isset( $settings['form_shadow'] ) && ! empty( $settings['form_shadow'] ) ? $settings['form_shadow'] : '';

		?>
		<section class="is-mywp-overflow-hidden">

            <h3 class="is-mywp-title-underlined"><?php esc_html_e( 'Form Layout', 'mywp-custom-login' ); ?></h3>

			<div class="is-mywp-grid with-grid-x3 with-gap-xl">

                <div class="">

                    <?php
                    $this->tailwind_field( 'options[login_settings][login_width]', 'login_width', 'number', $form_width, [
                        'label' => __( 'Form Width', 'mywp-custom-login' ),
                        'unit'  => __( 'px', 'mywp-custom-login' ),
                    ], true );
                    ?>


                    <p class="label-mimic"><?php esc_html_e( 'Form Box Shadow', 'mywp-custom-login' )?></p>

                    <div class="is-mywp-grid with-grid-stacked with-gap-m">
                        <div>
                        <?php

                        $this->tailwind_field( 'options[login_settings][delete_db]', 'delete_db', 'checkbox', $enable_shadow, [
                            'label'       => __( 'Enable Form Box shadow', 'mywp-custom-login' ),
                        ], true );
                        ?>
                        </div>
                        <div>
                        <?php
                        $this->tailwind_field( 'options[login_settings][form_shadow]', 'form_shadow', 'colorpicker', $shadow_color, [
                            'label' => __( 'Form Box Shadow Color', 'mywp-custom-login' ),
                        ], true );
                        ?>
                        </div>
                    </div>

                </div>

                <div>
					<?php

					$this->tailwind_field( 'options[login_settings][border_color]', 'border_color', 'colorpicker', $border_color, [
						'label' => __( 'Border Color', 'mywp-custom-login' ),
					], true );


					$this->tailwind_field( 'options[login_settings][login_border]', 'login_border', 'select', $border_style, [
						'label'   => __( 'Border Style', 'mywp-custom-login' ),
						'options' => [
							'none'   => __( 'None', 'mywp-custom-login' ),
							'solid'  => __( 'Solid', 'mywp-custom-login' ),
							'dotted' => __( 'Dotted', 'mywp-custom-login' ),
							'dashed' => __( 'Dashed', 'mywp-custom-login' ),
							'double' => __( 'Double', 'mywp-custom-login' ),
						],
					], true );
					?>


                </div>

                <div class="">

					<?php
					$this->tailwind_field( 'options[login_settings][border_thick]', 'border_thick', 'number', $border_width, [
						'label' => __( 'Border Width', 'mywp-custom-login' ),
						'unit'  => __( 'px', 'mywp-custom-login' ),
					], true );
					?>
                    <?php
                    $this->tailwind_field( 'options[login_settings][login_radius]', 'login_radius', 'number', $border_radius, [
                        'label' => __( 'Border Radius', 'mywp-custom-login' ),
                        'unit'  => __( 'px', 'mywp-custom-login' ),
                    ], true );
                    ?>


                </div>


			</div>
		</section>
		<?php
	}

	public function form_bg_settings( $settings ){
		$bg_color       = isset( $settings['login_bg'] ) && ! empty( $settings['login_bg'] ) ? $settings['login_bg'] : '';
		$bg_image_url   = isset( $settings['login_bg_image'] ) && ! empty( $settings['login_bg_image'] ) ? $settings['login_bg_image'] : '';
		$bg_repeat      = isset( $settings['login_bg_repeat'] ) && ! empty( $settings['login_bg_repeat'] ) ? $settings['login_bg_repeat'] : '';
		$horizontal_pos = isset( $settings['login_bg_xpos'] ) && ! empty( $settings['login_bg_xpos'] ) ? $settings['login_bg_xpos'] : '';
		$vertical_pos   = isset( $settings['login_bg_ypos'] ) && ! empty( $settings['login_bg_ypos'] ) ? $settings['login_bg_ypos'] : '';

		if ( isset( $settings['login_bg_opacity'] ) ) {
			// This `login_bg_opacity` won't be used anymore since we use colorpicker alpha now.
			$bg_opacity = '' !== $settings['login_bg_opacity'] ? $settings['login_bg_opacity'] : 1; // 0 is allowed here.

			if ( false === stripos( $bg_color, 'rgba' ) && 1 > $bg_opacity ) {
				$bg_color = ariColor::newColor( $bg_color );
				$bg_color = $bg_color->getNew( 'alpha', $bg_opacity )->toCSS( 'rgba' );
			}
		}
		?>
		<section class="">

			<h3 class="is-mywp-title-underlined with-underline-color-white"><?php esc_html_e( 'Form Background', 'mywp-custom-login' ); ?></h3>

			<div class="is-mywp-grid with-flex-x3 with-gap-xl">

                <div>
				<?php
				$this->tailwind_field( 'options[login_settings][login_bg]', 'login_bg', 'colorpicker', $bg_color, [
					'label' => __( 'Background Color', 'mywp-custom-login' ),
				], true );
                ?>
                </div>
                <div>
                <?php
				$this->tailwind_field( 'options[login_settings][login_bg_image]', 'login_bg_image', 'media', $bg_image_url, [
					'label' => __( 'Background Image URL', 'mywp-custom-login' ),
				], true );
				?>
                </div>
                <div>
                <?php
				$this->tailwind_field( 'options[login_settings][login_bg_repeat]', 'login_bg_repeat', 'select', $bg_repeat, [
					'label'   => __( 'Background Repeat', 'mywp-custom-login' ),
					'options' => [
						'no-repeat' => __( 'no-repeat', 'mywp-custom-login' ),
						'repeat'    => __( 'repeat', 'mywp-custom-login' ),
						'repeat-x'  => __( 'repeat-x', 'mywp-custom-login' ),
						'repeat-y'  => __( 'repeat-y', 'mywp-custom-login' ),
					],
				], true );
				?>
                </div>
                <div>
                <?php
				$this->tailwind_field( 'options[login_settings][login_bg_xpos]', 'login_bg_xpos', 'text', $horizontal_pos, [
					'label'       => __( 'Background Horizontal Position', 'mywp-custom-login' ),
					'description' => __( 'Possible values: <code>left</code>, <code>center</code>, <code>right</code> or numeric value. If a numeric value is provided, a unit (<code>px</code>, <code>%</code>, etc.) must be defined.', 'mywp-custom-login' ),
				], true );
				?>
                </div>
                <div>
                <?php
				$this->tailwind_field( 'options[login_settings][login_bg_ypos]', 'login_bg_ypos', 'text', $vertical_pos, [
					'label'       => __( 'Background Vertical Position', 'mywp-custom-login' ),
					'description' => __( 'Possible values: <code>left</code>, <code>center</code>, <code>right</code> or numeric value. If a numeric value is provided, a unit (<code>px</code>, <code>%</code>, etc.) must be defined.', 'mywp-custom-login' ),
				], true );
				?>
                </div>

			</div>
		</section>
		<?php
	}

	public function form_label_settings( $settings ){
		$font_color = isset( $settings['text_color'] ) && ! empty( $settings['text_color'] ) ? $settings['text_color'] : '';
		$font_size  = isset( $settings['label_text_size'] ) && ! empty( $settings['label_text_size'] ) ? $settings['label_text_size'] : '';

		?>
		<section class="">

			<h3 class="is-mywp-title-underlined"><?php esc_html_e( 'Form Label', 'mywp-custom-login' ); ?></h3>

			<div class="">
				<?php
				$this->tailwind_field( 'options[login_settings][text_color]', 'text_color', 'colorpicker', $font_color, [
					'label' => __( 'Font Color', 'mywp-custom-login' ),
				], true );
				$this->tailwind_field( 'options[login_settings][label_text_size]', 'label_text_size', 'number', $font_size, [
					'label' => __( 'Font Size', 'mywp-custom-login' ),
					'unit'  => __( 'px', 'mywp-custom-login' ),
				], true );
				?>
			</div>
		</section>
		<?php
	}

	public function form_input_settings( $settings ){
		$font_color = isset( $settings['input_text_color'] ) && ! empty( $settings['input_text_color'] ) ? $settings['input_text_color'] : '';
		$font_size  = isset( $settings['input_text_size'] ) && ! empty( $settings['input_text_size'] ) ? $settings['input_text_size'] : '';

		?>
		<section class="">

			<h3 class="is-mywp-title-underlined"><?php esc_html_e( 'Form Input', 'mywp-custom-login' ); ?></h3>

			<div class="">
				<?php
				$this->tailwind_field( 'options[login_settings][input_text_color]', 'input_text_color', 'colorpicker', $font_color, [
					'label' => __( 'Font Color', 'mywp-custom-login' ),
				], true );
				$this->tailwind_field( 'options[login_settings][input_text_size]', 'input_text_size', 'number', $font_size, [
					'label' => __( 'Font Size', 'mywp-custom-login' ),
					'unit'  => __( 'px', 'mywp-custom-login' ),
				], true );
				?>
			</div>
		</section>
		<?php
	}

	public function form_button_settings( $settings ){
		$button_bg_color   = isset( $settings['button_color'] ) && ! empty( $settings['button_color'] ) ? $settings['button_color'] : '';
		$button_text_color = isset( $settings['button_text_color'] ) && ! empty( $settings['button_text_color'] ) ? $settings['button_text_color'] : '';

		?>
		<section class="">

			<h3 class="is-mywp-title-underlined"><?php esc_html_e( 'Form Button', 'mywp-custom-login' ); ?></h3>

			<div class="">
				<?php
				$this->tailwind_field( 'options[login_settings][button_color]', 'button_color', 'colorpicker', $button_bg_color, [
					'label' => __( 'Background Color', 'mywp-custom-login' ),
				], true );
				$this->tailwind_field( 'options[login_settings][button_text_color]', 'button_text_color', 'colorpicker', $button_text_color, [
					'label' => __( 'Font Color', 'mywp-custom-login' ),
				], true );
				?>
			</div>
		</section>
		<?php
	}

	public function form_link_settings( $settings ){
		$link_color    = isset( $settings['link_color'] ) && ! empty( $settings['link_color'] ) ? $settings['link_color'] : '';
		$enable_shadow = isset( $settings['check_shadow'] ) ? $settings['check_shadow'] : 0;
		$enable_shadow = 'yes' === strtolower( $enable_shadow ) ? 1 : $enable_shadow;
		$enable_shadow = 'no' === strtolower( $enable_shadow ) ? 0 : $enable_shadow;
		$shadow_color  = isset( $settings['link_shadow'] ) && ! empty( $settings['link_shadow'] ) ? $settings['link_shadow'] : '';

		?>
		<section class="">

			<h3 class="is-mywp-title-underlined"><?php esc_html_e( 'Form Link', 'mywp-custom-login' ); ?></h3>

            <div class="is-mywp-grid">

                <div>
                    <?php
                    $this->tailwind_field( 'options[login_settings][link_color]', 'link_color', 'colorpicker', $link_color, [
                        'label' => __( 'Link Color', 'mywp-custom-login' ),
                    ], true );
                    ?>
                </div>

                <div>
                    <p class="label-mimic"><?php esc_html_e('Text Shadow','mywp-custom-login'); ?></p>

                    <div class="is-mywp-grid with-grid-stacked with-gap-m">
                        <div>
                        <?php

                        $this->tailwind_field( 'options[login_settings][check_shadow]', 'check_shadow', 'checkbox', $enable_shadow, [
                            'label' => __( 'Enable Text Shadow', 'mywp-custom-login' ),
                        ], true );
                        ?>
                        </div>
                        <div>
                        <?php
                        $this->tailwind_field( 'options[login_settings][link_shadow]', 'link_shadow', 'colorpicker', $shadow_color, [
                            'label' => __( 'Text Shadow Color', 'mywp-custom-login' ),
                        ], true );
                        ?>
                        </div>
                    </div>
                </div>
            </div>
		</section>
		<?php
	}

	public function footer_link_settings( $settings ){
		$remove_register_link = isset( $settings['check_lost_pass'] ) ? $settings['check_lost_pass'] : 0;
		$remove_register_link = 'yes' === strtolower( $remove_register_link ) ? 1 : $remove_register_link;
		$remove_register_link = 'no' === strtolower( $remove_register_link ) ? 0 : $remove_register_link;
		$remove_back_to_blog_link = isset( $settings['check_backtoblog'] ) ? $settings['check_backtoblog'] : 0;
		$remove_back_to_blog_link = 'yes' === strtolower( $remove_back_to_blog_link ) ? 1 : $remove_back_to_blog_link;
		$remove_back_to_blog_link = 'no' === strtolower( $remove_back_to_blog_link ) ? 0 : $remove_back_to_blog_link;

		?>
		<section class="">

			<h3 class="is-mywp-title-underlined"><?php esc_html_e( 'Footer', 'mywp-custom-login' ); ?></h3>

			<div class="">

                <p class="label-mimic"><?php esc_html_e( 'Remove Links', 'mywp-custom-login' ); ?></p>

                <div class="is-mywp-grid with-grid-stacked with-gap-m">
				<?php
				$this->tailwind_field( 'options[login_settings][check_lost_pass]', 'check_lost_pass', 'checkbox', $remove_register_link, [
					'label' => __( 'Remove "Register | Lost your password?" link', 'mywp-custom-login' ),
				], true );
				$this->tailwind_field( 'options[login_settings][check_backtoblog]', 'check_backtoblog', 'checkbox', $remove_back_to_blog_link, [
					'label' => __( 'Remove "Back to website" link', 'mywp-custom-login' ),
				], true );
				?>
                </div>
			</div>
		</section>
		<?php
	}

}