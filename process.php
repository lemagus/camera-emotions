<?php 
	
	date_default_timezone_set('Europe/Brussels');
	
	include_once __DIR__ . "/_config.php";
	include_once __DIR__ . "/_functions.php";
	include_once __DIR__ . '/vendor/autoload.php';
	
	use Intervention\Image\ImageManager;
	$manager = new ImageManager(array('driver' => 'gd'));
	
	$image = $_POST['image'];
	
	$encoding = substr($image,  strpos($image, ',' ) +1 );
	
	$filename = 'images/image_' . @date('y-m-d-H-i-s') . '.png';
	$modified = 'images/modified/image_' . @date('y-m-d-H-i-s') . '.png';
	
	$datas 		= base64_decode($encoding);
	
	file_put_contents(
		$filename, $datas);
		
	include __DIR__ . '/_config.php';
	
	use Aws\Rekognition\RekognitionClient;
	
	$rekognition = new RekognitionClient([
  		'region'            => 'eu-west-1',
  		'version'           => '2016-06-27',
  		'credentials' => [
       		'key'    => API_KEY,
	   		'secret' => API_SECRET,
		]
	]);
	
	$result = $rekognition->detectFaces([
	  'Attributes' => [ "ALL" ],
      'Image' => array(
         'Bytes' => $datas,
		)
	]);
	
	$details = $result["FaceDetails"][0];
				
	$emotions = [];
	foreach($result["FaceDetails"][0]['Emotions'] as $emotion) {
		$emotions[$emotion['Type']] = $emotion['Confidence'];
	}
	
	arsort($emotions);
	
	$colors 	= mixColor($emotions);
	
	//$best 		= array_keys($emotions)[0];
	//$bestValue	= array_values($emotions)[0];
	
	$img = $manager->make( $filename );
	
	$img->colorize(-50 + $colors['red'], -50 + $colors['green'], -50 + $colors['blue']);
	
	$keywords = ['much', 'such', 'many'];
	
	$it = 0;
	foreach($emotions as $emotion => $value) {
		$it++;
		if($it > 3 ) break;
		
		$x = $img->width() /2 - mt_rand(- ($img->width() /4), $img->width() /4);
		$y = (($img->height() / 3) * ($it -1)) + ($img->height() / 6) ;
		
		$text = strtoupper($keywords[ mt_rand(0, count($keywords)-1 )]) . ' ' . $emotion;
		
		$strokeSize = 2;
		
		$img->text($text, $x , $y, function($font) use($value, $strokeSize)
		{
		
			$ratio = 100;
			$fontSize = 60 + (($ratio / 100) * $value) + $strokeSize;
							
		    $font->file( __DIR__ . '/fonts/impact.ttf');
		    $font->size($fontSize);
		    $font->color('#000000');
		    
		    $font->align('center');
		    $font->valign('center');
		    
		});
		
		$img->text($text, $x , $y, function($font) use($value)
		{
		
			$ratio = 100;
			$fontSize = 60 + (($ratio / 100) * $value);
							
		    $font->file( __DIR__ . '/fonts/impact.ttf');
		    $font->size($fontSize);
		    $font->color('#FFFFFF');
		    
		    $font->align('center');
		    $font->valign('center');
		    
		});
		
	}
	
	
	
	
	$img->save( $modified );
	
	$datas = [
		'filename'	=> $modified,
		'emotions'	=> $emotions,
		'color'		=> $colors
	];		
	
	echo json_encode($datas, JSON_PRETTY_PRINT);