<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>UK College</title>

	<?php wp_head(); ?>

	<?php
		$folder = get_template_directory();
		$ghostBuster = filemtime($folder . '/dist/style.bundle.css');
	?>
	<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri() ?>/dist/style.bundle.css?v=<?php echo $ghostBuster; ?>">
</head>
<body>