<?php
	namespace DbmContent\OddCore\Utils;
	
	// \DbmContent\OddCore\Utils\TaxonomyFunctions
	class TaxonomyFunctions {
		
		public static function get_term_by_slugs($slugs, $taxonomy) {
			
			$current_id = 0;
			
			foreach($slugs as $slug) {
				$args = array(
					'taxonomy' => $taxonomy,
					'fields' => 'ids',
					'slug' => $slug,
					'parent' => $current_id,
					'hide_empty' => false
				);
					
				$terms = get_terms($args);
				
				if(empty($terms)) {
					return null;
				}
				$current_id = $terms[0];
			}
			
			return get_term_by('id', $current_id, $taxonomy);
		}
		
		public static function get_all_children_of_term($parent_id, $taxonomy) {
			$args = array(
				'taxonomy' => $taxonomy,
				'parent' => $parent_id,
				'hide_empty' => false
			);
				
			return get_terms($args);
		}
		
		public static function test_import() {
			echo("Imported \OddCore\Utils\TaxonomyFunctions<br />");
		}
	}
?>