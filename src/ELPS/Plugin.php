<?php
/**
 * Created by PhpStorm.
 * User: Matteo
 * Date: 24/10/2018
 * Time: 12:18
 */

namespace Teolaz\ELPS;

use \Elementor\Element_Section;

class Plugin {
	
	public function __construct() {
		/* check for updates */
		$this->checkForUpdates();
		
		add_action( 'plugins_loaded', function () {
			
			/* register activation hooks to control if Elementor is present, deactivate the plugin otherwise */
			if ( ! $this->checkElementorExistence() ) {
				add_action( 'admin_notices', [ $this, 'goOnBootstrapFailMode' ] );
				add_action( 'admin_init', function () {
					deactivate_plugins( TEOLAZ_ELPS_MAIN_FILE );
				} );
				
				return;
			}
			/* register hooks only on elementor init action */
			add_action( 'elementor/init', [ $this, 'addElementorInitHooks' ] );
			load_plugin_textdomain( 'teolaz-elps' );
		} );
		
		
	}
	
	/**
	 * Check existence just controlling launched action
	 * @return bool
	 */
	protected function checkElementorExistence() {
		if ( ! did_action( 'elementor/loaded' ) ) {
			
			return false;
		}
		
		return true;
	}
	
	/**
	 * Update checker working on github, possible thanks to package yahnis-elsts/plugin-update-checker
	 * @link https://packagist.org/packages/yahnis-elsts/plugin-update-checker
	 */
	protected function checkForUpdates() {
		\Puc_v4_Factory::buildUpdateChecker(
			'https://github.com/teolaz/elementor-login-protected-sections',
			TEOLAZ_ELPS_MAIN_FILE,
			'elementor-login-protected-sections'
		);
	}
	
	/**
	 * Add Elementor main hooks
	 */
	public function addElementorInitHooks() {
		/* add Elementor backend hooks */
		add_action( 'elementor/element/after_section_end', [ $this, 'addBackendControl' ], 10, 3 );
		
		/* add Elementor frontend hooks */
		$sectionType = Element_Section::get_type();
		add_action( "elementor/frontend/{$sectionType}/before_render", [ $this, 'addFrontendBeforeRenderHook' ] );
		add_action( "elementor/frontend/{$sectionType}/after_render", [ $this, 'addFrontendAfterRenderHook' ] );
	}
	
	/**
	 * Stolen from Elementor PRO source code, just to be sure it works
	 */
	public function goOnBootstrapFailMode() {
		$screen = get_current_screen();
		if ( isset( $screen->parent_file ) && 'plugins.php' === $screen->parent_file && 'update' === $screen->id ) {
			return;
		}
		
		$plugin            = 'elementor/elementor.php';
		$installed_plugins = get_plugins();
		
		if ( isset( $installed_plugins[ $plugin ] ) ) {
			if ( ! current_user_can( 'activate_plugins' ) ) {
				return;
			}
			
			$activation_url = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $plugin . '&amp;plugin_status=all&amp;paged=1&amp;s',
				'activate-plugin_' . $plugin );
			
			$message = '<p>' . __( 'Elementor Login Protected Section is not working because you need to activate the Elementor plugin.',
					'elementor-pro' ) . '</p>';
			$message .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $activation_url,
					__( 'Activate Elementor Now', 'elementor-pro' ) ) . '</p>';
		}
		else {
			if ( ! current_user_can( 'install_plugins' ) ) {
				return;
			}
			
			$install_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=elementor' ),
				'install-plugin_elementor' );
			
			$message = '<p>' . __( 'Elementor Login Protected Section is not working because you need to install the Elementor plugin.',
					'teolaz-elps' ) . '</p>';
			$message .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $install_url,
					__( 'Install Elementor Now', 'teolaz-elps' ) ) . '</p>';
		}
		
		echo '<div class="error"><p>' . $message . '</p></div>';
	}
	
	/**
	 * Add section under Advanced Tab of Section element
	 */
	public function addBackendControl( $element, $section_id, $arg ) {
		/** @var \Elementor\Element_Base $element */
		if (
			Element_Section::get_type() == $element->get_name() &&
			'_section_responsive' == $section_id
		) {
			LoginProtectedContent::registerSection( $element );
		}
	}
	
	/**
	 * Start removing content if needed
	 *
	 * @param $element
	 */
	public function addFrontendBeforeRenderHook( $element ) {
		/** @var \Elementor\Element_Base $element */
		if ( LoginProtectedContent::isProtectionEnabled( $element ) ) {
			ob_start();
		}
	}
	
	/**
	 * End removing content if needed
	 *
	 * @param $element
	 */
	public function addFrontendAfterRenderHook( $element ) {
		/** @var \Elementor\Element_Base $element */
		if ( LoginProtectedContent::isProtectionEnabled( $element ) ) {
			// clean buffer so all the content is discharged
			ob_get_clean();
		}
	}
	
}