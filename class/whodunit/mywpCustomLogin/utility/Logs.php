<?php

namespace whodunit\mywpCustomLogin\utility;

use whodunit\mywpCustomLogin\MyWPCustomLogin;

class Logs{

	/**
	 * append_log
	 *
	 * @param $message
	 * @param $vars
	 * @param $context
	 * @param $file_prefix
	 * @return void
	 * @throws \Exception
	 */
	public static function append_log( $message, $vars = null, $context = null, $file_prefix = 'logs' ){
		$dir_log = MyWPCustomLogin::get_instance()->get_dir().'logs/';
		if( ! file_exists( $dir_log ) ) {
			if( ! mkdir( $dir_log, 0755 ) && ! is_dir( $dir_log ) ){ return; }
		}
		$log_file_name = $dir_log.$file_prefix.'_'.date("j.n.Y").'.log';
		$log  = "[".date("F j, Y, g:i a")."]"
			.( ( is_string( $context ) ) ? "[".$context."]".PHP_EOL : PHP_EOL )
			.$message.PHP_EOL
		    .( ( $vars ) ? "-".strtoupper( gettype( $vars ) )."-".PHP_EOL.print_r( $vars, true ).PHP_EOL : '' );
		file_put_contents( $log_file_name, $log, FILE_APPEND );
	}

}