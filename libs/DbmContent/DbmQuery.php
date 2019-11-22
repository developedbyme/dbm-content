<?php
	namespace DbmContent;

	class DbmQuery {

		protected $query_args = array();

		function __construct() {
			//echo("\DbmContent\DbmQuery::__construct<br />");
			
		}
		
		public function set_query_args($query_args) {
			$this->query_args = $query_args;
			
			return $this;
		}
		
		public function set_argument($name, $value) {
			$this->query_args[$name] = $value;
			
			return $this;
		}
		
		public function set_post_type($post_type) {
			$this->set_field('post_type', $post_type);
			
			return $this;
		}
		
		public function set_field($field_name, $value) {
			$this->query_args[$field_name] = $value;
			
			return $this;
		}
		
		public function ensure_meta_query_exists() {
			if(!isset($this->query_args['meta_query'])) {
				$this->query_args['meta_query'] = array(
					'relation' => 'AND'
				);
			}
			
			return $this;
		}
		
		public function ensure_tax_query_exists() {
			if(!isset($this->query_args['tax_query'])) {
				$this->query_args['tax_query'] = array(
					'relation' => 'AND'
				);
			}
			
			return $this;
		}
		
		public function add_query($tax_query) {
			$this->ensure_tax_query_exists();
			
			$this->query_args['tax_query'][] = $tax_query;
		}
		
		public function add_relation_term($term) {
			if(!isset($term)) {
				//METODO: error message
				$tax_query = DbmQuery::create_no_term_tax_query('dbm_relation');
				$this->add_query($tax_query);
				return $this;
			}
			$tax_query = DbmQuery::create_term_ids_tax_query(array($term->term_id), 'dbm_relation');
			$this->add_query($tax_query);
			
			return $this;
		}
		
		public function add_relation_ids($ids, $operator = 'AND') {
			$tax_query = DbmQuery::create_term_ids_tax_query($ids, 'dbm_relation', false, $operator);
			$this->add_query($tax_query);
			
			return $this;
		}
		
		public function add_relation_by_id($term_id) {
			if(!isset($term_id)) {
				//METODO: error message
				$tax_query = DbmQuery::create_no_term_tax_query('dbm_relation');
				$this->add_query($tax_query);
				return $this;
			}
			$tax_query = DbmQuery::create_term_ids_tax_query(array($term_id), 'dbm_relation');
			$this->add_query($tax_query);
			
			return $this;
		}
		
		public function add_relations_from_post($post_id, $relation_path) {
			
			$tax_query = DbmQuery::create_term_ids_tax_query(dbm_get_post_relation($post_id, $relation_path), 'dbm_relation');
			$this->add_query($tax_query);
			
			return $this;
		}
		
		public function add_relation_from_owner($post_id, $group_name) {
			$related_term = dbm_get_owned_relation($post_id, $group_name);
			
			$this->add_relation_term($related_term);
			
			return $this;
		}
		
		public function add_relation_by_path($path) {
			if(!isset($path)) {
				//METODO: error message
				$tax_query = DbmQuery::create_no_term_tax_query('dbm_relation');
				$this->add_query($tax_query);
				return $this;
			}
			
			$term = \DbmContent\OddCore\Utils\TaxonomyFunctions::get_term_by_slug_path($path, 'dbm_relation');
			
			if(!$term) {
				//METODO: error message
				$tax_query = DbmQuery::create_no_term_tax_query('dbm_relation');
				$this->add_query($tax_query);
				return $this;
			}
			
			$tax_query = DbmQuery::create_term_ids_tax_query(array($term->term_id), 'dbm_relation');
			$this->add_query($tax_query);
			
			return $this;
		}
		
		public function add_type_by_path($path) {
			if(!isset($path)) {
				//METODO: error message
				$tax_query = DbmQuery::create_no_term_tax_query('dbm_type');
				$this->add_query($tax_query);
				return $this;
			}
			
			$term = \DbmContent\OddCore\Utils\TaxonomyFunctions::get_term_by_slug_path($path, 'dbm_type');
			
			if(!$term) {
				//METODO: error message
				$tax_query = DbmQuery::create_no_term_tax_query('dbm_type');
				$this->add_query($tax_query);
				return $this;
			}
			
			$tax_query = DbmQuery::create_term_ids_tax_query(array($term->term_id), 'dbm_type');
			$this->add_query($tax_query);
			
			return $this;
		}
		
		public function add_type_ids($ids, $operator = 'AND') {
			$tax_query = DbmQuery::create_term_ids_tax_query($ids, 'dbm_type', false, $operator);
			$this->add_query($tax_query);
			
			return $this;
		}
		
		public function add_meta_query($field, $value, $compare = '=', $type = 'CHAR') {
			$query = array(
				'key' => $field,
				'value' => $value,
				'compare' => $compare,
				'type' => $type,
			);
			
			$this->ensure_meta_query_exists();
			$this->query_args['meta_query'][] = $query;
			
			return $this;
		}
		
		public function get_query_args() {
			return $this->query_args;
		}
		
		public function debug_print_query_args() {
			
			var_dump($this->query_args);
			
			return $this;
		}
		
		public function get_post_ids() {
			$query_args = $this->get_query_args();
			
			$ids = $this->perform_ids_query($query_args);
			
			return $ids;
		}
		
		public function get_post_id() {
			$query_args = $this->get_query_args();
			
			$ids = $this->perform_ids_query($query_args);
			
			if(empty($ids)) {
				//METODO: error message
				return 0;
			}
			
			return $ids[0];
		}
		
		public function get_post_id_if_exists() {
			$query_args = $this->get_query_args();
			
			$ids = $this->perform_ids_query($query_args);
			
			if(empty($ids)) {
				return 0;
			}
			
			return $ids[0];
		}
		
		public function perform_ids_query($query_args) {
			$query_args['fields'] = 'ids';
			$query_args['posts_per_page'] = -1;
			$query_args['suppress_filters'] = 0;
			return get_posts($query_args);
		}
		
		public static function create_term_ids_tax_query($term_ids, $taxonomy, $include_chldren = false, $operator = 'AND') {
			return array(
				'taxonomy' => $taxonomy,
				'field' => 'id',
				'terms' => $term_ids,
				'include_children' => $include_chldren,
				'operator' => $operator
			);
		}
		
		public static function create_no_term_tax_query($taxonomy) {
			return array(
				'taxonomy' => $taxonomy,
				'field' => 'id',
				'terms' => array(-1),
				'include_children' => false,
				'operator' => 'AND'
			);
		}
		
		public static function test_import() {
			echo("Imported \DbmContent\DbmQuery<br />");
		}
	}
?>
