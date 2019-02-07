<?php 
	
	
	function mixColor( $emotions, $threshold = 5 ) {
		
		$colors = [
			'CALM' 		=>  [0,1,1],
			'DISGUSTED'	=>  [0,1,0],
			'ANGRY'		=>  [1,0,0],
			'HAPPY'		=>  [1,1,0],
			'SAD' 		=>  [0,0,1], 
			'SURPRISED' =>  [1,1,1],
			'CONFUSED'	=>  [1,0,1]
		];
		
		$red = [];
		$green = [];
		$blue = [];
				
		foreach($emotions as $emotion => $value) {
			if($value < $threshold) continue;
			
			$colorMatrix = $colors[$emotion];
			foreach($colorMatrix as $pos => $exists){
				
				switch(true){
					case $pos == 0 && $exists == 1 :
						$red[] = $value;
						break;
					case $pos == 1 && $exists == 1 :
						$green[] = $value;
						break;
					case $pos == 2 && $exists == 1 :
						$blue[] = $value;
						break;
				}	
			}
		}
		
		$red =  count($red) > 0 ? round(array_sum($red) / count($red)) : 0;		
		$green = count($green) > 0 ? round(array_sum($green) / count($green)) : 0;		
		$blue = count($blue) > 0 ? round(array_sum($blue) / count($blue)) : 0;
		
		$red = $red > 0 ?  round(($red/100) * 255) : 0;
		$green = $green > 0 ?round(($green/100) * 255) : 0;
		$blue = $blue > 0 ? round(($blue/100) * 255) : 0;
		
		return 'rgb('.$red.','.$green.','.$blue.')';
				
	}