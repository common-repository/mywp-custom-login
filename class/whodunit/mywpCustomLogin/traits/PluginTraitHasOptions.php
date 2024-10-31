<?php
namespace whodunit\mywpCustomLogin\traits;

use whodunit\mywpCustomLogin\utility\WordPress;

/**
 * PluginTraitHasOptions
 * this trait adds data handling capability to the plugin.
 * use this to store option
 * set options meta key whit set_options_meta_key()
 * this trait doesn't need init, but you need to call load_options()
 * at least once before getting access to stored data.
 */
trait PluginTraitHasOptions{

	protected $options_meta_key = null;
	protected $default_options  = [];
	protected $options          = [];
	protected $options_filters  = [];

	/**
	 * filter_options
	 * apply pre defined filter
	 *
	 * @param string $key use key to identify what filter to apply
	 * @param mixed $value value to filter as a reference
	 * @return boolean return true if a filter have been applied
	 */
	protected function filter_options( $key, &$value ){
		if( isset( $this->options_filters[ $key ] )  && is_callable( $this->options_filters[ $key ] ) ){
			$value = $this->options_filters[ $key ]( $value );
			return true;
		}
		return false;
	}

	/**
	 * load_options
	 * load saved option into $this->options
	 *
	 * @return void
	 */
	protected function load_options(){
		if( is_null( $this->options_meta_key ) ){ return; }
		$options = get_option( $this->options_meta_key );
		if( $options ){
			$this->options = WordPress::recursive_wp_parse_args( $options, $this->options, true );
		}
	}

	/**
	 * reload_options
	 * reset to default options and reload options from wp option table.
	 * @return void
	 */
	public function reload_options(){
		$this->options = $this->default_options;
	}

	/**
	 * save_options
	 * save current option and the given array as serialised array into the site meta table
	 * this use $this->set_options methode, this don't modify existing options non specified into the $options array
	 *
	 * @param array $options an array of options to save
	 * @return boolean return true is options has been updated, else return false
	 * Careful this can not differentiate a writing error from unchanged options
	 */
	public function save_options( array $options = [] ){
		if( is_null( $this->options_meta_key ) ){ return 0; }
		$this->set_options( $this->options, $options );
		$old_options = get_option( $this->options_meta_key );
		if ( json_encode( $this->options ) === json_encode( $old_options ) ) { return 2; }
		return ( update_option( $this->options_meta_key, $this->options ) ) ? 1 : 0;
	}

	//----GETTER

	/**
	 * get_options_meta_key
	 * getter
	 *
	 * @return null
	 */
	public function get_options_meta_key(){
		return $this->options_meta_key;
	}

	/**
	 * get_options
	 * getter
	 * return saved option
	 *
	 * @param null $index if index is define, it will return the specified option, else return all options
	 * @return array|mixed|null
	 */
	public function get_options( $index = null ){
		if( is_null( $index ) ){ return $this->options; }
		if( isset( $this->options[ $index ] ) ){ return $this->options[ $index ]; }
		return null;
	}

	//----SETTER

	/**
	 * set_options_meta_key
	 * setter
	 *
	 * @param $meta_key
	 * @return null
	 */
	public function set_options_meta_key( $meta_key ){
		if( is_string( $meta_key ) ){
			$this->options_meta_key = sanitize_title( $meta_key );
			return $this->options_meta_key;
		}
		return null;
	}

	/**
	 * set_filter_options
	 * set a input filter on a option key
	 *
	 * @param string $key the option key where you want to apply the filter, be careful options is a multi-dimensional array
	 * @param callable $filter the filter callable has only 1 argument value ( value can be change by reference or when return by filter )
	 * @return void
	 */
	protected function set_filter_options( $key, callable $filter ){
		$this->options_filters[ $key ] = $filter;
	}

	/**
	 * init_default_options
	 * setter
	 * set default options data, this will be overwritten by any data in base,
	 * use to provide default and working settings on installation.

	 * @param string $options default ptions array
	 * @return void
	 */
	protected function set_default_options( array $options ){
		$this->default_options = $options;
		$this->options = $options;
	}

	/**
	 * set_options
	 * set option, is recursive for multidimensional array
	 * do not remove or clear existing value not specified in $values
	 *
	 * @param $options, options array to update
	 * @param $values, new value, can be a multidimensional array
	 * @return void
	 */
	protected function set_options( &$options, $values ){
		foreach( $values as $k=>&$v ){
			//if not filtered and value is a array use recursion
			if( ! $this->filter_options( $k, $v ) && is_array( $v )){
				if( ! isset ( $options[ $k ] ) ){ $options[ $k ] = []; }
				$this->set_options($options[ $k ], $v );
			}else{
				$options[ $k ] = $v;
			}
		}
	}

}