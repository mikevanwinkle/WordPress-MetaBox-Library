<?php
/*
**
** Custom Columns For the cmb_Meta_Box class
**
**/

foreach($meta_boxes as $meta_box) {
	new cmb_Custom_Column($meta_box);
}


class cmb_Custom_Column {
	public $_meta_box;
	private $current_field;
	public $custom_cols;
	public $actions;
	
		function __construct($meta_box) {
			if ( !is_admin() ) return;
			$this->_meta_box = $meta_box;
			$this->setup();
			$this->columns();
			//print_r($this->_meta_box);
		}
	
		function setup() {
			$fields = $this->_meta_box['fields']; 
			foreach($fields as $field) {
			$this->current_field = $field;
			$this->actions = array();
			//set custom columns if option is set.
				if($field['custom_column'] === true) {
					if(is_array($this->_meta_box['pages'])) {
						foreach($this->_meta_box['pages'] as $page) {
							$this->custom_cols[$page][] = array( 
									'key'=> $field['id'],'label'=>$field['custom_column_label'], 'cb'=> $field['custom_column_cb']);
							$this->actions[] = $field['id'];	
						}
					} else {
						$this->custom_cols[$this->_meta_box['pages']][] = array( 
							'key'=> $field['id'],'label'=>$field['custom_column_label'], 'cb'=> $field['custom_column_cb']);
						$this->actions[] = $field['id'];
					}
				}
			}
		}
	
	function columns() {
		
		foreach($this->custom_cols as $key => $values) {
			add_filter('manage_edit-'.$key.'_columns',array($this,'custom_column'));
			add_action('manage_'.$key.'_posts_custom_column',array($this,'custom_column_display'));
		}	
	}
	
	function custom_column($columns) {
		global $post;
		foreach($this->custom_cols[$post->post_type] as $custom) {
			$columns[$custom['key']] = $custom['label'];
		}
		return $columns;
	}
	
	function custom_column_display($name) {
		global $post; 
			if(isset($this->custom_cols[$post->type][$name]['cb'])) {
				$this->custom_cols[$post->type][$name]['cb']();
			} else {
				$v = get_post_meta($post->ID, $name); 
				$v = (is_array($v)) ? implode(', ',$v) : $v ;
				echo $v;
			}
	}
}


?>