<?php
/*
Plugin Name: Hueman Multicolumn Category Widget
Plugin URI: https://hamdicatal.com/wordpress/hueman-multicolumn-widget
Description: A widget to display categories in two columns on Hueman theme.
Version: 1.0
Date: 01 June 2019
Author: Hamdi Çatal <iletisim@hamdicatal.com>
Author URI: https://hamdicatal.com/
Text Domain: hueman-multicolumn-category-widget
*/

/*
Copyright (C) 2019 Hamdi Çatal
	
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/**
 * Hueman Multicolumn category widget
 * 
 * @package multicolumn-category-widget
 */
class MulticolumnCategoryWidget extends WP_Widget {
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct(
			false,
			$name = __('Hueman Multicolumn Category Widget', 'hueman-multicolumn-category-widget')
		);
	}

	/**
	 * Output of the widget in the frontend
	 * 
	 * @param array $args
	 * @param array instance
	 */
	public function widget($args, $instance) {
		extract($args);
		
		$columns = 2; // Default column count. Please, do not change!

		echo '<div id="categories-2" class="widget widget_categories">';
		
		// Output widget header
		$title = apply_filters('widget_title', $instance['title']);
		if ($title) {
			echo $before_title . $title . $after_title;
		}
		else {
			echo $before_title . __('Categories', 'multicolumn-category-widget') . $after_title;
		}
		
		// Output top level category list in multiple columns
		$args = array(
			'orderby' => 'name',
			'parent' => 0
		);	
		$categories = get_categories($args);
		$categories_total = count($categories);
		$categories_per_column = ceil($categories_total / $columns);
		$number_of_columns = ceil($categories_total / $categories_per_column);
		$column_number = 1;
		$result_number = 0;
		
		echo '<ul class="left">';
		foreach($categories as $category) {
			if($result_number == $categories_per_column) {
				$result_number = 0;
				$column_number++;
				echo '</ul><ul class="right">';
			}
			$result_number++;
			$postcount = '';
			if($instance['showcount'] == 1) {
				$posts = get_term_by('name',$category->cat_name,'category');
				$postcount = ' <span class="postcount">('.$posts->count.')</span>';
			}
			printf('<li class="cat-item cat-item-%d"><a href="%s" title="%s">%s</a>%s</li>',
				$category->term_id,
				get_category_link($category->term_id),
				esc_attr($category->category_description),
				esc_html($category->cat_name),
				$postcount);
		}
		echo '</ul>';
		
		echo $after_widget;
	}

	/**
	 * Update widget in the backend
	 * 
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	public function update($new_instance, $old_instance) {
		$instance          = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['columns'] = strip_tags($new_instance['columns']);
		$instance['showcount'] = strip_tags($new_instance['showcount']);
		return $instance;
	}

	/**
	 * Display an input-form in the backend
	 * 
	 * @param array $instance
	 */
	public function form($instance) {
		$title = '';
		if(isset($instance['title'])) $title = $instance['title'];
		
		$columns = '2';
		if(isset($instance['columns']) && ctype_digit($instance['columns'])) $columns = $instance['columns'];
		if($columns < 1) $columns = 1;
		
		$showcount = 0;
		if(isset($instance['showcount']) && $instance['showcount']==1) $showcount = 1;
		
		printf(
			'<p><label for="%1$s">%2$s:</label> <input class="widefat" id="%1$s" name="%3$s" type="text" value="%4$s" placeholder="%13$s" /></p>'.
			'<p><input id="%9$s" name="%11$s" type="checkbox" value="1" %12$s /> <label for="%9$s">%10$s</label></p>',
			$this->get_field_id('title'),
			__('Title', 'multicolumn-category-widget'),
			$this->get_field_name('title'),
			esc_attr($title),
			$this->get_field_id('columns'),
			__('Number of columns', 'multicolumn-category-widget'),
			$this->get_field_name('columns'),
			esc_attr($columns),
			$this->get_field_id('showcount'),
			__('Show post counts', 'multicolumn-category-widget'),
			$this->get_field_name('showcount'),
			$showcount==1?'checked="checked"':'',
			__('Categories'));
	}
}

/**
 * Setup everything
 */
function mccw_init() {
	load_plugin_textdomain('multicolumn-category-widget', false, 'multicolumn-category-widget/languages/');
}

function mccw_widgets_init() {
	register_widget('MulticolumnCategoryWidget');
}

add_action('widgets_init', 'mccw_widgets_init');
add_action('plugins_loaded', 'mccw_init');
?>