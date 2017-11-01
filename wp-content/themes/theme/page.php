<?php 
get_header();

global $post;
$builder = get_fields();

// Output all the templates
foreach ($builder['builder'] as $template) {
	$template['ID'] = $post->ID;
	get_view($template['acf_fc_layout'], $template);
}

get_footer();
?>