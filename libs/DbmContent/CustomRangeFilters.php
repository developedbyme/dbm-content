<?php
	namespace DbmContent;

	class CustomRangeFilters {
		
		function __construct() {
			//echo("\DbmContent\CustomRangeFilters::__construct<br />");
		}
		
		protected function add_tax_query(&$query_args, $tax_query, $relation = 'AND') {
			if(isset($query_args['tax_query'])) {
				$combined_query = array(
					'relation' => $relation,
					$tax_query,
					$query_args['tax_query']
				);
				
				$query_args['tax_query'] = $combined_query;
			}
			else {
				$query_args['tax_query'] = array($tax_query);
			}
			
			return $query_args;
		}
		
		protected function get_type_ids($data) {
			$return_array = array();
			
			$types = explode(',', $data['type']);
			$typeField = isset($data['typeField']) ? $data['typeField'] : 'slugPath';
			foreach($types as $type) {
				if($typeField === 'slugPath') {
					$current_term = dbm_get_type_by_path($type);
				}
				else {
					$current_term = get_term_by($typeField, $type, 'dbm_type');
				}
				
				if($current_term) {
					$return_array[] = $current_term->term_id;
				}
			}
			
			return $return_array;
		}
		
		protected function get_relation_ids($data, $relation_key = 'relation', $relation_field_key = 'relationField') {
			$return_array = array();
			
			$types = explode(',', $data[$relation_key]);
			$typeField = isset($data[$relation_field_key]) ? $data[$relation_field_key] : 'slugPath';
			foreach($types as $type) {
				if($typeField === 'slugPath') {
					$current_term = dbm_get_relation_by_path($type);
				}
				else {
					$current_term = get_term_by($typeField, $type, 'dbm_relation');
				}
				
				if($current_term) {
					$return_array[] = $current_term->term_id;
				}
			}
			
			return $return_array;
		}
		
		public function query_relations_legacy($query_args, $data) {
			//echo("\DbmContent\CustomRangeFilters::query_relations_legacy<br />");
			
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
				
				$type_ids = $this->get_type_ids($data);
				
				if(!empty($type_ids)) {
					$current_tax_query = array(
						'taxonomy' => 'dbm_type',
						'field' => 'id',
						'terms' => $type_ids,
						'include_children' => false
					);
					array_push($tax_query, $current_tax_query);
					$has_query = true;
				}
				
			}
			if(isset($data['relation'])) {
				
				$relation_ids = $this->get_relation_ids($data);
				
				if(!empty($relation_ids)) {
					$current_tax_query = array(
						'taxonomy' => 'dbm_relation',
						'field' => 'id',
						'terms' => $relation_ids,
						'include_children' => false
					);
					array_push($tax_query, $current_tax_query);
					$has_query = true;
				}
			}
			
			if(!$has_query) {
				$query_args['post__in'] = array(0);
			}
			
			$this->add_tax_query($query_args, $tax_query);
			
			return $query_args;
		}
		
		public function query_relations($query_args, $data) {
			//echo("\DbmContent\CustomRangeFilters::query_relations<br />");
			
			$has_query = false;
			
			$dbm_query = dbm_new_query($query_args);
			
			if(isset($data['type'])) {
				
				$type_ids = $this->get_type_ids($data);
				
				if(!empty($type_ids)) {
					$dbm_query->add_type_ids($type_ids);
					$has_query = true;
				}
				else {
					$query_args['post__in'] = array(0);
					return $query_args;
				}
				
			}
			if(isset($data['relation'])) {
				
				$relation_ids = $this->get_relation_ids($data);
				
				if(!empty($relation_ids)) {
					$operator = 'AND';
					if(isset($data['relationMatch'])) {
						switch($data['relationMatch']) {
							case "all":
								//MENOTE: do nothing
								break;
							case "any":
								$operator = 'IN';
								break;
						}
						
					}
					
					$dbm_query->add_relation_ids($relation_ids, $operator);
					$has_query = true;
				}
				else {
					$query_args['post__in'] = array(0);
					return $query_args;
				}
			}
			
			$query_args = $dbm_query->get_query_args();
			
			if(!$has_query) {
				$query_args['post__in'] = array(0);
			}
			
			return $query_args;
		}
		
		public function query_by_owned_relation($query_args, $data) {
			$has_query = false;
			if(isset($data['ownedRelation'])) {
				
				$term_ids = array();
				
				$owned_relations = explode(',', $data['ownedRelation']);
				foreach($owned_relations as $owned_relation) {
					$temp_array = explode(':', $data['ownedRelation']);
					$group = $temp_array[0];
					$owner_id = $temp_array[1];
					
					$meta_name = 'dbm_relation_term_'.$group;
					$term_id = (int)get_post_meta($owner_id, $meta_name, true);
					if($term_id) {
						$term_ids[] = $term_id;
					}
				}
				
				if(!empty($term_ids)) {
					$has_query = true;
					
					$current_tax_query = array(
						'taxonomy' => 'dbm_relation',
						'field' => 'id',
						'terms' => $term_ids,
						'include_children' => false
					);
					
					$this->add_tax_query($query_args, $current_tax_query);
				}
				
			}
			
			if(!$has_query) {
				$query_args['post__in'] = array(0);
			}
			
			return $query_args;
		}
		
		public function query_byPostRelation($query_args, $data) {
			$has_query = false;
			if(isset($data['postRelation'])) {
				
				$term_ids = array();
				
				$owned_relations = explode(',', $data['postRelation']);
				foreach($owned_relations as $owned_relation) {
					$temp_array = explode(':', $data['postRelation']);
					$group = $temp_array[0];
					$owner_id = (int)$temp_array[1];
					
					$term_ids = dbm_get_post_relation($owner_id, $group);
				}
				
				if(!empty($term_ids)) {
					$has_query = true;
					
					$current_tax_query = array(
						'taxonomy' => 'dbm_relation',
						'field' => 'id',
						'terms' => $term_ids,
						'include_children' => false
					);
					
					$this->add_tax_query($query_args, $current_tax_query);
				}
				
			}
			
			if(!$has_query) {
				$query_args['post__in'] = array(0);
			}
			
			return $query_args;
		}
		
		
		
		public function query_by_relation_owner($query_args, $data) {
			$has_query = false;
			if(isset($data['relationGroup']) && isset($data['from'])) {
				
				$relation_ids = $this->get_relation_ids($data, 'relationGroup', 'relationGroupField');
				$group_term_id = $relation_ids[0];
				
				$post_id = (int)$data['from'];
				
				$term_ids = array();
				
				$parent_term = get_term_by('id', $group_term_id, 'dbm_relation');
				$current_terms = wp_get_post_terms($post_id, 'dbm_relation');
				foreach($current_terms as $current_term) {
					if(term_is_ancestor_of($parent_term, $current_term, 'dbm_relation')) {
						$term_ids[] = $current_term->term_id;
					}
				}
				
				$type_ids = $this->get_type_ids($data);
				
				$current_tax_query = array(
					'taxonomy' => 'dbm_type',
					'field' => 'id',
					'terms' => $type_ids,
					'include_children' => false
				);
				$this->add_tax_query($query_args, $current_tax_query);
				
				$current_tax_query = array(
					'taxonomy' => 'dbm_relation',
					'field' => 'id',
					'terms' => $term_ids,
					'include_children' => false
				);
				$this->add_tax_query($query_args, $current_tax_query);
				
				$has_query = true;
			}
			
			if(!$has_query) {
				$query_args['post__in'] = array(0);
			}
			
			return $query_args;
		}
		
		public function query_objectRelation($query_args, $data) {
			//echo("\DbmContent\CustomRangeFilters::query_objectRelation<br />");
			
			$ids = explode(',', $data['ids']);
			$path = $data['objectRelation'];
			
			$post_ids = \DbmContent\DbmPost::object_relation_query_from_ids($ids, $path);
			if(count($post_ids)) {
				$query_args['post__in'] = $post_ids;
			}
			else {
				$query_args['post__in'] = array(0);
			}
			
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
		
		public function query_languageTerm($query_args, $data) {
			//echo("\DbmContent\CustomRangeFilters::query_languageTerm<br />");
			
			$dbm_query = dbm_new_query($query_args);
			if(isset($data['language'])) {
				$dbm_query->add_relation_by_path('languages/'.$data['language']);
			}
			
			$query_args = $dbm_query->get_query_args();
			
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
						}
						$add_ons["dbmContent"]["relations"][$path][] = $term->term_id;
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
				array_push($tax_query, $current_tax_query);
				$has_query = true;
			}
			
			if(!$has_query) {
				return null;
			}
			
			$this->add_tax_query($query_args, $tax_query);
			
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
		
		public function encode_edit_fields($encoded_data, $post_id, $data) {
			$type_ids = wp_get_post_terms($post_id, 'dbm_type', array('fields' => 'ids'));
			
			foreach($type_ids as $type_id) {
				$current_type = \DbmContent\OddCore\Utils\TaxonomyFunctions::get_full_term_slug(get_term_by('id', $type_id, 'dbm_type'), 'dbm_type');
				$encoded_data = apply_filters('wprr/edit_fields/dbm/type/'.$current_type, $encoded_data, $post_id, $data);
			}
			
			return $encoded_data;
		}
		
		public function encode_incomingRelations($encoded_data, $post_id, $data) {
			$dbm_post = dbm_get_post($post_id);
			
			$dbm_post = dbm_get_post($post_id);
			
			$encoded_groups = array();
			
			$relation_groups = $dbm_post->get_all_incoming_relations();
			foreach($relation_groups as $name => $ids) {
				$encoded_group = array();
				foreach($ids as $id) {
					$encoded_group[] = $this->encode_relationLink(array('id' => $id), $id, $data);
				}
				$encoded_groups[$name] = $encoded_group;
			}
			
			$encoded_data['incomingRelations'] = $encoded_groups;

			
			return $encoded_data;
		}
		
		public function encode_outgoingRelations($encoded_data, $post_id, $data) {
			$dbm_post = dbm_get_post($post_id);
			
			$encoded_groups = array();
			
			$relation_groups = $dbm_post->get_all_outgoing_relations();
			foreach($relation_groups as $name => $ids) {
				$encoded_group = array();
				foreach($ids as $id) {
					$encoded_group[] = $this->encode_relationLink(array('id' => $id), $id, $data);
				}
				$encoded_groups[$name] = $encoded_group;
			}
			
			$encoded_data['outgoingRelations'] = $encoded_groups;
			
			return $encoded_data;
		}
		
		public function encode_relationLink($encoded_data, $post_id, $data) {
			$dbm_post = dbm_get_post($post_id);
			
			$encoded_data['from'] = wprr_encode_post_link(get_post_meta($post_id, 'fromId', true));
			$encoded_data['to'] = wprr_encode_post_link(get_post_meta($post_id, 'toId', true));
			
			return $encoded_data;
		}
		
		public static function test_import() {
			echo("Imported \DbmContent\CustomRangeFilters<br />");
		}
	}
?>
