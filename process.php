<?php 
	
	date_default_timezone_set('Europe/Brussels');
	
	include_once __DIR__ . "_config.php";
	include_once __DIR__ . "_functions.php";
	include_once __DIR__ . '/vendor/autoload.php';
	
	$image = $_POST['image'];
	
	$encoding = substr($image,  strpos($image, ',' ) +1 );
	$filename = 'images/image_ ' . @date('y-m-d-H-i-s') . '.png';
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
	
	$datas = [
		'filename'	=> $filename,
		'emotions'	=> $emotions,
		'color'		=> mixColor($emotions)
	];		
	
	echo json_encode($datas, JSON_PRETTY_PRINT);