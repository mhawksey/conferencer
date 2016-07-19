<?php

new Conferencer_Chair();
class Conferencer_Chair extends Conferencer_CustomPostType {
	var $slug = 'chair';
	var $archive_slug = 'chairs';
	var $singular = "Chair";
	var $plural = "Chairs";
	
	function columns($columns) {
		$columns = parent::columns($columns);
		$columns['conferencer_type_session_count'] = "Chairs";
		return $columns;
	}
}