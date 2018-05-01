<?php

namespace OpenClub;

require_once( OPENCLUB_CSV_PLUGIN_DIR . '/vendor/class-gamajo-template-loader.php' );

class Template_Loader extends \Gamajo_Template_Loader {

	protected $filter_prefix = 'openclub';

	protected $theme_template_directory = 'templates';

	protected $plugin_directory = OPENCLUB_CSV_PLUGIN_DIR;

	public function set_plugin_dir_path( $path ) {
		$this->plugin_directory = $path;
	}

	/**
	 * Wrapper
	 *
	 * @param $slug
	 * @param null $name
	 * @param bool $load
	 *
	 * @return string
	 */
	public function get_template( $slug, $name = null, $load = true ) {

		$out = '';
		ob_start();
		$this->get_template_part( $slug, $name, $load );
		$out .= ob_get_clean();

		return $out;

	}


}