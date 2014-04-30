<?php
/*
Plugin Name: ITS Site List Menu - Default
Description:  Sets the ITS site list to default (ONLY ACTIVATE TO RESET LINKS TO ITS SITES IN FOOTER)
Author: Devin Rousso
Version: 1.0
Date: 20140207
*/

class ITS_Site_List_Menu_Defaults {
	//
	// Adds Default ITS Site List to database on activation
	//
	public static function activate() {
		if(wp_get_nav_menu_object('ITS_Links') === false){
			global $wpdb;

			$sites = array(
				'Kim Kardashian'		=> 'http://kimkardashian.celebuzz.com/',
				'Khloe Kardashian'		=> 'http://khloekardashian.celebuzz.com/',
				'Kourtney Kardashian'	=> 'http://www.kourtneykardashian.com/',
				'Kendall and Kylie'		=> 'http://kendallandkylie.celebuzz.com/',
				'Kris Jenner'			=> 'http://krisjenner.celebuzz.com/',
				'Snooki Nicole'			=> 'http://snookinicole.celebuzz.com/',
				'Jenni Farley'			=> 'http://www.jennifarley.com/',
				'Lindsay Lohan'			=> 'http://www.lindsaylohan.com/',
				'Louise Roe'			=> 'http://www.louiseroe.com/',
				'NeNe Leakes'			=> 'http://www.neneleakesofficial.com/',
				'Johnny Wujek'			=> 'http://www.johnnywujek.com/',
				'Brad Goreski'			=> 'http://www.mrbradgoreski.com/'

			);
			$count = 0;

			wp_insert_term('ITS_Links', 'nav_menu', array('slug'=>'its-links', 'count'=>$sites.count()));

			foreach($sites as $name=>$url){
				$count ++;
				$modified_name = 'menu' . strtolower(str_replace(' ', '-', $name));

				$post = array(
					'post_name'			=> $modified_name,
					'post_title'		=> $name,
					'post_status'		=> 'publish',
					'post_type'			=> 'nav_menu_item',
					'menu_order'		=> $count
				);
				wp_insert_post($post);

				$post_id_query = $wpdb->prepare('SELECT ID FROM wp_posts WHERE post_name = "' . $modified_name . '"');
				$post_id = $wpdb->get_var($post_id_query);

				add_post_meta($post_id, '_menu_item_url', $url);
				add_post_meta($post_id, '_menu_item_classes', 'a:1:{i:0;s:0:"";}');
				add_post_meta($post_id, '_menu_item_object', 'custom');
				add_post_meta($post_id, '_menu_item_object_id', $post_id);
				add_post_meta($post_id, '_menu_item_menu_item_parent', '0');
				add_post_meta($post_id, '_menu_item_type', 'custom');

				$term_id_query = $wpdb->prepare('SELECT term_id FROM wp_terms WHERE name = "ITS_Links"');
				$term_id = $wpdb->get_var($term_id_query);

				$term_taxonomy_id_query = $wpdb->prepare('SELECT term_taxonomy_id FROM wp_term_taxonomy WHERE term_id = "' . $term_id . '"');
				$term_taxonomy_id = $wpdb->get_var($term_taxonomy_id_query);

				$wpdb->insert('wp_term_relationships', array(
					'object_id'			=> $post_id,
					'term_taxonomy_id'	=> $term_taxonomy_id
				));
			}
		}
	}

	//
	//
	//
	public static function deactivate() {
	}

}

// function to be run when the plugin is activated / deactivated
register_activation_hook(__FILE__, array('ITS_Site_List_Menu_Defaults', 'activate'));
register_deactivation_hook(__FILE__, array('ITS_Site_List_Menu_Defaults', 'deactivate'));