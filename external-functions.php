<?php
	function dbm_content_get_status_box() {
		$current_box = new \DbmContent\OddCore\Admin\MetaData\ReactPostMetaDataHiddenBox();
		$current_box->set_name('Dbm content controller');
		$current_box->set_nonce_name('dbm_content_controller');
		$current_box->set_component('dbmContentController');
		
		$current_box->create_simple_meta_fields(array('dbm_content'));
		
		return $current_box;
	}
	
	function dbm_content_add_owned_relationship($type, $relation_term, $type_group = null) {
		$owned_term = new \DbmContent\Admin\Hooks\OwnedRelationTerm();
		$owned_term->setup($type, $relation_term);
		if(isset($type_group)) {
			$owned_term->set_type_group($type_group);
		}
		$owned_term->register_hooks();
		
		global $DbmContentPlugin;
		$DbmContentPlugin->add_owned_relation_term($owned_term);
	}
	
	function dbm_content_add_owned_relationship_with_auto_add($type, $relation_term, $type_group = null) {
		$owned_term = new \DbmContent\Admin\Hooks\OwnedRelationTerm();
		$owned_term->setup($type, $relation_term);
		$owned_term->set_add_term_to_owner_post(true);
		if(isset($type_group)) {
			$owned_term->set_type_group($type_group);
		}
		$owned_term->register_hooks();
		
		global $DbmContentPlugin;
		$DbmContentPlugin->add_owned_relation_term($owned_term);
	}
	
	function dbm_get_relation($slugs) {
		return \DbmContent\OddCore\Utils\TaxonomyFunctions::get_term_by_slugs($slugs, 'dbm_relation');
	}
	
	function dbm_get_relation_by_path($path) {
		return dbm_get_relation(explode('/', $path));
	}
	
	function dbm_get_relations_by_paths($paths) {
		$return_array = array();
		
		foreach($paths as $path) {
			$current_relation = dbm_get_relation_by_path($path);
			
			if($current_relation) {
				$return_array[] = $current_relation;
			}
		}
		
		return $return_array;
	}
	
	function dbm_get_child_relations_by_path($path) {
		
		$parent = dbm_get_relation(explode('/', $path));
		
		return get_term_children($parent->term_id, 'dbm_relation');
	}
	
	function dbm_get_tax_query_for_relation_ids($ids) {
		return array(
			'taxonomy' => 'dbm_relation',
			'field' => 'id',
			'terms' => $ids,
			'include_children' => false,
			'operator' => 'AND'
		);
	}
	
	function dbm_get_ids_from_terms($terms) {
		$ids = array_map(function($term) {return $term->term_id;}, $terms);
		
		return $ids;
	}
	
	function dbm_get_tax_query_for_relation_paths($paths) {
		
		$paths = dbm_get_relations_by_paths($paths);
		
		if(isset($paths) && count($paths) > 0) {
			$ids = array_map(function($term) {return $term->term_id;}, $paths);
			
			return dbm_get_tax_query_for_relation_ids($ids);
		}
		
		return array(
			'taxonomy' => 'dbm_relation',
			'field' => 'id',
			'terms' => array(-1),
			'include_children' => false,
			'operator' => 'AND'
		);
	}
	
	function dbm_get_type($slugs) {
		return \DbmContent\OddCore\Utils\TaxonomyFunctions::get_term_by_slugs($slugs, 'dbm_type');
	}
	
	function dbm_get_type_by_path($path) {
		return dbm_get_type(explode('/', $path));
	}
	
	function dbm_get_types_by_paths($paths) {
		$return_array = array();
		
		foreach($paths as $path) {
			$current_relation = dbm_get_type_by_path($path);
			
			if($current_relation) {
				$return_array[] = $current_relation;
			}
		}
		
		return $return_array;
	}
	
	function dbm_get_tax_query_for_type_ids($ids) {
		return array(
			'taxonomy' => 'dbm_type',
			'field' => 'id',
			'terms' => $ids,
			'include_children' => false,
			'operator' => 'AND'
		);
	}
	
	function dbm_get_tax_query_for_type_paths($paths) {
		
		$paths = dbm_get_types_by_paths($paths);
		
		if(isset($paths) && count($paths) > 0) {
			$ids = array_map(function($term) {return $term->term_id;}, $paths);
			
			return dbm_get_tax_query_for_type_ids($ids);
		}
		
		return array(
			'taxonomy' => 'dbm_type',
			'field' => 'id',
			'terms' => array(-1),
			'include_children' => false,
			'operator' => 'AND'
		);
	}
	
	function dbm_create_data($name, $type_path, $grouping_path = null) {
		
		wprr_performance_tracker()->start_meassure('dbm_create_data');
		
		$parent_id = 0;
		
		if($grouping_path) {
			wprr_performance_tracker()->start_meassure('dbm_create_data get_parent (dataapi)');
			
			$parent_grouping_term = wprr_get_data_api()->wordpress()->get_taxonomy('dbm_type')->get_term($grouping_path);
			$parent_id = wprr_get_data_api()->database()->new_select_query()->set_post_type('dbm_data')->include_private()->include_term($parent_grouping_term)->get_id();
			wprr_performance_tracker()->stop_meassure('dbm_create_data get_parent (dataapi)');
			
			/*
			wprr_performance_tracker()->start_meassure('dbm_create_data get_parent');
			
			$parent_grouping_term = dbm_get_type(explode('/', $grouping_path));
			$parent_id = \DbmContent\OddCore\Utils\TaxonomyFunctions::get_single_post_id_by_term($parent_grouping_term);
			wprr_performance_tracker()->stop_meassure('dbm_create_data get_parent');
			*/
		}
		
		/*
		$args = array(
			'post_type' => 'dbm_data',
			'post_title' => $name,
			'post_parent' => $parent_id,
			'post_status' => 'draft'
		);
		*/
		
		wprr_performance_tracker()->start_meassure('dbm_create_data wp_insert_post');
		//$new_id = wp_insert_post($args);
		$new_id = wprr_get_data_api()->wordpress()->editor()->create_post('dbm_data', $name, $parent_id)->get_id();
		wprr_performance_tracker()->stop_meassure('dbm_create_data wp_insert_post');
		
		/*
		if(is_wp_error($new_id)) {
			throw(new \Exception('No post created '.$new_id->get_error_message()));
		}
		*/
		
		wprr_performance_tracker()->start_meassure('dbm_create_data add_terms');
		
		//$type_term = dbm_get_type(explode('/', $type_path));
		
		$type_term = wprr_get_data_api()->wordpress()->get_taxonomy('dbm_type')->get_term($type_path);
		wp_set_post_terms($new_id, array($type_term->get_id()), 'dbm_type', false);
		
		wprr_performance_tracker()->stop_meassure('dbm_create_data add_terms');
		
		wprr_performance_tracker()->stop_meassure('dbm_create_data');
		
		return $new_id;
	}
	
	function dbm_create_draft_object_relation($from_object_id, $to_object_id, $type_path) {
		
		wprr_performance_tracker()->start_meassure('dbm_create_draft_object_relation');
		$type_term = dbm_get_type_by_path('object-relation/'.$type_path);
		
		if(!$type_term) {
			throw(new \Exception('No type '.$type_path));
		}
		
		wprr_performance_tracker()->start_meassure('dbm_create_draft_object_relation create');
		$new_id = wprr_get_data_api()->wordpress()->editor()->create_post('dbm_object_relation', $from_object_id.' '.($type_path).' '.$to_object_id)->get_id();
		wprr_performance_tracker()->stop_meassure('dbm_create_draft_object_relation create');
		
		wprr_performance_tracker()->start_meassure('dbm_create_draft_object_relation meta');
		update_post_meta($new_id, 'fromId', $from_object_id);
		update_post_meta($new_id, 'toId', $to_object_id);
		update_post_meta($new_id, 'startAt', -1);
		update_post_meta($new_id, 'endAt', -1);
		wprr_performance_tracker()->stop_meassure('dbm_create_draft_object_relation meta');
		
		wprr_performance_tracker()->start_meassure('dbm_create_draft_object_relation relations');
		$object_relation_term = dbm_get_type_by_path('object-relation');
		wp_set_post_terms($new_id, array($object_relation_term->term_id, $type_term->term_id), 'dbm_type', false);
		wprr_performance_tracker()->stop_meassure('dbm_create_draft_object_relation relations');
		
		wprr_performance_tracker()->start_meassure('dbm_create_draft_object_relation cache');
		delete_post_meta($from_object_id, 'dbm/objectRelations/outgoing');
		delete_post_meta($to_object_id, 'dbm/objectRelations/incoming');
		wprr_performance_tracker()->stop_meassure('dbm_create_draft_object_relation cache');
		
		wprr_performance_tracker()->stop_meassure('dbm_create_draft_object_relation');
		
		return $new_id;
	}
	
	function dbm_create_object_relation($from_object_id, $to_object_id, $type_path) {
		
		$new_id = dbm_create_draft_object_relation($from_object_id, $to_object_id, $type_path);
		
		global $wpdb;
		$wpdb->update( $wpdb->posts, array('post_status' => 'private'), array('ID' => $new_id));
		
		return $new_id;
	}
	
	function dbm_create_draft_object_user_relation($from_object_id, $to_user_id, $type_path) {
		
		$type_term = dbm_get_type_by_path('object-user-relation/'.$type_path);
		
		if(!$type_term) {
			throw(new \Exception('No type '.$type_path));
		}
		
		$args = array(
			'post_type' => 'dbm_object_relation',
			'post_title' => $from_object_id.' '.($type_path).' '.$to_user_id,
			'post_status' => 'draft'
		);
		
		$new_id = wp_insert_post($args);
		
		if(is_wp_error($new_id)) {
			throw(new \Exception('No post created '.$new_id->get_error_message()));
		}
		
		update_post_meta($new_id, 'fromId', $from_object_id);
		update_post_meta($new_id, 'toId', $to_user_id);
		update_post_meta($new_id, 'startAt', -1);
		update_post_meta($new_id, 'endAt', -1);
		
		$object_relation_term = dbm_get_type_by_path('object-user-relation');
		wp_set_post_terms($new_id, array($object_relation_term->term_id, $type_term->term_id), 'dbm_type', false);
		
		return $new_id;
	}
	
	function dbm_create_object_user_relation($from_object_id, $to_user_id, $type_path) {
		
		$new_id = dbm_create_draft_object_user_relation($from_object_id, $to_user_id, $type_path);
		
		global $wpdb;
		$wpdb->update( $wpdb->posts, array('post_status' => 'private'), array('ID' => $new_id));
		
		return $new_id;
	}
	
	function dbm_replace_relations($post_id, $parent_term, $new_term_ids) {
		
		if(!($parent_term instanceof \WP_Term)) {
			return;
		}
		
		$current_terms = wp_get_post_terms($post_id, 'dbm_relation');
		foreach($current_terms as $current_term) {
			if(!term_is_ancestor_of($parent_term, $current_term, 'dbm_relation')) {
				$new_term_ids[] = $current_term->term_id;
			}
		}
	
		wp_set_post_terms($post_id, $new_term_ids, 'dbm_relation', false);
	}
	
	function dbm_set_single_relation_by_name($post_id, $parent_path, $child_name) {
		
		$parent_term = dbm_get_relation_by_path($parent_path);
		$ids = dbm_get_ids_from_terms(dbm_get_relations_by_paths(array($parent_path.'/'.$child_name)));
		
		dbm_replace_relations($post_id, $parent_term, $ids);
	}
	
	function dbm_get_content_object_for_type_and_relation($post_id) {
		
		$return_object = array(
			'dbm' => array(
				'type' => array(),
				'relation' => array()
			)
		);
		
		$current_terms = wp_get_post_terms($post_id, 'dbm_type');
		foreach($current_terms as $current_term) {
			$return_object['dbm']['type'][] = $current_term->term_id;
		}
		
		$current_terms = wp_get_post_terms($post_id, 'dbm_relation');
		foreach($current_terms as $current_term) {
			if($current_term->parent) {
				$parent_term = get_term_by('id', $current_term->parent, 'dbm_relation');
				
				$relation_path = \DbmContent\OddCore\Utils\TaxonomyFunctions::get_full_term_slug($parent_term, 'dbm_relation');
				if(!isset($return_object['dbm']['relation'][$relation_path])) {
					$return_object['dbm']['relation'][$relation_path] = array();
				}
				$return_object['dbm']['relation'][$relation_path][] = $current_term->term_id;
			}
		}
		
		return $return_object;
	}
	
	function dbm_get_post_relation($post_id, $relation_path) {
		
		$return_array = array();
		
		$parent_term = \DbmContent\OddCore\Utils\TaxonomyFunctions::get_term_by_slugs(explode('/', $relation_path), 'dbm_relation');
		if(!$parent_term) {
			return $return_array;
		}
		
		$current_terms = wp_get_post_terms($post_id, 'dbm_relation');
		foreach($current_terms as $current_term) {
			if($current_term->parent === $parent_term->term_id) {
				$return_array[] = $current_term->term_id;
			}
		}
		
		return $return_array;
	}
	
	function dbm_get_single_post_relation($post_id, $relation_path) {
		$relations = dbm_get_post_relation($post_id, $relation_path);
		if(!empty($relations)) {
			return $relations[0];
		}
		
		return null;
	}
	
	function dbm_get_post_relation_with_children($post_id, $relation_path) {
		
		$return_array = array();
		
		$parent_term = \DbmContent\OddCore\Utils\TaxonomyFunctions::get_term_by_slugs(explode('/', $relation_path), 'dbm_relation');
		
		$current_terms = wp_get_post_terms($post_id, 'dbm_relation');
		foreach($current_terms as $current_term) {
			if(term_is_ancestor_of($parent_term, $current_term, 'dbm_relation')) {
				$return_array[] = $current_term->term_id;
			}
		}
		
		return $return_array;
	}
	
	function dbm_add_post_type($post_id, $type_path) {
		$term = \DbmContent\OddCore\Utils\TaxonomyFunctions::get_term_by_slugs(explode('/', $type_path), 'dbm_type');
		
		if($term) {
			wp_add_object_terms($post_id, array($term->term_id), 'dbm_type');
		}
	}
	
	function dbm_add_post_relation($post_id, $relation_path) {
		$term = \DbmContent\OddCore\Utils\TaxonomyFunctions::get_term_by_slugs(explode('/', $relation_path), 'dbm_relation');
		
		if($term) {
			wp_add_object_terms($post_id, array($term->term_id), 'dbm_relation');
		}
	}
	
	function dbm_remove_post_relation($post_id, $relation_path) {
		$term = \DbmContent\OddCore\Utils\TaxonomyFunctions::get_term_by_slugs(explode('/', $relation_path), 'dbm_relation');
		
		if($term) {
			wp_remove_object_terms($post_id, array($term->term_id), 'dbm_relation');
		}
	}
	
	function dbm_has_post_relation($post_id, $relation_path) {
		$term = \DbmContent\OddCore\Utils\TaxonomyFunctions::get_term_by_slugs(explode('/', $relation_path), 'dbm_relation');
		
		if($term) {
			return has_term($term->term_id, 'dbm_relation', $post_id);
		}
		return false;
	}
	
	function dbm_has_post_type($post_id, $type_path) {
		$term = \DbmContent\OddCore\Utils\TaxonomyFunctions::get_term_by_slugs(explode('/', $type_path), 'dbm_type');
		
		if($term) {
			return has_term($term->term_id, 'dbm_type', $post_id);
		}
		return false;
	}
	
	function dbm_get_post_id_by_type_and_relation($post_type = 'any', $type_paths = null, $relation_paths = null) {
		$query_args = array();
		
		if($post_type !== 'any') {
			$postTypes = explode(',', $post_type);
			$query_args['post_type'] = $postTypes;
		}
		else {
			$query_args['post_type'] = get_post_types(array(), 'names');
		}
		
		$dbm_query = dbm_new_query($query_args);
		
		if($type_paths) {
			$dbm_query->add_type_by_path(implode(',', $type_paths));
		}
		if($relation_paths) {
			$dbm_query->add_relation_by_path(implode(',', $relation_paths));
		}
		
		return $dbm_query->get_post_id();
		
	}
	
	function dbm_get_post_ids_by_type_and_relation($post_type = 'any', $type_paths = null, $relation_paths = null) {
		$query_args = array();
		
		if($post_type !== 'any') {
			$postTypes = explode(',', $post_type);
			$query_args['post_type'] = $postTypes;
		}
		else {
			$query_args['post_type'] = get_post_types(array(), 'names');
		}
		
		$dbm_query = dbm_new_query($query_args);
		
		if($type_paths) {
			$dbm_query->add_type_by_path(implode(',', $type_paths));
		}
		if($relation_paths) {
			$dbm_query->add_relation_by_path(implode(',', $relation_paths));
		}
		
		return $dbm_query->get_post_ids();
	}
	
	function dbm_filter_custom_range_relation_query($query_args, $data) {
		$custom_range_filters = new \DbmContent\CustomRangeFilters();
		
		return $custom_range_filters->query_relations($query_args, $data);
	}
	
	function dbm_get_owned_relation_id($owner_id, $group) {
		$meta_name = 'dbm_relation_term_'.$group;
		$term_id = (int)get_post_meta($owner_id, $meta_name, true);
		
		return $term_id;
	}
	
	function dbm_get_owned_relation($owner_id, $group) {
		$term_id = dbm_get_owned_relation_id($owner_id, $group);
		
		if($term_id) {
			return get_term_by('id', $term_id, 'dbm_relation');
		}
		
		return null;
	}
	
	function dbm_new_query($query_args_or_post_type = null) {
		$new_query = new \DbmContent\DbmQuery();
		
		if($query_args_or_post_type) {
			if(is_string($query_args_or_post_type)) {
				$new_query->set_post_type($query_args_or_post_type);
			}
			else if(is_array($query_args_or_post_type)) {
				$new_query->set_query_args($query_args_or_post_type);
			}
		}
		
		return $new_query;
	}
	
	global $dbm_posts;
	$dbm_posts = array();
	
	function dbm_get_post($id) {
		global $dbm_posts;
		if(isset($dbm_posts[$id])) {
			return $dbm_posts[$id];
		}
		$new_post = new \DbmContent\DbmPost($id);
		$dbm_posts[$id] = $new_post;
		
		return $new_post;
	}
	
	global $dbm_object_relations;
	$dbm_object_relations = array();
	
	function dbm_get_object_relation($id) {
		global $dbm_object_relations;
		if(isset($dbm_object_relations[$id])) {
			return $dbm_object_relations[$id];
		}
		$new_post = new \DbmContent\DbmObjectRelation($id);
		$dbm_object_relations[$id] = $new_post;
		
		return $new_post;
	}
	
	function dbm_get_number_sequence($id) {
		
		if(!get_post_meta($id, 'currentSequenceNumber', true)) {
			update_post_meta($id, 'currentSequenceNumber', 0);
		}
		
		$new_post = new \DbmContent\DbmNumberSequence($id);
		
		return $new_post;
	}
	
	function dbm_get_process($id) {
		
		$new_process = new \DbmContent\Process\DbmProcess($id);
		
		return $new_process;
	}
	
	function dbm_get_process_part($id) {
		
		$new_process_part = new \DbmContent\Process\DbmProcessPart($id);
		
		return $new_process_part;
	}
	
	function dbm_get_process_for_item($id) {
		
		$new_process_for_item = new \DbmContent\Process\DbmProcessForItem($id);
		
		return $new_process_for_item;
	}
	
	function dbm_get_global_page_id($slug) {
		$id = dbm_new_query('page')->add_relation_by_path('global-pages/'.$slug)->get_post_id();
		
		return $id;
	}
	
	function dbm_get_objects_by_user_relation($user_id, $relation_type, $object_type, $time = -1) {
		$dbm_query = dbm_new_query('dbm_object_relation')->set_field('post_status', array('publish', 'private'));
		$dbm_query->add_type_by_path('object-user-relation')->add_type_by_path('object-user-relation/'.$relation_type);
		$dbm_query->add_meta_query('toId', $user_id);
		
		$return_array = array();
		$relation_ids = $dbm_query->get_post_ids();
		
		if($time !== false) {
			if($time === -1) {
				$time = time();
			}
			
			$filtered_ids = array();
			
			foreach($relation_ids as $relation_id) {
				$start_at = (int)get_post_meta($relation_id, 'startAt', true);
				$end_at = (int)get_post_meta($relation_id, 'endAt', true);
				if(($start_at === -1 || $start_at <= $time) && ($end_at === -1 || $end_at > $time)) {
					$filtered_ids[] = $relation_id;
				}
			}
			
			$relation_ids = $filtered_ids;
		}
		
		$term = dbm_get_type_by_path($object_type);
		if($term) {
			foreach($relation_ids as $relation_id) {
			
				$post_id = get_post_meta($relation_id, 'fromId', true);
				if(has_term($term->term_id, 'dbm_type', $post_id)) {
					$return_array[] = $post_id;
				}
			}
		}
		
		return $return_array;
	}
	
	function dbm_get_single_object_by_user_relation($user_id, $relation_type, $object_type, $time = -1) {
		$ids = dbm_get_objects_by_user_relation($user_id, $relation_type, $object_type, $time);
		$count = count($ids);
		if($count > 0) {
			if($count > 1) {
				//METODO: error message
			}
			
			return $ids[0];
		}
		//METODO: error message
		return 0;
	}
	
	function dbm_get_global_item($identifier) {
		$global_item_id = dbm_new_query('dbm_data')->set_field('post_status', array('publish', 'private'))->add_meta_query('identifier', $identifier)->get_post_id();
		
		$return_id = 0;
		
		if($global_item_id) {
			$dbm_post = dbm_get_post($global_item_id);
			
			$outgoing_relation_id = $dbm_post->get_single_outgoing_relation('pointing-to', null);
			$to_id = (int)get_post_meta($outgoing_relation_id, 'toId', true);
			
			if($to_id) {
				$return_id = $to_id;
			}
		}
		
		return $return_id;
	}
	
	function dbm_relation_ids($relation_ids, $key) {
		$return_array = array();
		
		foreach($relation_ids as $relation_id) {
			$return_array[] = (int)get_post_meta($relation_id, $key, true);
		}
		
		return $return_array;
	}
	
	function dbm_outgoing_relation_ids($relation_ids) {
		return dbm_relation_ids($relation_ids, 'toId');
	}
	
	function dbm_incoming_relation_ids($relation_ids) {
		return dbm_relation_ids($relation_ids, 'fromId');
	}
	
	function dbm_setup_get_manager() {
		$manager = new \DbmContent\Admin\Setup\SetupManager();
		
		return $manager;
	}
	
	function dbm_trash_item($id) {
		$post = dbm_get_post($id);
		
		$remove_collection = new \DbmContent\RemoveCollection();
		
		$remove_collection->set_origin_id($id);
		$post->get_remove_items($remove_collection);
		
		$remove_collection->perform_remove_all();
	}
	
	function dbm_clear_post_cache($post_id) {
		$action_name = 'dbm_content/clear_post_cache';
		do_action($action_name, $post_id);
	}
	
	global $dbm_content_identifiable_items;
	$dbm_content_identifiable_items = array();
	
	function dbm_get_identifiable_data($type, $identifier) {
		global $dbm_content_identifiable_items;
		
		if(isset($dbm_content_identifiable_items[$type][$identifier])) {
			return $dbm_content_identifiable_items[$type][$identifier];
		}
		
		if(!isset($dbm_content_identifiable_items[$type])) {
			$dbm_content_identifiable_items[$type] = array();
		}
		
		$post_id = dbm_new_query('dbm_data')->include_private()->add_type_by_path($type)->add_meta_query('identifier', $identifier)->get_post_id();
		$dbm_content_identifiable_items[$type] = $post_id;
		
		return $post_id;
	}
	
	global $dbm_term_cache;
	$dbm_term_cache = array();
	
	function dbm_get_post_ids_for_term_id($term_id) {
		global $dbm_term_cache;
		
		if(!isset($dbm_term_cache[$term_id])) {
			global $wpdb;
	
			$statement = "SELECT object_id FROM {$wpdb->prefix}term_relationships WHERE term_taxonomy_id = %d";
			$safe_statement = $wpdb->prepare($statement, $term_id);
	
			$results = $wpdb->get_results($safe_statement, ARRAY_N);
	
			$ids = array_map('intval', array_column($results, 0));
			$dbm_term_cache[$term_id] = $ids;
		}

		return $dbm_term_cache[$term_id];
	}
?>