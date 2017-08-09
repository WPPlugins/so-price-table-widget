

<div class="ow-pt-columns-basic">

	<?php foreach($instance['columns'] as $i => $column) : ?>
		<div class="ow-pt-column <?php echo $this->get_column_classes($column, $i, $instance['columns']) ?>" style="width: <?php echo round(100/count($instance['columns']), 3) ?>%">
			<div class="ow-pt-title"><?php echo esc_html($column['title']) ?></div>

			<?php if(!empty($column['image'])) : ?>
				<div class="ow-pt-image">
					<?php $this->column_image($column['image']) ?>
				</div>
			<?php endif; ?>

			<div class="ow-pt-details">
				<div class="ow-pt-button">
					<a href='<?php echo esc_url($column['url']) ?>' class="ow-pt-link"><?php echo esc_html($column['button']) ?></a></div>
				<div class="ow-pt-price"><?php echo esc_html($column['price']) ?></div>
				<div class="ow-pt-per"><?php echo esc_html($column['per']) ?></div>
			</div>

			<div class="ow-pt-features">
				<?php foreach($column['features'] as $feature) : ?>
					<div class="ow-pt-feature">
						<p data-tooltip-text="<?php echo esc_attr($feature['hover']) ?>">
							<?php if(!empty($feature['icon']) && $feature['icon'] != 'none') : ?>
								<div class="ow-pt-icon" data-icon="<?php echo esc_attr($feature['icon']) ?>" data-icon-color="<?php echo esc_attr($feature['icon_color']) ?>"></div>
							<?php endif ?>
							<?php echo wp_kses_post($feature['text']) ?>
						</p>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endforeach; ?>


	<?php
	global $siteorigin_price_table_icons;
	if( empty($siteorigin_price_table_icons) ) $siteorigin_price_table_icons = array();
	foreach($instance['columns'] as $i => $column){
		foreach($column['features'] as $feature) {
			if(!empty($feature['icon']) && empty($siteorigin_price_table_icons[$feature['icon']])) {
				$siteorigin_price_table_icons[$feature['icon']] = true;
				echo '<div style="display:none" id="so-pt-icon-'.$feature['icon'].'">';
				readfile(plugin_dir_path(__FILE__).'../fontawesome/'.$feature['icon'].'.svg');
				echo '</div>';
			}
		}
	}
	?>

</div>