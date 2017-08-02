<?php
	namespace DbmContent;

	class CustomRangeFilters {
		
		function __construct() {
			//echo("\DbmContent\CustomRangeFilters::__construct<br />");
		}
		
		public function query_relations($query_args, $data) {
			//echo("\DbmContent\CustomRangeFilters::query_relations<br />");
			
			if(isset($data['postType'])) {
				$postTypes = explode(',', $data['postType']);
				$query_args['post_type'] = $postTypes;
			}
			else {
				$query_args['post_type'] = get_post_types(array(), 'names');
			}
			
			$tax_query = array(
				'relation' => 'AND',
			);
			
			$has_query = false;
			
			if(isset($data['type'])) {
				$types = explode(',', $data['type']);
				$typeField = isset($data['typeField']) ? $data['typeField'] : 'id';
				$current_tax_query = array(
					'taxonomy' => 'dbm_type',
					'field' => $typeField,
					'terms' => $types
				);
				array_push($tax_query, $current_tax_query);
				$has_query = true;
			}
			if(isset($data['relation'])) {
				$relations = explode(',', $data['relation']);
				$relationField = isset($data['relationField']) ? $data['relationField'] : 'id';
				$current_tax_query = array(
					'taxonomy' => 'dbm_relation',
					'field' => $relationField,
					'terms' => $relations
				);
				array_push($tax_query, $current_tax_query);
				$has_query = true;
			}
			
			if(!$has_query) {
				$query_args['post__in'] = array(0);
			}
			
			$query_args['tax_query'] = $tax_query;
			
			return $query_args;
		}
		
		protected function _get_term_path($term_id, $taxonomy) {
			//echo("\DbmContent\CustomRangeFilters::_get_term_path<br />");
			$current_term = get_term_by('id', $term_id, $taxonomy);
			if($current_term->parent === 0) {
				return $current_term->slug;
			}
			return $this->_get_term_path($current_term->parent, $taxonomy).'/'.($current_term->slug);
		}
		
		public function encode_post_add_ons($add_ons, $post_id) {
			//echo("\DbmContent\CustomRangeFilters::encode_post_add_ons<br />");
			
			if(!isset($add_ons["dbmContent"])) {
				$add_ons["dbmContent"] = array();
			}
			if(!isset($add_ons["dbmContent"]["relations"])) {
				$add_ons["dbmContent"]["relations"] = array();
			}
			
			$terms = get_the_terms($post_id, 'dbm_relation');
			if($terms) {
				foreach($terms as $term) {
					if($term->parent !== 0) {
						$path = $this->_get_term_path($term->parent, 'dbm_relation');
						if(!isset($add_ons["dbmContent"]["relations"][$path])) {
							$add_ons["dbmContent"]["relations"][$path] = array();
							$add_ons["dbmContent"]["relations"][$path][] = $term->term_id;
						}
					}
				}
			}
			
			return $add_ons;
		}
		
		public function encode_term($return_data, $term_id, $term) {
			//echo("\DbmContent\CustomRangeFilters::encode_term<br />");
			
			$page = get_field('dbm_taxonomy_page', $term);
			if($page) {
				$return_data["permalink"] = get_permalink($page->ID);
			}
			
			return $return_data;
		}
		
		public static function test_import() {
			echo("Imported \DbmContent\CustomRangeFilters<br />");
		}
	}
?>
