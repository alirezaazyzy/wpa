jQuery(document).ready(function(){
	
	var attrName = 'data-a-src' ;
	if(jQuery('.wp_automatic_gallery').attr('data-a-src') == undefined) attrName = 'src'; 
	
	var wp_automatic_main_scr = jQuery('.wp_automatic_gallery:first-child').attr(attrName);
	 
	jQuery('.wp_automatic_gallery:first-child').before('<div class="wp_automatic_gallery_wrap"><div style="background-image:url(\'' + wp_automatic_main_scr +'\')" class="wp_automatic_gallery_main" ></div><div class="clear"></div></div>');
	
	
	jQuery('.wp_automatic_gallery').each(function(){
		
		jQuery('.wp_automatic_gallery_wrap').append('<div class="wp_automatic_gallery_btn" style="background-image:url(\''+ jQuery(this).attr( attrName ) + '\')" ></div>');
		jQuery(this).remove();
	});
	
	//append inside the wp_automatic_gallery_wrap at the end a clear div and a br
	jQuery('.wp_automatic_gallery_wrap').append('<div style="clear:both"></div><br>');

	//remove duplicates by looping every wp_automatic_gallery_wrap and remove the duplicate wp_automatic_gallery_btn
	jQuery('.wp_automatic_gallery_wrap').each(function(){
		var seen = {};
		jQuery(this).find('.wp_automatic_gallery_btn').each(function() {
			var bg = jQuery(this).css('background-image');
			if (seen[bg]) {
				jQuery(this).remove();
			} else {
				seen[bg] = true;
			}
		});
	});

	//on click of any wp_automatic_gallery_btn change the background image of wp_automatic_gallery_main to the clicked one
	jQuery('.wp_automatic_gallery_btn').click(function(){
	  
	  jQuery('.wp_automatic_gallery_main').css('background-image', jQuery(this).css('background-image')  );
	  
	});

	
});