<?php

new Conferencer_Shortcode_Session_Meta();
class Conferencer_Shortcode_Session_Meta extends Conferencer_Shortcode {
	var $shortcode = 'session_meta';
	var $defaults = array(
		'post_id' => false,
		
		'show' => "time,speakers,room,track,type",
		
		'title_prefix' => "",
		'time_prefix' => "",
		'speakers_prefix' => "Authors: ",
		'room_prefix' => "Rooom: ",
		'track_prefix' => "Theme: ",
		'type_prefix' => "Type: ",
		'chair_prefix' => "Chair: ",
		'sponsors_prefix' => "Session sponsor: ",

		'title_suffix' => "",
		'time_suffix' => "",
		'speaker_suffix' => "",
		'room_suffix' => "",
		'track_suffix' => "",
		'type_suffix' => "",
		'chair_suffix' => "",
		'sponsor_suffix' => "",

		'date_format' => 'D, M j Y',
		'time_format' => 'g:ia',
		'time_separator' => ' &ndash; ',
		
		'link_all' => true,
		'link_title' => true,
		'link_speakers' => true,
		'link_room' => true,
		'link_track' => true,
		'link_type' => true,
		'link_chair' => true,
		'link_sponsors' => true,

		'sponsor_logo' => true
	);

	var $buttons = array('session_meta');

	function add_to_page($content) {
		if (get_post_type() == 'session') {
			$meta = function_exists('conferencer_session_meta')
					? conferencer_session_meta($post)
					: do_shortcode('[session_meta]');
			$content = $meta.$content;
		}
		return $content;
	}

	function prep_options() {
		parent::prep_options();
		
		if (!$this->options['post_id'] && isset($GLOBALS['post'])) {
			$this->options['post_id'] = $GLOBALS['post']->ID;
		}
		
		if ($this->options['link_all'] === false) {
			$this->options['link_title'] = false;
			$this->options['link_speakers'] = false;
			$this->options['link_room'] = false;
			$this->options['link_track'] = false;
			$this->options['link_type'] = false;
			$this->options['link_sponsors'] = false;
		}
	}
	
	function content() {
		extract($this->options);
	
		$post = get_post($post_id);
		if (!$post) return "[Shortcode error (session_meta): Invalid post_id.  If not used within a session page, you must provide a session ID using 'post_id'.]";
		if ($post->post_type != 'session') {
			if ($post_id) return "[Shortcode error (session_meta): <a href='".get_permalink($post_id)."'>$post->post_title</a> (ID: $post_id, type: $post->post_type) is not a session.]";
			else return "[Shortcode error (session_meta): This post is not a session.  Maybe you meant to supply a session using post_id.]";
		}
		
		Conferencer::add_meta($post);

		$meta = array();
		foreach (explode(',', $show) as $type) {
			$type = trim($type);
			
			switch ($type) {
				case 'title':
					$html = $post->post_title;
					if ($link_title) $html = "<a href='".get_permalink($post->ID)."'>$html</a>";
					$meta[] = "<div class='title'>".$title_prefix.$html.$title_suffix."</div>";
					break;
				
				case 'time':
					if ($post->time_slot) {
						$starts = get_post_meta($post->time_slot, '_conferencer_starts', true);
						$ends = get_post_meta($post->time_slot, '_conferencer_ends', true);
						$html = date($date_format, $starts).", ".date($time_format, $starts).$time_separator.date($time_format, $ends);
						$meta[] = "<div class='time'>".$time_prefix.$html.$time_suffix."</div>";
					}
					break;
		
				case 'speakers':
					if (count($speakers = Conferencer::get_posts('speaker', $post->speakers, 'natural_sort')) && $speakers[0]->ID) {
						//$values = array_reverse($post->speakers);
						$html = comma_separated_post_titles($speakers, $link_speakers);
						//$html .= "<!-- ".json_encode($values). " -->";
						//print_r($speakers);
						$html .= "<!-- ".$speakers[0]->ID. " -->";
						$meta[] = "<div class='speakers'>".$speakers_prefix.$html.$speaker_suffix."</div>";
					}
					break;
		

				case 'room':
					if ($post->room) {
						$html = get_the_title($post->room);
						if ($link_room) $html = "<a href='".get_permalink($post->room)."'>$html</a>";
						$meta[] = "<div class='room'>".$room_prefix.$html.$room_suffix."</div>";
					}
					break;

				case 'track':
					if ($post->track) {
						$html = get_the_title($post->track);
						if ($link_track) $html = "<a href='".get_permalink($post->track)."'>$html</a>";
						$meta[] = "<div class='track'>".$track_prefix.$html.$track_suffix."</div>";
					}
					break;
				
				case 'type':
					if ($post->type) {
						$html = get_the_title($post->type);
						if ($link_type) $html = "<a href='".get_permalink($post->type)."'>$html</a>";
						$meta[] = "<div class='type'>".$type_prefix.$html.$type_suffix."</div>";
					}
					break;
				
				case 'chair':
					if ($post->chair) {
						$html = get_the_title($post->chair);
						if ($link_chair) $html = "<a href='".get_permalink($post->chair)."'>$html</a>";
						$meta[] = "<div class='chair'>".$chair_prefix.$html.$chair_suffix."</div>";
					}
					break;
					
				case 'sponsors':
					if (is_array($post->sponsors)) {
						print_r("<!-- ".$post->sponsors." -->");
						$html = get_the_title($post->sponsors[0]);
						if ($sponsor_logo && !$link_sponsors) $html = get_the_post_thumbnail( $post->sponsors[0], 'thumbnail' );
						if ($sponsor_logo && $link_sponsors) $html = "<div class='agenda_sponsor_logo'><a href='".get_permalink($post->sponsors[0])."' title='" . esc_attr( $html ) . "'>".get_the_post_thumbnail( $post->sponsors[0],'medium', array( 'style' => 'max-width:200px;height:auto;' ) )."</a></div>";
						if ($link_sponsors && !$sponsor_logo) $html = "<a href='".get_permalink($post->sponsors[0])."'>$html</a>";
						$meta[] = "<div class='sponsor'>".$sponsors_prefix.$html.$sponsor_suffix."</div>";
					}
					break;
					
				default:
					$meta[] = "Unknown session attribute";
			}
		}

		return count($meta) ? "<div class='session_meta'>".implode("", $meta)."</div>" : '';
	}
}