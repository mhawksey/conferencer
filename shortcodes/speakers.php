<?php

/* ============================================================================

	You can override the session display function in your own template.
	In your own functions.php, define:
		conferencer_speaker_display_session($session, $options)
	
============================================================================ */

new Conferencer_Shortcode_Speakers();
class Conferencer_Shortcode_Speakers extends Conferencer_Shortcode {
	var $shortcode = 'speakers';
	var $defaults = array(
		'link_speakers' => false,
		'limit_list' => false,
	);
	
	var $buttons = array();
	
	function prep_options() {
		parent::prep_options();
		
	}

	function content() {
		extract($this->options);
		$conferencer_options = get_option('conferencer_options');

		// Define main speaker_type variable

		$speakers = Conferencer::get_posts('speaker', false, 'title_sort');
		foreach (array_keys($speakers) as $id) {
			Conferencer::add_meta($speakers[$id]);
		}

		$speaker_type = array();
		$speaker_type_name = array();
	
		// Fill speaker with empty time slot rows
	
		foreach (Conferencer::get_posts('company', false, 'order_sort') as $company_id => $company) {
			$speaker_type[$company_id] = array();
			$speaker_type_name[$company_id] = $company->post_title;

		}
		$speaker_type[0] = array(); // for unscheduled time slots

		// loop through the speakers and add to correct speaker_type
		foreach ($speakers as $speaker) {
			$company_id = $speaker->company ? $speaker->company : 0;
			$speaker_type[$company_id][$speaker->ID] = $speaker;
			$column_post_counts[$company_id]++;

		}
	
		
		if (deep_empty($speaker_type[0])) unset($speaker_type[0]);
	
		ob_start();
	
		?>
      	<!--Import materialize.css-->
		<?php  echo '<link type="text/css" rel="stylesheet" href="'.plugins_url( 'css/materialize.min.css?v=1' , dirname(__FILE__) ).'"  media="screen,projection"/>'; ?>
		
		<div class="conferencer_speaker">
		  <?php foreach ($speaker_type_name as $speaker_type_id => $speaker_title) { ?>
		  <?php  print_r("<!-- ".$speaker_title." -->"); ?>
			<?php  if (($limit_list && strpos($limit_list, $speaker_title) !== false) || !$limit_list ) { ?>
				<div class="row">
				<h2><?php echo($speaker_title); ?></h2>
					<?php 
					foreach ($speaker_type[$speaker_type_id] as $speaker_id => $speaker){ ?>
						<div class="col s6 m4">
						<div class="card small">
						<div class="card-image">
							<?php if ($link_speakers) echo "<a href='".get_permalink($speaker_id)."' title='".esc_attr($speaker->post_title)."' >"; ?>
								<?php echo get_the_post_thumbnail( $speaker_id , 'thumbnail', array( 'alt' =>  esc_attr($speaker->post_title))); ?>
							<?php if ($link_speakers) echo "</a>"; ?>
						</div>
						<div class="card-content">
						<span class="card-title">
							<?php if ($link_speakers) echo "<a class='speaker' href='".get_permalink($speaker_id)."' title='".esc_attr($speaker->post_title)."' >"; ?>
								<?php echo($speaker->post_title); ?> </span>
							<?php if ($link_speakers) echo "</a>"; ?>
						<?php 
							//$content = $speaker->post_content;
							$content = $speaker->title;
							$content = apply_filters('the_content', $content);
							$content = str_replace(']]>', ']]&gt;', $content);
							echo $content;
						?>
							
						</div>
						</div>
					</div>
					<?php }	?>
				</div>
				<?php } ?>
			<?php } ?>
		</div> <!-- .conferencer_speaker -->
		<?php  echo '<script src="'.plugins_url( 'js/materialize.min.js?v=1' , dirname(__FILE__) ).'"></script>'; ?>
		

	<?php
		// Retrieve and return buffer
		return ob_get_clean();
	} 
}
	