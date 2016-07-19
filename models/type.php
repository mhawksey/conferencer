<?php

new Conferencer_Type();
class Conferencer_Type extends Conferencer_CustomPostType {
	var $slug = 'type';
	var $archive_slug = 'types';
	var $singular = "Type";
	var $plural = "Types";
	
	function columns($columns) {
		$columns = parent::columns($columns);
		$columns['conferencer_type_session_count'] = "Types";
		return $columns;
	}
}