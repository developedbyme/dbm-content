<?php
	namespace DbmContent;

	class CustomRangeFilters {
		
		function __construct() {
			//echo("\DbmContent\CustomRangeFilters::__construct<br />");
		}
		
		public function encode_objects_as($ids, $types, $request_data = null) {
			$return_array = array();
			
			$types = explode(',', $types);
			
			foreach($ids as $id) {
				$encoded_object = array('id' => $id);
				
				foreach($types as $type) {
					$encoded_object = apply_filters('wprr/range_encoding/'.$type, $encoded_object, $id, $request_data);
				}
				
				$return_array[] = $encoded_object;
			}
			
			return $return_array;
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
		
		protected function combine_post_ids($post_ids_groups, $operator = 'all') {
			
			$post_ids = array();
			
			switch($operator) {
				case "all":
					$post_ids = array_pop($post_ids_groups);
					foreach($post_ids_groups as $post_ids_group) {
						$post_ids = array_intersect($post_ids, $post_ids_group);
					}
					break;
				case "any":
					foreach($post_ids_groups as $post_ids_group) {
						$post_ids = array_merge($post_ids, $post_ids_group);
					}
					$post_ids = array_unique($post_ids);
					break;
			}
			
			return $post_ids;
		}
		
		public function query_relations($query_args, $data) {
			//echo("\DbmContent\CustomRangeFilters::query_relations<br />");
			
			$has_query = false;
			
			$dbm_query = dbm_new_query($query_args);
			
			if(isset($data['type'])) {
				
				$type_ids = $this->get_type_ids($data);
				
				if(!empty($type_ids)) {
					
					$type_post_ids_groups = array();
					
					foreach($type_ids as $type_id) {
						$type_post_ids_groups[] = dbm_get_post_ids_for_term_id($type_id);
					}
					
					$operator = 'all';
					if(isset($data['typeMatch'])) {
						$operator = $data['typeMatch'];
					}
					
					$post_ids = $this->combine_post_ids($type_post_ids_groups, $operator);
					$dbm_query->include_only($post_ids);
					
					$has_query = true;
				}
				else {
					$dbm_query->include_only(array(0));
					return $dbm_query->get_query_args();
				}
				
			}
			if(isset($data['relation'])) {
				
				$relation_ids = $this->get_relation_ids($data);
				
				if(!empty($relation_ids)) {
					
					$post_ids_groups = array();
					
					foreach($relation_ids as $relation_id) {
						$post_ids_groups[] = dbm_get_post_ids_for_term_id($relation_id);
					}
					
					$operator = 'all';
					if(isset($data['relationMatch'])) {
						$operator = $data['relationMatch'];
					}
					
					$post_ids = $this->combine_post_ids($post_ids_groups, $operator);
					$dbm_query->include_only($post_ids);
					
					$has_query = true;
				}
				else {
					$dbm_query->include_only(array(0));
					return $dbm_query->get_query_args();
				}
			}
			
			if(!$has_query) {
				$dbm_query->include_only(array(0));
			}
			
			return $dbm_query->get_query_args();
		}
		
		public function query_by_owned_relation($query_args, $data) {
			$has_query = false;
			
			$dbm_query = dbm_new_query($query_args);
			
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
					
					$post_ids_groups = array();
					
					foreach($term_ids as $term_id) {
						$post_ids_groups[] = dbm_get_post_ids_for_term_id($term_id);
					}
					
					$post_ids = $this->combine_post_ids($post_ids_groups, 'all');
					$dbm_query->include_only($post_ids);
					
				}
				
			}
			
			if(!$has_query) {
				$dbm_query->include_only(array(0));
			}
			
			return $dbm_query->get_query_args();
		}
		
		public function query_byPostRelation($query_args, $data) {
			$has_query = false;
			
			$dbm_query = dbm_new_query($query_args);
			
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
					
					$post_ids_groups = array();
					
					foreach($term_ids as $term_id) {
						$post_ids_groups[] = dbm_get_post_ids_for_term_id($term_id);
					}
					
					$post_ids = $this->combine_post_ids($post_ids_groups, 'all');
					$dbm_query->include_only($post_ids);
				}
				
			}
			
			if(!$has_query) {
				$dbm_query->include_only(array(0));
			}
			
			return $dbm_query->get_query_args();
		}
		
		
		
		public function query_by_relation_owner($query_args, $data) {
			$has_query = false;
			
			$dbm_query = dbm_new_query($query_args);
			
			if(isset($data['relationGroup']) && isset($data['from'])) {
				
				$relation_ids = $this->get_relation_ids($data, 'relationGroup', 'slugPath');
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
				$dbm_query->include_by_term_ids($type_ids);
				
				if(!empty($term_ids)) {
					$has_query = true;
					
					$post_ids_groups = array();
					
					foreach($term_ids as $term_id) {
						$post_ids_groups[] = dbm_get_post_ids_for_term_id($term_id);
					}
					
					$post_ids = $this->combine_post_ids($post_ids_groups, 'all');
					$dbm_query->include_only($post_ids);
				}
			}
			
			if(!$has_query) {
				$dbm_query->include_only(array(0));
			}
			
			return $dbm_query->get_query_args();
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
		
		public function query_globalItem($query_args, $data) {
			//echo("\DbmContent\CustomRangeFilters::query_globalItem<br />");
			
			$identifier = $data['identifier'];
			
			$query_args['post__in'] = array(dbm_get_global_item($identifier));
			
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
			
			$dbm_post = dbm_get_post($post_id);
			
			if($dbm_post->get_post_type() === 'page') {
				if(!isset($add_ons["pageSettings"])) {
					$add_ons["pageSettings"] = array();
				}
				
				$type_term = wprr_get_data_api()->wordpress()->get_taxonomy('dbm_type')->get_term('settings/page-settings');
				
				if($type_term) {
					$page_setting_ids = $dbm_post->object_relation_query('in:for:settings/page-settings');
				
					foreach($page_setting_ids as $page_setting_id) {
						$add_ons["pageSettings"][] = $this->encode_pageSettings(array('id' => $page_setting_id), $page_setting_id, null);
					}
				}
				
				if(!isset($add_ons["dataSources"])) {
					$add_ons["dataSources"] = array();
				}
				
				$type_term = wprr_get_data_api()->wordpress()->get_taxonomy('dbm_type')->get_term('settings/data-source');
				if($type_term) {
					$data_source_ids = $dbm_post->object_relation_query('in:for:settings/data-source');
				
					foreach($data_source_ids as $data_source_id) {
						$add_ons["dataSources"][] = $this->encode_dataSource(array('id' => $data_source_id), $data_source_id, null);
					}
				}
			}
			
			
			return $add_ons;
		}
		
		public function encode_dataSource($encoded_data, $post_id, $data) {
			
			$group = dbmtc_get_group($post_id);
			
			try {
				$encoded_data['sourceType'] = \DbmContent\OddCore\Utils\TaxonomyFunctions::get_term_slugs_from_ids($group->get_subtypes('settings/data-source'), 'dbm_type')[0];
				$encoded_data['dataName'] = $group->get_field_value('dataName');
				$encoded_data['data'] = $group->get_field_value('data');
			}
			catch(\exception $the_exception) {
				
			}
			
			return $encoded_data;
		}
		
		public function encode_pageSettings($encoded_data, $post_id, $data) {
			
			$group = dbmtc_get_group($post_id);
			
			try {
				$encoded_data['data'] = $group->get_field_value('data');
			}
			catch(\exception $the_exception) {
				
			}
			
			$encoded_data['headerType'] = $group->get_single_object_relation_field_value("in:for:type/header-type", "identifier");
			$encoded_data['heroType'] = $group->get_single_object_relation_field_value("in:for:type/hero-type", "identifier");
			$encoded_data['footerType'] = $group->get_single_object_relation_field_value("in:for:type/footer-type", "identifier");
			
			$page_setting_ids = $group->object_relation_query('out:based-on:settings/page-settings');
			
			$encoded_data['basedOn'] = array();
			
			foreach($page_setting_ids as $page_setting_id) {
				$encoded_data['basedOn'][] = $this->encode_pageSettings(array('id' => $page_setting_id), $page_setting_id, null);
			}
			
			return $encoded_data;
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
			
			$encoded_data['from'] = wprr_encode_private_post_link(get_post_meta($post_id, 'fromId', true));
			$encoded_data['to'] = wprr_encode_private_post_link(get_post_meta($post_id, 'toId', true));
			
			return $encoded_data;
		}
		
		public function encode_relationType($encoded_data, $post_id, $data) {
			$dbm_post = dbm_get_post($post_id);
			
			$types = $dbm_post->get_subtypes('object-relation');
			
			if(!empty($types)) {
				$encoded_data['type'] = wprr_encode_term_by_id($types[0], 'dbm_type');
			}
			
			
			return $encoded_data;
		}
		
		public function encode_editObjectRelations($encoded_data, $post_id, $data) {
			global $DbmContentTransactionalCommunicationPlugin;
			
			$dbm_post = dbm_get_post($post_id);
			
			$outgoing = $dbm_post->get_encoded_outgoing_relations();
			
			$encoded_orders = array();
			$order_ids = $dbm_post->get_outgoing_relations('relation-order-by', 'relation-order');
			foreach($order_ids as $order_id) {
				
				$order_data_id = get_post_meta($order_id, 'toId', true);
				
				$encoded_order = array(
					'id' => $order_data_id,
					'order' => get_post_meta($order_data_id, 'order', true),
					'forType' => get_post_meta($order_data_id, 'forType', true)
				);
				
				$encoded_orders[] = $encoded_order;
			}
			
			$user_relations = $dbm_post->get_encoded_user_relations();
			
			$encoded_data['relations'] = array(
				'incoming' => $dbm_post->get_encoded_incoming_relations(),
				'outgoing' => $outgoing,
				'orders' => $encoded_orders,
				'userRelations' => $user_relations
			);
			
			
			/*
			$incoming_relation_groups = $dbm_post->get_all_incoming_relations_at_any_time(true);
			$incoming_groups = array();
			foreach($incoming_relation_groups as $name => $ids) {
				$encoded_group = array();
				foreach($ids as $id) {
					$encoded_object = $this->encode_relationLink(array('id' => $id), $id, $data);
					
					$encoded_object = $DbmContentTransactionalCommunicationPlugin->ranges->filter_encode_fields($encoded_object, $id, $data);
					
					$encoded_object['status'] = get_post_status($id);
					$encoded_object['from'] = $this->encode_dbmTypes($encoded_object['from'], $encoded_object['from']['id'], $data);
					
					$encoded_group[] = $encoded_object;
				}
				$incoming_groups[$name] = $encoded_group;
			}
			
			$outgoing_relation_groups = $dbm_post->get_all_outgoing_relations_at_any_time(true);
			$outgoing_groups = array();
			foreach($outgoing_relation_groups as $name => $ids) {
				$encoded_group = array();
				foreach($ids as $id) {
					$encoded_object = $this->encode_relationLink(array('id' => $id), $id, $data);
					
					$encoded_object = $DbmContentTransactionalCommunicationPlugin->ranges->filter_encode_fields($encoded_object, $id, $data);
					
					$encoded_object['status'] = get_post_status($id);
					$encoded_object['to'] = $this->encode_dbmTypes($encoded_object['to'], $encoded_object['to']['id'], $data);
					
					$encoded_group[] = $encoded_object;
				}
				$outgoing_groups[$name] = $encoded_group;
			}
			
			$encoded_data['relations'] = array(
				'incoming' => $incoming_groups,
				'outgoing' => $outgoing_groups,
			);
			*/
			
			return $encoded_data;
		}
		
		public function encode_processForItem($encoded_data, $post_id, $data) {
			$encoded_parts = array();
			
			$process_for_item = dbm_get_process_for_item($post_id);
			$parts = $process_for_item->get_parts();
			$statuses = $process_for_item->get_statuses();
			
			foreach($parts as $part) {
				$part_id = $part->get_id();
				$current_encoded_object = array('id' => $part_id);
				
				$current_encoded_object = apply_filters('wprr/range_encoding/fieldValues', $current_encoded_object, $part_id, $data);
				$current_statuses = array();
				foreach($statuses as $status_name => $status_group) {
					if(in_array($part_id, $status_group)) {
						$current_statuses[] = $status_name;
					}
				}
				$current_encoded_object['statuses'] = $current_statuses;
				
				$encoded_parts[] = $current_encoded_object; 
			}
			
			$encoded_data['processParts'] = $encoded_parts;
			
			return $encoded_data;
		}
		
		public function encode_dbmTypes($encoded_data, $post_id, $data) {
			$dbm_post = dbm_get_post($post_id);
			$type_ids = $dbm_post->get_types();
			$encoded_data['types'] = \DbmContent\OddCore\Utils\TaxonomyFunctions::get_full_term_slugs_from_ids($type_ids, 'dbm_type');
			
			return $encoded_data;
		}
		
		public function encode_currentSequenceNumber($encoded_data, $post_id, $data) {
			
			$encoded_data["currentSequenceNumber"] = (int)get_post_meta($post_id, 'currentSequenceNumber', true);
			
			return $encoded_data;
		}
		
		public function encode_image($encoded_data, $post_id, $data) {
			
			$post = dbmtc_get_group($post_id);
			
			$encoded_data["url"] = $post->get_field_value('value')['url'];
			$encoded_data["title"] = $post->get_field_value('title');
			$encoded_data["description"] = $post->get_field_value('description');
			
			return $encoded_data;
		}
		
		public static function test_import() {
			echo("Imported \DbmContent\CustomRangeFilters<br />");
		}
	}
?>
