<?php

namespace OpenClub;

require_once( OPENCLUB_CSV_PLUGIN_DIR . '/vendor/class-gamajo-template-loader.php' );

class Template_Loader extends \Gamajo_Template_Loader {

	protected $filter_prefix = 'openclub';

	protected $theme_template_directory = 'templates';

	protected $plugin_directory = OPENCLUB_CSV_PLUGIN_DIR;

}