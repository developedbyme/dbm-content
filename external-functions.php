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
	}
	
	function dbm_content_add_owned_relationship_with_auto_add($type, $relation_term, $type_group = null) {
		$owned_term = new \DbmContent\Admin\Hooks\OwnedRelationTerm();
		$owned_term->setup($type, $relation_term);
		$owned_term->set_add_term_to_owner_post(true);
		if(isset($type_group)) {
			$owned_term->set_type_group($type_group);
		}
		$owned_term->register_hooks();
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
	
	function dbm_get_tax_query_for_relation_paths($paths) {
		
		$paths = dbm_get_relations_by_paths($paths);
		
		if(isset($paths) && count($paths) > 0) {
			$ids = array_map(function($term) {return $term->term_id;}, $paths);
			
			return array(
				'taxonomy' => 'dbm_relation',
				'field' => 'id',
				'terms' => $ids,
				'include_children' => false
			);
		}
		
		return array(
			'taxonomy' => 'dbm_relation',
			'field' => 'id',
			'terms' => array(-1),
			'include_children' => false
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
	
	function dbm_get_tax_query_for_type_paths($paths) {
		
		$paths = dbm_get_types_by_paths($paths);
		
		if(isset($paths) && count($paths) > 0) {
			$ids = array_map(function($term) {return $term->term_id;}, $paths);
			
			return array(
				'taxonomy' => 'dbm_type',
				'field' => 'id',
				'terms' => $ids,
				'include_children' => false
			);
		}
		
		return array(
			'taxonomy' => 'dbm_type',
			'field' => 'id',
			'terms' => array(-1),
			'include_children' => false
		);
	}
	
	function dbm_create_data($name, $type_path, $grouping_path) {
		
		$parent_grouping_term = dbm_get_type(explode('/', $grouping_path));
		$parent_id = \DbmContent\OddCore\Utils\TaxonomyFunctions::get_single_post_id_by_term($parent_grouping_term);
		
		$args = array(
			'post_type' => 'dbm_data',
			'post_title' => $name,
			'post_parent' => $parent_id,
			'post_status' => 'draft'
		);
		
		$new_id = wp_insert_post($args);
		
		if(!$new_id) {
			//METODO: error message
			return $new_id;
		}
		
		$type_term = dbm_get_type(explode('/', $type_path));
		wp_set_post_terms($new_id, array($type_term->term_id), 'dbm_type', false);
		
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
		
		$current_terms = wp_get_post_terms($post_id, 'dbm_relation');
		foreach($current_terms as $current_term) {
			if($current_term->parent === $parent_term->term_id) {
				$return_array[] = $current_term->term_id;
			}
		}
		
		return $return_array;
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
	
	function dbm_get_post_id_by_type_and_relation($post_type = 'any', $type_paths = null, $relation_paths = null) {
		$query_args = array(
			'posts_per_page' => 1,
			'fields' => 'ids'
		);
		
		if($post_type !== 'any') {
			$postTypes = explode(',', $post_type);
			$query_args['post_type'] = $postTypes;
		}
		else {
			$query_args['post_type'] = get_post_types(array(), 'names');
		}
		
		$tax_query = array(
			'relation' => 'AND',
		);
		
		if($type_paths) {
			$current_tax_query = dbm_get_tax_query_for_type_paths($type_paths);
			array_push($tax_query, $current_tax_query);
		}
		if($relation_paths) {
			$current_tax_query = dbm_get_tax_query_for_relation_paths($relation_paths);
			array_push($tax_query, $current_tax_query);
		}
		
		$query_args['tax_query'] = $tax_query;
		
		$posts = get_posts($query_args);
		
		if(count($posts) > 0) {
			return $posts[0];
		}
		
		return null;
	}
	
	function dbm_get_post_ids_by_type_and_relation($post_type = 'any', $type_paths = null, $relation_paths = null) {
		$query_args = array(
			'posts_per_page' => -1,
			'fields' => 'ids'
		);
		
		if($post_type !== 'any') {
			$postTypes = explode(',', $post_type);
			$query_args['post_type'] = $postTypes;
		}
		else {
			$query_args['post_type'] = get_post_types(array(), 'names');
		}
		
		$tax_query = array(
			'relation' => 'AND',
		);
		
		if($type_paths) {
			$current_tax_query = dbm_get_tax_query_for_type_paths($type_paths);
			array_push($tax_query, $current_tax_query);
		}
		if($relation_paths) {
			$current_tax_query = dbm_get_tax_query_for_relation_paths($relation_paths);
			array_push($tax_query, $current_tax_query);
		}
		
		$query_args['tax_query'] = $tax_query;
		
		$posts = get_posts($query_args);
		
		return $posts;
	}
	
	function dbm_filter_custom_range_relation_query($query_args, $data) {
		$custom_range_filters = new \DbmContent\CustomRangeFilters();
		
		return $custom_range_filters->query_relations($query_args, $data);
	}
?>