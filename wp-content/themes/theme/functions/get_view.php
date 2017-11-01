<?php
function get_view($viewName, $vars) {
	$view = __DIR__ . '/../views/templates/' . $viewName . '.php';

	$data = $vars;
	include $view;
}
