<?php
namespace whodunit\mywpCustomLogin\utility;

use whodunit\mywpCustomLogin\MyWPCustomLogin;

class WordPress {

	/**
	 * recursive_wp_parse_args
	 * This will merge data from two array, this version support multidimensional array and will parse them too.
	 * behave like wp_parse_args but recursive
	 *
	 * @param $a array $a usually default data
	 * @param $b array $b this data will overwrite $a data if both exist.
	 * @param $replace_by_empty_value boolean, flag if true empty value from array $b will replace $a value event if they are empty, is false by default
	 * @return array of merged data from $a and $b
	 */
	static function recursive_wp_parse_args( $a, $b, $replace_by_empty_value = false ) {
		$a      = (array) $a;
		$b      = (array) $b;
		$result = $b;
		foreach( $a as $k => &$v ) {
			if( is_array( $v ) ){
				if( ! isset( $result[ $k ] ) ){ $result[ $k ] = []; }
				$result[ $k ] = self::recursive_wp_parse_args( $v, $result[ $k ] );
			}elseif( $replace_by_empty_value || '' !== $v ){
				$result[ $k ] = $v;
			}
		}
		return $result;
	}

	/**
	 * recursive_sanitize_text_field
	 * behave like sanitize_text_field but recursive
	 *
	 * @param $value string | array, array of string or a string to sanitize
	 * @return string | array, sanitized given array of string or string
	 */
	static function recursive_sanitize_text_field( $value ) {
		if( is_string( $value ) ){
			$value = sanitize_text_field( $value );
		}elseif( is_array( $value ) ){
			foreach( $value as &$entry ){
				$entry = self::recursive_sanitize_text_field( $entry );
			}
		}else{
			$value = null;
		}
		return $value;
	}

	/**
	 * update_localize_script
	 * work like localize_script but can be updated without loosing data precedently defined by localize_script or this method.
	 *
	 * @param $handle string, script handle name
	 * @param $var_name string, var name use to store data given by $localized_data
	 * @param $localized_data array, data put into var $var_name
	 * @return void
	 */
	static function update_localize_script( $handle, $var_name, $localized_data ){
		global $wp_scripts;
		$script_data = $wp_scripts->get_data( $handle, 'data' );

		if( empty( $script_data ) ){
			wp_localize_script( $handle, $var_name, $localized_data);
		}else{
			if( ! is_array( $script_data ) ){
				//TODO::add a json integrity check
				$script_data = json_decode( str_replace('var '.$var_name.' = ', '', substr( $script_data, 0, -1 ) ), true );
			}
			foreach( $script_data as $key => $value ){
				$localized_data[$key] = $value;
			}
			$wp_scripts->add_data( $handle, 'data', '' );
			wp_localize_script( $handle, $var_name, $localized_data );
		}
	}

	/**
	 * wordpress_dot_org_trad_exist
	 * check if translation for the WP public repository exist
	 * @return bool, return true if it finds public translation.
	 * @throws \Exception
	 */
	static function wordpress_dot_org_trad_exist(){
		return file_exists( WP_LANG_DIR.'/plugins/'.MyWPCustomLogin::get_instance()->get_basename().'-'.determine_locale().'.mo' );
	}

	/**
	 * object_error_log
	 * log objects
	 * @return void
	 */
	static function object_error_log( $var ){
		if ( true === WP_DEBUG ) {
			if ( is_array( $var ) || is_object( $var ) ) {
				error_log( print_r( $var, true) );
			} else {
				error_log( $var );
			}
		}
	}

	/**
	 * remove_url_arguments
	 * object parsing, this tool remove every 'url' param from an object or an array
	 * use for parsing zendesk api object
	 * //TODO::maybe this can be a think if this have some params like what params to remove...
	 *
	 * @param $object
	 * @return void
	 */
	static function remove_url_arguments( &$object ){
		if ( is_array( $object ) || is_object( $object ) ) {
			foreach ( $object as $prop => &$value ) {
				if( is_array( $value ) || is_object( $value ) ){
					self::remove_url_arguments( $value );
				} elseif( 'url' === $prop ){
					unset( $object->{$prop} );
				}
			}
		}
	}

	/**
	 * esc_json
	 * steal with no shame from woocommerce
	 * https://woocommerce.github.io/code-reference/files/woocommerce-includes-wc-formatting-functions.html#source-view.1453
	 * Escape JSON for use on HTML or attribute text nodes.
	 *
	 * @param string $json JSON to escape.
	 * @param bool   $html True if escaping for HTML text node, false for attributes. Determines how quotes are handled.
	 * @return string Escaped JSON.
	 */
	static function esc_json( $json, $html = false ) {
		return _wp_specialchars( $json, $html ? ENT_NOQUOTES : ENT_QUOTES, 'UTF-8', true );
	}

}