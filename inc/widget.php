<?php

class SiteOrigin_Widget_PriceTable_Widget extends SiteOrigin_Widget {
	function __construct() {
		parent::__construct(
			'sow-price-table',
			__('Price Table', 'sow-pt'),
			array(
				'description' => __('A simple Price Table.', 'sow-pt'),
				'help' => 'http://siteorigin.com/'
			),
			array(

			),
			array(
				'title' => array(
					'type' => 'text',
					'label' => __('Title', 'sow-pt'),
				),

				'columns' => array(
					'type' => 'repeater',
					'label' => __('Columns', 'sow-pt'),
					'item_name' => __('Column', 'sow-pt'),
					'fields' => array(
						'featured' => array(
							'type' => 'checkbox',
							'label' => __('Featured', 'sow-pt'),
						),
						'title' => array(
							'type' => 'text',
							'label' => __('Title', 'sow-pt'),
						),
						'subtitle' => array(
							'type' => 'text',
							'label' => __('Sub Title', 'sow-pt'),
						),

						'image' => array(
							'type' => 'media',
							'label' => __('Image', 'sow-pt'),
						),

						'price' => array(
							'type' => 'text',
							'label' => __('Price', 'sow-pt'),
						),
						'per' => array(
							'type' => 'text',
							'label' => __('Per', 'sow-pt'),
						),
						'button' => array(
							'type' => 'text',
							'label' => __('Button Text', 'sow-pt'),
						),
						'url' => array(
							'type' => 'text',
							'sanitize' => 'url',
							'label' => __('Button URL', 'sow-pt'),
						),
						'features' => array(
							'type' => 'repeater',
							'label' => __('Features', 'sow-pt'),
							'item_name' => __('Feature', 'sow-pt'),
							'fields' => array(
								'text' => array(
									'type' => 'text',
									'label' => __('Text', 'sow-pt'),
								),
								'hover' => array(
									'type' => 'text',
									'label' => __('Hover Text', 'sow-pt'),
								),
								'icon' => array(
									'type' => 'select',
									'label' => __('Icon', 'sow-pt'),
									'options' => array(
										'' => __('None', 'sow-pt')
									)
								),
								'icon_color' => array(
									'type' => 'color',
									'label' => __('Icon Color', 'sow-pt'),
								),
							),
						),
					),
				),

				'theme' => array(
					'type' => 'select',
					'label' => __('Price Table Theme', 'sow-pt'),
					'options' => array(
						'atom' => __('Atom', 'sow-pt'),
					),
				),

				'header_color' => array(
					'type' => 'color',
					'label' => __('Header Color', 'sow-pt'),
				),

				'featured_header_color' => array(
					'type' => 'color',
					'label' => __('Featured Header Color', 'sow-pt'),
				),

				'button_color' => array(
					'type' => 'color',
					'label' => __('Button Color', 'sow-pt'),
				),

				'featured_button_color' => array(
					'type' => 'color',
					'label' => __('Featured Button Color', 'sow-pt'),
				),
			),
			plugin_dir_path(__FILE__).'../'
		);
	}

	function get_column_classes($column, $i, $columns) {
		$classes = array();
		if($i == 0) $classes[] = 'ow-pt-first';
		if($i == count($columns) -1 ) $classes[] = 'ow-pt-last';
		if(!empty($column['featured'])) $classes[] = 'ow-pt-featured';

		if($i % 2 == 0) $classes[] = 'ow-pt-even';
		else $classes[] = 'ow-pt-odd';

		return implode(' ', $classes);
	}

	function column_image($image){
		$src = wp_get_attachment_image_src($image, 'full');
		?><img src="<?php echo $src[0] ?>" /> <?php
	}

	function get_style_hash($instance) {
		return substr( md5( serialize( $this->get_less_variables( $instance ) ) . SOW_PT_VERSION ), 0, 12 );
	}

	function get_template_name($instance) {
		return $this->get_style_name($instance);
	}

	function get_style_name($instance) {
		if(empty($instance['theme'])) return 'atom';
		return $instance['theme'];
	}

	function get_less_variables($instance){
		$instance = wp_parse_args($instance, array(
			'header_color' => '',
			'featured_header_color' => '',
			'button_color' => '',
			'featured_button_color' => '',
		));

		$colors = array(
			'header_color' => $instance['header_color'],
			'featured_header_color' => $instance['featured_header_color'],
			'button_color' => $instance['button_color'],
			'featured_button_color' => $instance['featured_button_color'],
		);

		if( !class_exists('SiteOrigin_Widgets_Color_Object') ) require plugin_dir_path( __FILE__ ).'../base/inc/color.php';

		if( !empty( $instance['button_color'] ) ) {
			$color = new SiteOrigin_Widgets_Color_Object( $instance['button_color'] );
			$color->lum += ($color->lum > 0.75 ? -0.5 : 0.8);
			$colors['button_text_color'] = $color->hex;
		}

		if( !empty( $instance['featured_button_color'] ) ) {
			$color = new SiteOrigin_Widgets_Color_Object( $instance['featured_button_color'] );
			$color->lum += ($color->lum > 0.75 ? -0.5 : 0.8);
			$colors['featured_button_text_color'] = $color->hex;
		}

		return $colors;
	}

	/**
	 * Modify the form to add font awesome icons
	 *
	 * @param $form
	 * @return mixed
	 */
	function modify_form($form) {
		$options = array();

		foreach(glob(plugin_dir_path(__FILE__).'../fontawesome/*.svg') as $option) {
			$name = pathinfo($option);
			$name = $name['filename'];
			$options[$name] = ucwords( str_replace('-', ' ', $name) );
		}

		$form['columns']['fields']['features']['fields']['icon']['options'] = array_merge(
			$form['columns']['fields']['features']['fields']['icon']['options'],
			$options
		);

		return $form;
	}

	function enqueue_admin_scripts(){
		wp_enqueue_script( 'siteorigin-pricetable-admin',  siteorigin_widget_get_plugin_dir_url('price-table') . 'js/admin.min.js', array('jquery'), SOW_PT_VERSION );
		wp_localize_script( 'siteorigin-pricetable-admin', 'siteoriginPricetable', array(
			'svg_url' => siteorigin_widget_get_plugin_dir_url('price-table').'fontawesome/'
		) );
	}

	function enqueue_frontend_scripts(){
		wp_enqueue_script( 'siteorigin-pricetable', siteorigin_widget_get_plugin_dir_url('price-table') . 'js/pricetable.min.js', array('jquery') );
	}
}

function sow_pt_register_widget(){
	register_widget('SiteOrigin_Widget_PriceTable_Widget');
}
add_action('widgets_init', 'sow_pt_register_widget');