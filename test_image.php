<?php
	
	include_once __DIR__ . '/vendor/autoload.php';
	
	use Intervention\Image\ImageManager;
	
	$manager = new ImageManager(array('driver' => 'gd'));
	
	$img = $manager->make( __DIR__ . '/images/image_ 19-02-07-16-54-39.png' );
	
	$img->colorize(-100, -100, 0);
	
	echo $img->response();

