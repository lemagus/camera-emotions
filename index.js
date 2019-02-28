/* Functions */

function takeSnapshot(video) {
	
	var context;
	var width = video.offsetWidth
	var height = video.offsetHeight;
	
	canvas = document.createElement('canvas');
	
	canvas.width = width;
	canvas.height = height;
	
	context = canvas.getContext('2d');
	context.drawImage(video, 0, 0, width, height);
	
	return canvas.toDataURL('image/png');
}

/* */

var video = document.querySelector('video')

navigator.getMedia = (navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia)

navigator.getMedia({ video: true, audio: false }, function(stream) {
	video.srcObject = stream
	video.play()
	
	}, function(e) {
	alert("Une erreur est survenue : ", e)
})

var button = document.querySelector('button.camera')

var trigger;
var triggerTime = 3;

button.addEventListener('click', function(MouseEvent) {
	
	var countdown = document.querySelector('.countdown');
	countdown.classList.remove('hide');
	countdown.innerHTML = triggerTime--;
	
	trigger = setInterval(function(){
		
		if(triggerTime <= 0) {
			
			clearInterval(trigger);
			triggerTime = 3;
			countdown.innerHTML = '';
			countdown.classList.add('hide')
			
			var snapshot = takeSnapshot(video)
			var image = document.createElement('img')
			
			image.src = snapshot
			image.classList.add('snapshot')
			
			document.body.appendChild(image)
			
			var data = new FormData();
			data.append( "image", snapshot )
			
			fetch("process.php",
			{
			    method: "POST",
			    body: data
			})
			.then(function(res){
				return res.json() 
			})
			.then(function(datas){
				
				//var mask = document.querySelector('.mask')
				//mask.style.backgroundColor = datas.color
				//mask.classList.remove('hide')
				
				image.classList.add('active');
				image.src = datas.filename;

			})
			
		} else {
			countdown.innerHTML = triggerTime--;
		}
		
	}, 1000);
		
})