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
					'terms' => $types,
					'include_children' => false
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
					'terms' => $relations,
					'include_children' => false
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
		
		public function query_relation_manager_items($query_args, $data) {
			//echo("\DbmContent\CustomRangeFilters::query_relation_manager_items<br />");
			
			$taxonomy_name = $data['taxonomy'];
			
			$taxonomy = get_taxonomy($taxonomy_name);
			
			$query_args['post_type'] = $taxonomy->object_type;
			
			return $query_args;
		}
		
		public function encode_relation_manager_items($return_object, $post_id, $data) {
			//echo("\DbmContent\CustomRangeFilters::encode_relation_manager_items<br />");
			
			$taxonomy_name = $data['taxonomy'];
			
			$term_ids = wp_get_post_terms($post_id, $taxonomy_name, array('fields' => 'ids'));
			
			$return_object['terms'] = $term_ids;
			$return_object['postType'] = get_post_type($post_id);
			$return_object['parent'] = wp_get_post_parent_id($post_id);
				
			return $return_object;
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
			
			if(function_exists('get_field')) {
				$page = get_field('dbm_taxonomy_page', $term);
				if($page && $page instanceof \WP_Post) {
					$return_data["permalink"] = get_permalink($page->ID);
				}
			}
			
			return $return_data;
		}
		
		public function custom_item_get_global_relation($return_object, $id, $data) {
			
			$query_args = array(
				'posts_per_page' => 1
			);
			
			if($id !== 'any') {
				$postTypes = explode(',', $id);
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
				$typeField = isset($data['typeField']) ? $data['typeField'] : 'slug_path';
				
				if($typeField === 'slug_path') {
					$resloved_types = array();
					foreach($types as $type) {
						$current_term = dbm_get_type(explode("/", $type));
						if($current_term) {
							$resloved_types[] = $current_term->term_id;
						}
					}
					$types = $resloved_types;
					$typeField = 'id';
				}
				
				$current_tax_query = array(
					'taxonomy' => 'dbm_type',
					'field' => $typeField,
					'terms' => $types,
					'include_children' => false
				);
				array_push($tax_query, $current_tax_query);
				$has_query = true;
			}
			if(isset($data['relation'])) {
				$relations = explode(',', $data['relation']);
				$relationField = isset($data['relationField']) ? $data['relationField'] : 'slugPath';
				
				if($relationField === 'slugPath') {
					$resloved_relations = array();
					foreach($relations as $relation) {
						$current_term = dbm_get_relation(explode("/", $relation));
						if($current_term) {
							$resloved_relations[] = $current_term->term_id;
						}
					}
					$relations = $resloved_relations;
					$relationField = 'id';
				}
				
				
				$current_tax_query = array(
					'taxonomy' => 'dbm_relation',
					'field' => $relationField,
					'terms' => $relations,
					'include_children' => false
				);
				$has_query = true;
			}
			
			if(!$has_query) {
				return null;
			}
			
			$query_args['tax_query'] = $tax_query;
			
			$posts = get_posts($query_args);
			
			if(count($posts) > 0) {
				return $posts[0];
			}
			
			return null;
		}
		
		public function custom_item_encode_global_relation($return_object, $item, $data) {
			$return_object['data'] = mrouter_encode_post($item);
			
			return $return_object;
		}
		
		public static function test_import() {
			echo("Imported \DbmContent\CustomRangeFilters<br />");
		}
	}
?>
