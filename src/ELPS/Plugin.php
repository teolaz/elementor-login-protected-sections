<?php
/**
 * Created by PhpStorm.
 * User: Matteo
 * Date: 24/10/2018
 * Time: 12:18
 */

namespace Teolaz\ELPS;


class Plugin {
	
	public function __construct() {
		/* decide whether to enable the plugin or not */
		if ( ! $this->checkElementorExistence() ) {
			return;
		}
		
		/* check for updates */
		$this->checkForUpdates();
		
		/* add Elementor backend hooks */
		add_action( 'elementor/element/after_section_end', [ $this, 'addBackendControl' ], 10, 3 );
		
		/* add Elementor frontend hooks */
		$sectionType = \Elementor\Element_Section::get_type();
		add_action( "elementor/frontend/{$sectionType}/before_render", [ $this, 'addFrontendBeforeRenderHook' ] );
		add_action( "elementor/frontend/{$sectionType}/after_render", [ $this, 'addFrontendAfterRenderHook' ] );
	}
	
	protected function checkElementorExistence() {
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', [ $this, 'goOnBootstrapFailMode' ] );
			
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
			dirname( __FILE__ ) . '/../../elementor-login-protected-sections.php',
			'elementor-login-protected-sections'
		);
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
	
	public function addBackendControl( $element, $section_id, $arg ) {
		/** @var \Elementor\Element_Base $element */
		if (
			\Elementor\Element_Section::get_type() == $element->get_name() &&
			'_section_responsive' == $section_id
		) {
			LoginProtectedContent::registerSection( $element );
		}
	}
	
	public function addFrontendBeforeRenderHook( $element ) {
		/** @var \Elementor\Element_Base $element */
		if ( LoginProtectedContent::isProtectionEnabled( $element ) ) {
			ob_start();
		}
	}
	
	public function addFrontendAfterRenderHook( $element ) {
		/** @var \Elementor\Element_Base $element */
		if ( LoginProtectedContent::isProtectionEnabled( $element ) ) {
			// clean buffer so all the content is discharged
			ob_get_clean();
		}
	}
	
}