<?php

$helper  = new \whodunit\mywpCustomLogin\utility\Helpers();
$options = \whodunit\mywpCustomLogin\MyWPCustomLogin::get_instance()->get_options();
$notices = \whodunit\mywpCustomLogin\utility\Notices::get_instance();
$notices->echo_notices();

wp_enqueue_style( 'mywp_custom_login__admin_style' );
wp_enqueue_style( 'wp-color-picker' );
//load wp.media dependency
if ( ! did_action( 'wp_enqueue_media' ) ) { wp_enqueue_media(); }
wp_enqueue_script( 'mywp_custom_login__admin_setting_script' );
wp_localize_script( 'mywp_custom_login__admin_setting_script', 'mywp_custom_login', [
	'save_settings_api_url'      => get_rest_url( null, 'mywp-custom-login/v1/settings' ),
	'nonce'                      => wp_create_nonce( 'wp_rest' ),
] );

?>
<div class="mywp-admin">


    <form class="mywp-custom-login__settings_form mywp-admin-section">

        <div class="mywp-admin-section">
            <div class="mywp-admin-spacer with-space-xl"></div>
            <h2 class="">
				<?php esc_html_e( 'General settings', 'mywp-custom-login' ); ?>
            </h2>
        </div>

        <div class="mywp-admin-section">

            <div class="is-mywp-grid with-grid-x3 with-gap-xl">

                <?php $helper->logo_settings( $options[ 'login_settings' ] );  ?>
                <?php $helper->footer_link_settings( $options[ 'login_settings' ] );  ?>
            </div>
        </div>

        <div class="mywp-admin-section with-background-color">
            <?php $helper->bg_settings( $options[ 'login_settings' ] );  ?>
        </div>



        <div class="mywp-admin-section">

            <div class="mywp-admin-spacer with-space-xl"></div>

            <h2 class="">
				<?php esc_html_e( 'Form settings', 'mywp-custom-login' ); ?>
            </h2>
        </div>

        <div class="mywp-admin-section">
            <?php $helper->form_layout_settings( $options[ 'login_settings' ] );  ?>
        </div>

        <div class="mywp-admin-section is-mywp-grid with-gap-xl">

            <?php $helper->form_label_settings( $options[ 'login_settings' ] );  ?>
            <?php $helper->form_input_settings( $options[ 'login_settings' ] );  ?>
            <?php $helper->form_button_settings( $options[ 'login_settings' ] );  ?>
        </div>


        <div class="mywp-admin-section">
            <?php $helper->form_link_settings( $options[ 'login_settings' ] );  ?>
        </div>

        <div class="mywp-admin-section with-background-color">
			<?php $helper->form_bg_settings( $options[ 'login_settings' ] );  ?>
        </div>



        <div class="mywp-admin-section">

            <div class="mywp-admin-spacer with-space-xl"></div>

            <button class="submit-form button button-alternative is-mywp-flex with-gap-s" type="button">
                <span class="dashicons dashicons-saved"></span> <?php esc_html_e( 'Save Settings', 'mywp-custom-login' ); ?>
            </button>
        </div>

    </form>

</div>