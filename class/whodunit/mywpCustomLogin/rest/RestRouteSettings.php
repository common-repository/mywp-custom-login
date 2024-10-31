<?php
namespace whodunit\mywpCustomLogin\rest;

use whodunit\mywpCustomLogin\utility\WordPress;
use whodunit\mywpCustomLogin\MyWPCustomLogin;

/**
 * RestRouteSettings class
 * handle whodunit read and write plugin option wp rest api route
 */
class RestRouteSettings extends RestRoute{

	protected $name_space;
	protected $route;
	protected $params;

	/**
	 * Constructor
	 * final object need no arguments
	 * - define params use by this route
	 * - register route
	 */
	function __construct(){
		$params = [
			'options' => [
				'required' => true,
				'validate' => function( $v, $r, $k ){ return is_array( $v ); },
				'sanitize' => function( $v, $r, $k ){ return WordPress::recursive_sanitize_text_field( $v ); },
			],
		];
		parent::__construct(
			'mywp-custom-login/v1',
			'settings',
			$params
		);
		$this->set_routes(
			\WP_REST_Server::CREATABLE,
			[ $this, 'endpoint_controller_set_plugin_option' ],
			[ 'options' ],
			function(){ return current_user_can( 'manage_options' ); }
		);
	}

	/**
	 * endpoint_controller_set_plugin_option
	 * Post options route controller
	 * @param \Requests $request client request
	 */
	public function endpoint_controller_set_plugin_option( $request ){

		//TODO:: detect if option has been changed, wp dont do it and return a false
		//so before saving the new params, get currently loaded option and check it against the new one
		//this need to support multidimensional array and ignore missing key from the new param ( value are parsed )
		//this need to be done here or save_options return type need to change from boolean to int
		$plugin_settings = $request->get_param( 'options' );
		$saved           = MyWPCustomLogin::get_instance()->save_options( $plugin_settings );
		if( 1 === $saved ) {
			return new \WP_REST_Response([
				'code' => 'success',
				'message' => __( 'MyWP Custom Login options saved', 'mywp-custom-login'),
			]);
		}elseif( 2 === $saved ){
			return new \WP_Error(
				'error',
				__( 'MyWP Custom Login options have not change', 'mywp-custom-login')
			);
		}
		return new \WP_Error(
			'error',
			__( 'MyWP Custom Login options cant save', 'mywp-custom-login')
		);
	}

}