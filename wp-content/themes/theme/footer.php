<?php wp_footer(); ?>

<?php
	$folder = get_template_directory();
	$ghostBuster = filemtime($folder . '/dist/app.bundle.js');
?>
<script src="<?php echo get_stylesheet_directory_uri() ?>/dist/app.bundle.js?v=<?php echo $ghostBuster; ?>"></script>
</body>