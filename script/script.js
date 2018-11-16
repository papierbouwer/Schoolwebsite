$(document).ready(function(){
	$('.recent_msg').hide(); 
	$('.recent_msg:eq(0)').fadeIn(0);
	if($(window).width() < 820) $('.inlog').before($('.recent'));
	main();
});

function main(){
	var klik = false;
	var currentSheet = 0;
	$('#vorige').click(function(){
		$('.recent_msg:eq(' + currentSheet + ')').fadeOut();
		if(currentSheet == 0){
			currentSheet = 4;
		} else{
			currentSheet -= 1;
		}
		$('.recent_msg:eq(' + currentSheet + ')').delay(700).fadeIn();
	});
	$('#volgende').click(function(){
		$('.recent_msg:eq(' + currentSheet + ')').fadeOut();
		if(currentSheet == 4){
			currentSheet = 0;
		} else{
			currentSheet += 1;
		}
		$('.recent_msg:eq(' + currentSheet + ')').delay(700).fadeIn();
	});
	$('.handle').click(function(){
		if(klik) klik = false; else klik = true;
		if(klik){
			$('nav').css({"height": "250px"});
			//$('nav ul li').fadeIn();
		} else{
			$('nav').css({"height": "50px"});
			//$('nav ul li').fadeIn();
		}
	});
	$(window).resize(function(){
		if($(window).width() > 820){ 
			$('nav').css({"height": "50px"})
			$('.recent').before($('.inlog'));
		} else{
			$('.inlog').before($('.recent'));			
		}
	});
}