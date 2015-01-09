<?php
/**
 * @package Opes PhpMailer Patch - Contact Form 7
 */
/*
Plugin Name: Opes PhpMailer Patch - Contact Form 7
Plugin URI: https://wordpress.org/plugins/opes-phpmailer-patch-contact-form-7/
Description: This plugin fixes a problem with setting the field 'From' and 'FromName' while sending e-mail forms from the Contact Form 7 plugin i.e. having set SMTP with fixed 'From' and 'FromName' fields
Version: 1.0.0
Author: Paweł Twardziak
Author URI: http://it-opes.com/
License: GPLv2 or later
Text Domain: opes_phpmailer_patch_cf7
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

class OpesPhpMailerPatchCf7 {

	private $sender = array();
	private $senderName = array();
	private $global_components = array();

	private function __construct( $params ) {
		add_filter( 'wpcf7_mail_components' , array( $this , 'use_wpcf7_mail_components' ) , 999999 , 2 );
	}

	function use_wpcf7_mail_components( $components, $current_contact_form ) {

		$this->global_components = $components;

		$if_sender = preg_match( '/<(.*?)>/' , $this->global_components[ 'sender' ] , $this->sender );
		if ( $if_sender == 1 ) {
		//echo "<pre>".print_r($sender,true)."</pre>";
			if ( filter_var( trim( $this->sender[1] ) , FILTER_VALIDATE_EMAIL ) ) {
				add_action('phpmailer_init', array( $this , 'phpmailer_init_function' ) , 999999 );
			}
		} else if ( filter_var( trim( $this->global_components[ 'sender' ] ) , FILTER_VALIDATE_EMAIL ) ) {
			$this->sender[1] = trim( $this->global_components[ 'sender' ] );
			if ( filter_var( trim( $this->sender[1] , FILTER_VALIDATE_EMAIL ) ) ) {
				add_action('phpmailer_init', array( $this , 'phpmailer_init_function' ) , 999999 );
			}
		}
		return $components;

	}

	function phpmailer_init_function( $phpmailer ) {

		//echo "<pre>".print_r($components[ 'sender' ],true)."</pre>";
		if 	(
				is_array( $this->sender )
				&& count( $this->sender ) > 0
				&& isset( $this->sender[1] )
				&& filter_var( trim( $this->sender[1] ) , FILTER_VALIDATE_EMAIL )
			) {

			$phpmailer->set( 'From' , trim( $this->sender[1] ) );

		}

		$if_sender_name = preg_match( '/(.*?)</' , $this->global_components[ 'sender' ] , $this->senderName );
		if 	( 
				is_array( $this->senderName ) 
				&& count( $this->senderName ) > 0
				&& isset( $this->senderName[1] )
				&& !filter_var( trim( $this->senderName[1] ) , FILTER_VALIDATE_EMAIL )
				&& trim( $this->senderName[1] ) != ''
			) {

			$phpmailer->set( 'FromName' , trim( $this->senderName[1] ) );

		} /*else if ( 
				!filter_var( trim( $this->global_components[ 'sender' ] ) , FILTER_VALIDATE_EMAIL ) 
				&& trim( $this->global_components[ 'sender' ] ) != '' 
			) {

			$this->senderName[1] = trim( $this->global_components[ 'sender' ] );
			$phpmailer->set( 'FromName' , $this->senderName[1] );

		}*/

	}

	public static function initOpesPhpMailerPatchCf7( $params ) {
		new OpesPhpMailerPatchCf7( $params );
	}
}

OpesPhpMailerPatchCf7::initOpesPhpMailerPatchCf7( array() );











