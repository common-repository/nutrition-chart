<?php
/**
 * Plugin Name: Nutrition Chart
 * Description: Shortcode <code>[nutrition protein="145" carbs="34" fat="15" calories="540"]</code> displays the nutrition ingredients breakdown.
 * Version: 1.0
 * Author: Denis Buka
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


function chart_shortcode( $atts ) {

	wp_enqueue_script( 'cr-admin-scripts', plugin_dir_url(__FILE__).'assets/Chart.min.js', array('jquery'));	
	wp_enqueue_style( 'cr-admin-styles', plugin_dir_url(__FILE__).'assets/nutrition-chart.css', false, '1.1', 'all');	

	add_action( 'wp_footer', function () { ?>
	<script type='text/javascript'>
	(function($){	
		$(document).ready(function(){	
			$('.nutrition-chart').each(function() {
				var values = $(this).data('values').split(',');
				var keys = $(this).data('keys').split(',');
				var colors = $(this).data('colors').split(',');
				new Chart($(this), {
					type: 'doughnut',
					data: {
					  labels: keys,
					  datasets: [
						{
						  borderWidth: 5,
						  hoverBorderColor: "#FFFFFF",
						  backgroundColor: colors,
						  data: values
						}
					  ]
					},
					options: {
					  cutoutPercentage: 70,
					  legend: {
						 display: false
					  },
					}
				});
			});
		});
	})(jQuery);
	</script>
	<?php });

	$color_defaults = array('protein'=>"#f39231",'carbs'=>"#54c0ad", 'fat'=>"#6e87fc");
	
	$out .= '<div class="nutrition-wrap">';
	$out .= '<div class="nutrition-bars">';

	foreach($atts as $k=>$v) {
		if($k == 'calories') continue;
		$nutrients[$k] = $v;
	}
	$total_grams = array_sum($nutrients);

	foreach($nutrients as $k=>$v) {
		if($k != 'calories') {
			$colors[$k] = $color_defaults[$k];
			$grams[$k] = explode('|',$v)[0];
			$bar_width = $v / ($total_grams / 100);
			$keys[$k] = $k;
			$out .= '<div class="nutrition-bars-inner">
						<div class="labels-wrap">
							<label class="key">'.$k.'</label>
							<label class="grams">'.$grams[$k].'g</label>
							<div style="clear:both;"></div>
						</div>
						<div class="bar-outer"><div class="bar-inner '.$k.'" style="width:'.$bar_width.'%;background:'.$colors[$k].'"></div></div>
					</div>';
		}
	}
	$out .= '</div>';	
	$out .= '<div class="nutrition-canvas-wrap">';	
	$out .= isset($atts['calories']) ? '<div class="nutrition-calories">'.$atts['calories'].'<label>calories</label></div>' : '';
	$out .= '<canvas data-values="'.implode(',',$nutrients).'" data-keys="'.implode(',',$keys).'" data-colors="'.implode(',',$colors).'" id="nutrition-chart-'.get_the_ID().'" class="nutrition-chart" width="200" height="200"></canvas>';
	$out .= '</div>';	
	$out .= '<div style="clear:both;"></div>';
	$out .= '</div>';	
	return $out;
}
add_shortcode( 'nutrition', 'chart_shortcode' );

?>