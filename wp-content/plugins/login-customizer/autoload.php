<?php
/**
* Plugin autoload.
*
* @package 			LoginCustomizer
* @author 			WPBrigade
* @copyright 		Copyright (c) 2021, WPBrigade
* @link 			https://loginpress.pro/
* @license			https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

 /**
 * Use to autoload needed classes without Composer.
 *
 * @param string $class The fully-qualified class name.
 * @return void
 */

spl_autoload_register( function( $class ) {

	$namespace = 'LoginCustomizer\\';
	$path      = 'src';

	// Bail if the class is not in our namespace.
	if ( 0 !== strpos( $class, $namespace ) ) {
		return;
	}

	// Remove the namespace.
	$class = str_replace( $namespace, '', $class );

	// Build the filename.
	$file = realpath( __DIR__ . "/{$path}" );
	$file = $file . DIRECTORY_SEPARATOR . str_replace( '\\', DIRECTORY_SEPARATOR, $class ) . '.php';

	// If the file exists for the class name, load it.
	if ( file_exists( $file ) ) {
		include( $file );
	}
} );