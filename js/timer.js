function getTimeRemaining(endtime){
	var t = Date.parse(endtime) - Date.parse(new Date());
	var seconds = Math.floor( (t/1000) % 60 );
	var minutes = Math.floor( (t/1000/60) % 60 );
	var hours = Math.floor( (t/(1000*60*60)) % 24 );
	var days = Math.floor( t/(1000*60*60*24) );
	return {
		'total': t,
		'days': days,
		'hours': hours,
		'minutes': minutes,
		'seconds': seconds
		};
}

timers = document.getElementsByClassName("timer");


function initializeClock(){

	function updateClock(span){
	  
		var t = getTimeRemaining(new Date(parseInt(span.getAttribute("name"))));

		span.innerHTML = t.days + "J " + ('0' + t.hours).slice(-2) + "H " + ('0' + t.minutes).slice(-2) + "M " + ('0' + t.seconds).slice(-2) + "S";

		if(t.total<=0)
			span.innerHTML = "Adjugé vendu";
		
		
		}

	for(i=0;i<timers.length;i++)
		updateClock(timers[i]);

	setTimeout(function () {initializeClock();},1000);
	}

initializeClock();