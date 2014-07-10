$(function(){
	setTimeout(function(){
	$('.frame_alert').fadeOut("slow");	
	},2000);

	var key_words = $('#data-keywords').data('keywords');
	
	$('.movie_frame dd').each(function(){
		var dd = $(this).text();
		for(var k in key_words){
			var keyword = key_words[k];
			keyword = keyword.replace(/(\s+)/,"(<[^>]+>)*$1(<[^>]+>)*");
			var pattern = new RegExp("("+keyword+")", "gi");
			dd = dd.replace(pattern, "<mark>$1</mark>");
			dd = dd.replace(/(<mark>[^<>]*)((<[^>]+>)+)([^<>]*<\/mark>)/,"$1</mark>$2<mark>$4");
		}
		$(this).html(dd);
	});
});