# DBM Content data

This plugin enables more content types and linking

## Usage

Add types and relations to pages, posts and attachments.

## Installation
### From your WordPress dashboard

1. Visit 'Plugins > Add New'
2. Search for 'DBM Content'
3. Activate DBM Content from your Plugins page.

### From WordPress.org
1. Upload the folder `dbm-content` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.

## Functions
### dbm_create_data($name, $type_path, $grouping_path)
Creates a new data post

### dbm_get_owned_relation($owner_id, $group)
Gets the relation that is owned by an object

## Range options
### Selections
#### byOwnedRelation
Gets items that has the same relation as the owner. 

Parameters

* ownedRelation - Comma separated string of group:id

/wprr/v1/range/page/byOwnedRelation/default?ownedRelation=example-group:1234

## Changelog

### 0.1
* First release.