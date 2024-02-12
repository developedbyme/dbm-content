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
		}
		
		wprr_performance_tracker()->start_meassure('dbm_create_data wp_insert_post');
		//$new_id = wp_insert_post($args);
		$new_id = wprr_get_data_api()->wordpress()->editor()->create_post('dbm_data', $name, $parent_id)->get_id();
		$post_editor = wprr_get_data_api()->wordpress()->editor()->get_post_editor($new_id);
		wprr_performance_tracker()->stop_meassure('dbm_create_data wp_insert_post');
		
		wprr_performance_tracker()->start_meassure('dbm_create_data add_terms');
		
		$type_term = wprr_get_data_api()->wordpress()->get_taxonomy('dbm_type')->get_term($type_path);
		$post_editor->add_term_by_id($type_term->get_id());
		
		wprr_performance_tracker()->stop_meassure('dbm_create_data add_terms');
		
		wprr_performance_tracker()->stop_meassure('dbm_create_data');
		
		return $new_id;
	}
	
	function dbm_create_draft_object_relation($from_object_id, $to_object_id, $type_path, $start_time = -1) {
		
		$from_post = wprr_get_data_api()->wordpress()->get_post($from_object_id);
		$to_post = wprr_get_data_api()->wordpress()->get_post($to_object_id);
		
		$post = wprr_get_data_api()->wordpress()->editor()->create_relation($from_post, $to_post, $type_path, $start_time);
		
		return $post->get_id();
	}
	
	function dbm_create_object_relation($from_object_id, $to_object_id, $type_path, $start_time = -1) {
		
		$new_id = dbm_create_draft_object_relation($from_object_id, $to_object_id, $type_path, $start_time);
		
		$post = wprr_get_data_api()->wordpress()->get_post($new_id);
		$post->editor()->make_private();
		
		return $new_id;
	}
	
	function dbm_create_draft_object_user_relation($from_object_id, $to_user_id, $type_path, $start_time = -1) {
		
		$from_post = wprr_get_data_api()->wordpress()->get_post($from_object_id);
		$to_user = wprr_get_data_api()->wordpress()->get_user($to_user_id);
		
		$post = wprr_get_data_api()->wordpress()->editor()->create_relation($from_post, $to_user, $type_path, $start_time);
		
		return $post->get_id();
	}
	
	function dbm_create_object_user_relation($from_object_id, $to_user_id, $type_path) {
		
		$new_id = dbm_create_draft_object_user_relation($from_object_id, $to_user_id, $type_path);
		
		$post = wprr_get_data_api()->wordpress()->get_post($new_id);
		$post->editor()->make_private();
		
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
	
	function dbm_get_number_sequence($id) {
		
		if(!get_post_meta($id, 'currentSequenceNumber', true)) {
			update_post_meta($id, 'currentSequenceNumber', 0);
		}
		
		$new_post = new \DbmContent\DbmNumberSequence($id);
		
		return $new_post;
	}
	
	function dbm_get_global_page_id($slug) {
		$id = dbm_new_query('page')->add_relation_by_path('global-pages/'.$slug)->get_post_id();
		
		return $id;
	}
	
	function dbm_get_objects_by_user_relation($user_id, $relation_type, $object_type, $time = -1) {
		
		$relations = wprr_get_data_api()->wordpress()->wordpress()->get_user($user_id)->get_post_relations()->get_type($relation_type)->get_relations($object_type, $time);
		
		$ids = array_map(function($relation) {return $relation->get_object_id();}, $relations);
		
		return $ids;
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