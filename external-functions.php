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
	
	function dbm_get_type($slugs) {
		return \DbmContent\OddCore\Utils\TaxonomyFunctions::get_term_by_slugs($slugs, 'dbm_type');
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
?>