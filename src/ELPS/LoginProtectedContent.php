<?php
/**
 * Created by PhpStorm.
 * User: Matteo
 * Date: 23/10/2018
 * Time: 14:53
 */

namespace Teolaz\ELPS;

use Elementor\Element_Base;
use Elementor\Controls_Manager;

class LoginProtectedContent {
	
	const SECTION_ID = 'teolaz-elps';
	const CONTROL_ENABLED_ID = 'teolaz-elps_loggedin_control';
	const CONTROL_ENABLED_VALUE_DISABLED = 'disabled';
	const CONTROL_ENABLED_VALUE_ENABLED_LOGGEDOUT = 'enabled-logged-out';
	const CONTROL_ENABLED_VALUE_ENABLED_LOGGEDIN = 'enable-logged-in';
	
	final public function __construct() {
	}
	
	public static function registerSection( Element_Base $element ) {
		$element->start_controls_section(
			self::SECTION_ID,
			[
				'label' => __( 'Login Protected Content', 'teolaz-elps' ),
				'tab'   => Controls_Manager::TAB_ADVANCED,
			]
		);
		
		$element->add_control(
			self::CONTROL_ENABLED_ID,
			[
				'label'   => __( 'Enable Login Protected Content', 'teolaz-elps' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					self::CONTROL_ENABLED_VALUE_DISABLED          =>
						__( 'Disabled', 'teolaz-elps' ),
					self::CONTROL_ENABLED_VALUE_ENABLED_LOGGEDOUT =>
						__( 'Enabled only for logged out users', 'teolaz-elps' ),
					self::CONTROL_ENABLED_VALUE_ENABLED_LOGGEDIN  =>
						__( 'Enabled only for logged in users', 'teolaz-elps' ),
				],
				'default' => 'disabled',
			]
		);
		
		$element->end_controls_section();
	}
	
	public static function isProtectionEnabled( Element_Base $element ) {
		/** @var Element_Base $settings */
		$settings = $element->get_controls_settings();
		if (
			is_array( $settings ) &&
			array_key_exists( LoginProtectedContent::CONTROL_ENABLED_ID, $settings ) &&
			( $enabled = $settings[ LoginProtectedContent::CONTROL_ENABLED_ID ] ) != LoginProtectedContent::CONTROL_ENABLED_VALUE_DISABLED
		) {
			if (
				(
					$enabled == LoginProtectedContent::CONTROL_ENABLED_VALUE_ENABLED_LOGGEDIN &&
					! is_user_logged_in()
				) ||
				(
					$enabled == LoginProtectedContent::CONTROL_ENABLED_VALUE_ENABLED_LOGGEDOUT &&
					is_user_logged_in()
				)
			) {
				
				return true;
			}
		}
		
		return false;
	}
}