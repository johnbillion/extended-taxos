
## Changelog ##

### 2.0.2 ###

* Bugfix for the term updated messages.

### 2.0.1 ###

* Add some missing escaping and update some inline docs.

### 2.0.0 ###

* Bump the minimum supported WordPress version to 4.4.
* Extended Taxonomies no longer contains a plugin header, reinforcing the fact this is a developer library.
* Implement support for custom columns on the term listing screen in the admin area.
* Implement automatic integration with the Rewrite Rule Testing plugin.
* Add filters for the taxonomy arguments and taxonomy names.
* Add before and after actions to the custom meta box output.

### 1.6.0 ###

* Correctly handle non-hierarchical taxonomies in the `dropdown` meta box.
* Update the 'Right Now' code to support the 'At a Glance' dashboard widget.
* New labels for WordPress 4.3 and 4.4.
* Remove the default value for the `show_in_nav_menus` argument.
* Add support for the `no_terms` label.
* Remove back-compat method of providing singular, plural, and slug names.
* Wrap functions and classes in `function_exists` and `class_exists` checks, respectively.

### 1.5.4 ###

* Added Composer type of wordpress-plugin.

### 1.5.3 ###

* Remove the `autoload` section from composer.json.
* Fixing PHP notice on term_updated_messages.

### 1.5.2 ###

* Bugfix for the term updated messages.

### 1.5.1 ###

* Remove type hinting from the `meta_boxes()` method to account for the fact that the `add_meta_boxes` action can be fired on non-post screens.

### 1.5.0 ###

* Name parameters should now be passed as an associative array.
* Code overhaul to split the admin area functionality into its own class.

### 1.4.2 ###
* Fix use of 'selected'/'selected_cats' parameters
* Removed unused Walker_ExtendedTaxonomyDropdownSlug class

### 1.4.1 ###
* Full support for non-hierarchical taxonomies in walker classes (thanks @simonwheatley)
* Use 'query_var' parameter instead of taxonomy name where required
* Raise an error if taxonomy query var clashes with an existing post type
* Avoid using checked_ontop by default

### 1.4 ###
* More improvements to plural, singular and slug generation
* Add a check for the 'assign_terms' cap before adding the meta box

### 1.3.2 ###
* Allow meta box to be disabled with boolean false
* Add a 'dropdown' type meta box and walker class

### 1.3.1 ###
* New 'checked_ontop' argument
* A little more error prevention

### 1.3 ###
* Some error prevention

### 1.2.9 ###
* Allow register_extended_taxonomy() to be called on or before the init hook
* Inline docs!

### 1.2.8 ###
* Remove the unnecessary custom walker class for simple meta boxes

### 1.2.7 ###
* Improve the plural, singular and slug generation

### 1.2.6 ###
* Add support for displaying the taxonomy in the Right Now dashboard widget

### 1.2.5 ###
* Add preemptive support for term updated messages (see http://core.trac.wordpress.org/ticket/18714)

### 1.2.4 ###
* Support for Term Order plugin

### 1.2.3 ###
* Better defaults for rewrites
* Taxonomy dropdown menu walker class

### 1.2.2 ###
* Register taxonomies with an earlier priority on init

### 1.2.1 ###
* Initial move to separate repo for ExtTaxos. Oldest version I have is 1.2.1
