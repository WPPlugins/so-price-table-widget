<?php

class SiteOrigin_Widgets_Loader {
	private $file;
	private $widget_id;
	private $load_file;

	/**
	 * @param string $file The current file
	 * @param string $widget_id The widget ID
	 * @param string $load_file File that's loaded after the widget base is loaded
	 */
	function __construct($widget_id, $file, $load_file){
		$this->file = $file;
		$this->widget_id = $widget_id;
		$this->load_file = $load_file;

		add_filter( 'siteorigin_widgets_include_version', array($this, 'version_filter') );
		add_action( 'plugins_loaded', array($this, 'init'), 5 );
		add_action( 'siteorigin_widgets_base_loaded', array($this, 'load_register') );
		add_action( 'widgets_init', array($this, 'widgets_init') );
	}

	/**
	 * Lets the current loader know which version we're running.
	 *
	 * @filter siteorigin_widgets_include_version
	 * @param $versions
	 * @return mixed
	 */
	function version_filter($versions){
		$versions[ plugin_basename($this->file) ] = include(plugin_dir_path($this->file).'base/version.php');
		return $versions;
	}

	/**
	 * Initialize the base using which ever plugin has the highest version.
	 *
	 * @action plugins_loaded
	 */
	function init(){
		if( defined('SITEORIGIN_WIDGETS_BASE_PARENT_FILE') ) return;

		global $siteorigin_widget_include_versions;
		if( empty( $siteorigin_widget_include_versions ) ) {
			$siteorigin_widget_include_versions = apply_filters( 'siteorigin_widgets_include_version', array() );
			uasort($siteorigin_widget_include_versions, 'version_compare');
		}

		if( is_array($siteorigin_widget_include_versions) ) {
			$keys = array_keys($siteorigin_widget_include_versions);
			if( $keys[count($keys) - 1] == plugin_basename($this->file) ) {
				define('SITEORIGIN_WIDGETS_BASE_PARENT_FILE', $this->file);
				define('SITEORIGIN_WIDGETS_BASE_VERSION', $siteorigin_widget_include_versions[plugin_basename($this->file)]);

				include plugin_dir_path($this->file).'base/inc.php';

				do_action('siteorigin_widgets_base_loaded');
			}
		}
	}

	/**
	 * Register the widget and load its main include file.
	 *
	 * @action siteorigin_widgets_base_loaded
	 */
	function load_register(){
		siteorigin_widget_register_self($this->widget_id, $this->file);

		require_once $this->load_file;
	}

	/**
	 * Registers the widget that was created in this widget plugin.
	 *
	 * @action widgets_init
	 */
	function widgets_init(){
		$class_name = 'SiteOrigin_Widget_' . str_replace( ' ', '', ucwords( str_replace('-', ' ', $this->widget_id) ) ) . '_Widget';
		if( class_exists($class_name) ) {
			register_widget($class_name);
		}
	}
}