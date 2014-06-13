var imgCounter = 0;
function runCarousel(){
	var done = 0;
	var img_len = $(".jcarousel ul li img").length;
	
	$(".jcarousel ul li img").each(function(){
		if($(this).css('display')=='block' && done==0){
			imgCounter++;
			if(imgCounter == img_len){
				$(this).css('display','none');
				imgCounter = 0;
				$('.jcarousel ul li img').css('display','none');
				$('.jcarousel ul li').find('img:eq(0)').css('display','block');
			} else {
				$(this).css('display','none');
				$(this).parent().next().find('img').css('display','block');
			}
			done = 1;
		}
	});
	setTimeout(runCarousel,5000);
}
function manualCarousel(o){
	var img_len = $(".jcarousel ul li img").length;
	if(o=='prev'){
		if(imgCounter==0){
			$(".jcarousel ul li img:eq("+((imgCounter))+")").css('display','none');
			imgCounter = img_len-1;
			$(".jcarousel ul li img:eq("+(imgCounter)+")").css('display','block');
		} else {
			$(".jcarousel ul li img:eq("+(imgCounter)+")").css('display','none');
			imgCounter -= 1;
			$(".jcarousel ul li img:eq("+imgCounter+")").css('display','block');
		}
	} else if(o=='next'){
		if(imgCounter==(img_len-1)){
			$(".jcarousel ul li img:eq("+((imgCounter))+")").css('display','none');
			imgCounter = 0;
			$(".jcarousel ul li img:eq("+(imgCounter)+")").css('display','block');
		} else {
			$(".jcarousel ul li img:eq("+(imgCounter)+")").css('display','none');
			imgCounter += 1;
			$(".jcarousel ul li img:eq("+imgCounter+")").css('display','block');
		}
	}
}
runCarousel();