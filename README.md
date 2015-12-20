[![Stable Release](https://img.shields.io/packagist/v/johnbillion/extended-taxos.svg)](https://packagist.org/packages/johnbillion/extended-taxos)
[![License](https://img.shields.io/badge/license-GPL_v2%2B-blue.svg)](https://github.com/johnbillion/extended-taxos/blob/master/LICENSE)

# Extended Taxonomies #

Extended Taxonomies is a library which provides extended functionality to WordPress custom taxonomies, allowing developers to quickly build custom taxonomies without having to write the same code again and again.

This is a sister library to [Extended CPTs](https://github.com/johnbillion/extended-cpts).

## Improved defaults ##

 * Automatically generated labels and term updated messages
 * Public taxonomy with admin UI enabled

## Minimum Requirements ##

**PHP:** 5.4  
**WordPress:** 4.3  

## Usage ##

Need a simple taxonomy with no frills? You can register a taxonomy with two parameters:

```php
register_extended_taxonomy( 'location', 'post' );
```

Try it. You'll have a hierarchical public taxonomy with an admin UI, and all the labels and term updated messages will be automatically generated. Or for a bit more functionality:

```php
register_extended_taxonomy( 'story', 'post', array(

	# Use radio buttons in the meta box for this taxonomy on the post editing screen:
	'meta_box' => 'radio',

	# Show this taxonomy in the 'At a Glance' dashboard widget:
	'dashboard_glance' => true,

	# Add a custom column to the admin screen:
	'admin_cols' => array(
		'updated' => array(
			'title'       => 'Updated',
			'meta_key'    => 'updated_date',
			'date_format' => 'd/m/Y'
		),
	),

), array(

	# Override the base names used for labels:
	'singular' => 'Story',
	'plural'   => 'Stories',
	'slug'     => 'tales'

) );
```

Bam, we have a 'Stories' taxonomy attached to the Post post type, with correctly generated labels and term updated messages, radio buttons in place of the standard meta box for this taxonomy on the post editing screen, and a count of the terms in this taxonomy in the 'At a Glance' dashboard widget.

## License: GPLv2 or later ##

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
