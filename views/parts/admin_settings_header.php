<?php

$plugin = \whodunit\mywpCustomLogin\MyWPCustomLogin::get_instance() ;

$plugin_support_url       = 'https://wordpress.org/support/plugin/mywp-custom-login/' ;
$plugin_documentation_url = 'https://wordpress.org/plugins/mywp-custom-login/' ;

?>
<div class="mywp-admin">

    <div class="mywp-admin-header mywp-admin-header--center">

        <div class="mywp-admin-header__inner-container">

            <div class="mywp-admin-header__brand">
                <?php /*
				<div class="mywp-admin-header__logo">
                    <img src="<?php echo esc_url( $plugin->get_assets_url() . 'img/mywp-custom-login-logo.png' ) ; ?>" alt="" class="mywp-admin-header__logo-image">
                </div>
				*/ ?>
                <h1 class="mywp-admin-header__title">
                    <small><?php esc_html_e('MyWP', 'mywp-custom-login') ?></small>
                    <span><?php esc_html_e('Custom Login', 'mywp-custom-login') ?></span>
                </h1>
            </div>

            <div class="mywp-admin-header__help">
                <p>
                    <span><?php esc_html_e('Need some help?', 'mywp-custom-login') ?></span>
                    <span>
                      <?php
                      echo sprintf(
                      /* translators: 1: Opening HTML link tag. 2: Closing HTML link tag. 3: Opening HTML link tag. 4: Closing HTML link tag. */
						  esc_html__( 'Ask to %1$ssupport%2$s or check our %3$sdocumentation%4$s!', 'mywp-custom-login' ),
                          '<a href="'.esc_url( $plugin_support_url ).'" target="_blank" rel="noopener noreferrer" >',
                          '</a>',
                          '<a href="'.esc_url( $plugin_documentation_url ).'" target="_blank" rel="noopener noreferrer" >',
                          '</a>'
                      );
                      ?>
                    </span>
                </p>
                <img class="mywp-admin-header__help-icon" src="<?php echo esc_url( $plugin->get_assets_url()  . 'img/mywp-help.svg' ) ; ?>">
            </div>
        </div>
    </div>
</div>
