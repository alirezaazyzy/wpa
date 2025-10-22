<?php
//if WPAUTOMATIC_VERSION not defined exiit
if( !defined('WPAUTOMATIC_VERSION') ){
    exit;
}?>
<div class="tempArticles">[ad_1]
[matched_content]
[ad_2]
<br><a href="[source_link]">Source</a> by <a href="[author_link]">[author_name]</a></div>		

<div class="tempgpt3">[ad_1]
[matched_content]
[ad_2]</div>	

<div class="tempArticlesBase">[ad_1]
[matched_content]
[ad_2]
<br><a href="[source_link]">Source</a> by <a href="[author_link]">[author_name]</a></div>		

<div class="tempWalmart">[item_imgs_html]
Price: <span style="color:#b12704">[price_with_discount]</span>
<br><a href="[product_affiliate_url]"><img data-src="https://i.imgur.com/SUv4PIl.png"></a> 
[ad_1]
[item_description]
<br>[ad_2]</div>		

<!-- FB template -->
<div class="tempFacebook tempFacebookposts tempFacebookfeed tempFacebooktagged">[ad_1]
[matched_content]
[ad_2]
<br><a href="[source_link]">Source</a>  </div>		

<div class="tempFacebookevents">[ad_1]
[matched_content]

Starts at: [start_time]
Ends at: [end_time]

Location:

[place_name]
[place_address]

[place_map]

[ad_2]
<br><a href="[source_link]">Source</a> </div>		


<div class="tempFeeds">[ad_1]
<br>[matched_content]
<br>[ad_2]
<br><a href="[source_link]">Source link </a></div>

<div class="tempMulti">[ad_1]
<br>[matched_content]
<br>[ad_2]
<br><a href="[source_link]">Source link </a></div>

<div class="tempBingNews">[ad_1]
<br>[matched_content]
<br>[ad_2]
<br><a href="[source_link]">Source link </a></div>

<div class="tempGoogleNews">[ad_1]
<br>[matched_content]
<br>[ad_2]
<br><a href="[source_link]">Source link </a></div>

<div class="tempGoogleTrends">[ad_1]
<br>[matched_content]
<br>[ad_2]
<br><a href="[source_link]">Source link </a></div>

<div class="tempMedium">[item_description]
<br><a href="[source_link]">Source link </a></div>

			<!-- amazon template -->	
			<div class="tempAmazon">[product_imgs_html]
Price: <span style="color:#b12704">[price_with_discount]</span><br><i><small>(as of [price_update_date] - <span class="wp_automatic_amazon_disclaimer" title="Product prices and availability are accurate as of the date/time indicated and are subject to change. Any price and availability information displayed on [relevant Amazon Site(s), as applicable] at the time of purchase will apply to the purchase of this product.">Details</span>)</small></i><br><br><a href="[product_link]"><img data-src="https://valvepress.s3.amazonaws.com/imgs/buy_now.png"></a> 
[ad_1]
[product_desc]
[product_summary]
<br>[ad_2]</div> 


		<!-- Clickbank template -->
		<div class="tempClickbank"><p style="text-align:center">[product_img]</p>

<p>
<strong>Product Name:</strong> [original_title]
</p>
[ad_1]
<p style="text-align: center; font-size: 150%;"><strong><a href="[product_link]">Click here to get [original_title] at discounted price while it's still available...</a></strong></p>

<p style="text-align: center; ">
<a href="[product_link]"><img style="display:inline" data-src="https://valvepress.s3.amazonaws.com/imgs/order_now.jpeg"></a></p>

<p style="text-align: center; ">
<em>All orders are protected by SSL encryption – the highest industry standard for online security from trusted vendors.<br>
<img data-src="https://valvepress.s3.amazonaws.com/imgs/money_back_gurantee.png"><br>
[original_title] is backed with a 60 Day No Questions Asked Money Back Guarantee. If within the first 60 days of receipt you are not satisfied with Wake Up Lean™, you can request a refund by sending an email to the address given inside the product and we will immediately refund your entire purchase price, with no questions asked.</em></p>

<!--more-->

<p>
<strong>Description:</strong> [product_desc]
</p>

[ad_2] 
 
<p style="text-align: center; font-size: 150%;"><strong><a href="[product_link]">Click here to get [original_title] at discounted price while it's still available...</a></strong></p>

<p style="text-align: center; ">
<a href="[product_link]"><img style="display:inline" data-src="https://valvepress.s3.amazonaws.com/imgs/order_now.jpeg"></a></p>

<p style="text-align: center; ">
<em>All orders are protected by SSL encryption – the highest industry standard for online security from trusted vendors.<br>
<img data-src="https://valvepress.s3.amazonaws.com/imgs/money_back_gurantee.png"><br>
[original_title] is backed with a 60 Day No Questions Asked Money Back Guarantee. If within the first 60 days of receipt you are not satisfied with Wake Up Lean™, you can request a refund by sending an email to the address given inside the product and we will immediately refund your entire purchase price, with no questions asked.</em></p>
</div>			
			
			<!-- Pinterest template -->
			<div class="tempPinterest">[ad_1]
<a href="[pin_url]"><img data-src="[pin_img]" title="[pin_title]" /></a>
<p>[pin_description]</p>
[ad_2]
<br><a href="[pin_url]">Source</a> by <a href="https://pinterest.com/[pin_pinner_username]">[pin_pinner_username]</a>
			
			</div>
			
			<!-- TikTok template -->
			<div class="tempTikTok">[ad_1]
[item_description]
[embed][item_url][/embed]
[ad_2]
<br><a href="[item_url]">Tiktok </a> by <a href="[item_user_link]">[item_user_name]</a>
			</div>
			
		<!-- Spintax template -->
		<div class="tempSpintax"></div>	
		
		<?php 

		$player= "[vid_player]
<br>";
		
		$vmplayer= "[vid_embed]
<br>";
		$dmplayer="[vid_player]
<br>";
		
		 if(  1   ){
			
			if( (defined('PARENT_THEME') &&  (PARENT_THEME =='truemag' || PARENT_THEME =='newstube'))  || class_exists('Cactus_video') ){
				$player ='';
				$vmplayer = '';	
				$dmplayer = '' ;
			}

		 	
		 } 
		 
		 //newspaper integration
		 if(function_exists('td_bbp_change_avatar_size')){
		 	
		 	
		 	
		 	if(! in_array( 'OPT_NO_NEWSPAPER' , $wp_automatic_options ) ){
			 	$player ='';
			 	$vmplayer = '';
		 	}
		 }
		
		?>
		
		<!-- youtube part -->
		<div class="tempYoutube"><?php   echo $player ?>[vid_desc]
<br><a href="[source_link]">source</a></div>

<!-- Rumble.com part -->
<div class="tempRumble">[item_embed_iframe]
<br>[item_description]
<br><a href="[source_link]">source</a></div>

<!-- Places part -->
<div class="tempPlaces">[item_photos_html]<br>
<strong>[item_title]</strong><br>
[item_formatted_address]<br>
Rating: [item_rating]<br>
Rated count: [item_user_ratings_total]<br>
[item_map_iframe]<br><br>
<a href="[item_url]">Check on Google Maps</a></div>

<!-- Reddit part -->
<div class="tempReddit">[ad_1]
[item_img_html]
<p>[item_description]</p>
[item_embed]
[ad_2]
<br><a href="[item_link]">View Reddit</a> by [item_author_link] -  <a href="[item_url]">View Source</a></div>

<!-- Telegram part -->
<div class="temptelegram">[ad_1]
[item_img_html]
<p>[item_description]</p>
[item_embed]
[ad_2]
<a href="[item_url]">View Source</a></div>

<!-- Careerjet part -->
<div class="tempCareerjet">[item_logo_html]
<b>Job title:</b> [item_title]
<br><b>Company:</b> [item_company]
<br><b>Job description</b>: [item_description]
<br><b>Expected salary</b>: [item_salary]
<br><b>Location</b>: [item_locations]
<br><b>Job date</b>: [item_date]
<br><a href="[item_url]"><b>Apply for the job now!</b></a>

[ad_2]
</div>

		<!-- Instagram part -->
		<div class="tempInstagram">[ad_1]
<a href="[item_url]">[item_images]</a>
<p>[item_description]</p>
[ad_2]
<br><a href="[item_url]">Source</a></div>
	
	<!-- craigslist part -->
		<div class="tempCraigslist">[item_imgs_html]
<p>[item_price]</p>
<p>[item_hood]</p>
<p>[item_address]</p>
<p>[item_description]</p>
<p>[item_attributes]</p>
<p>[item_map]</p>
<a href="[item_link]">Check more...</a>
</div>

<!-- craigslist part -->
		<div class="tempAliexpress">[item_imgs_html]
Price: <span style="color:#b12704">[price_with_discount]</span>

<a href="[item_affiliate_url]"><img src="https://valvepress.s3.amazonaws.com/imgs/buy_now.png"></a> 

[item_description]</div>
	
	<!-- SoundCloud part -->
	<div class="tempSoundCloud" >[ad_1]
[item_embed]
<br>[item_description]
[ad_2]
<br><a href="[source_link]">Source</a> by <a href="[item_user_link]">[item_user_username]</a></div>

		<!-- vimeo part -->
		<div class="tempVimeo"><?php   echo $vmplayer ?>[vid_description]
<br>Likes: [vid_likes]
<br>Viewed: [vid_views]
<br><a href="[source_link]">source</a></div>


<!-- Twitter template -->
<div class="tempTwitter">[ad_1]
[item_description]
[ad_2]
<br><a href="[source_link]">Source</a> by <a href="[item_author_url]">[item_author_name]</a></div>


		<div class="tempFlicker"><img data-src="[img_src]" alt="[img_title]" />    
<p>[img_description] </p>
<p><a href="[img_link]">Posted</a> by <a href="http://flicker.com/[img_author] " >[img_author_name] </a> on [img_date_posted] </p>
  <p>  Tagged: [img_tags] </p></div>

  
  <div class="tempeBay">[item_images] 
<br> [item_desc]
<br> Price : [price_with_discount]
<br> Ends on : [readable_time][item_end_date][/readable_time]
<br> <a href="[item_link]">View on eBay </a></div>


<!-- Itunes template -->
		<div class="tempItunesmusic"><img data-src="[item_img]">

<p>[embed][item_previewUrl][/embed]</p>
<br>
<p>By <a href="[item_artistViewUrl]">[item_artistName]</a></p>
<br><a href="[item_link]&at=[affiliate_id]">Download now from Itunes</a></div>


<div class="tempItunesmovie"><img data-src="[item_img]">
<p>[video src="[item_previewUrl]"]</p>
<br>
<p>[item_description]</p>
<p>By [item_artistName]</p>
<br><a href="[item_link]&at=[affiliate_id]">Download movie from Itunes</a></div>

<div class="tempItunesshortFilm"><img data-src="[item_img]">
<p>[video src="[item_previewUrl]"]</p>
<br>
<p>[item_description]</p>

<p>By [item_artistName]</p>
<br><a href="[item_link]&at=[affiliate_id]">Download movie from Itunes</a></div>

<div class="tempItunestvShow"><img data-src="[item_img]">
<p>[video src="[item_previewUrl]"]</p>
<br>
<p>[item_description]</p>

<p>By [item_artistName]</p>
<br><a href="[item_link]&at=[affiliate_id]">Download from Itunes</a></div>


<div class="tempItunespodcast"><img data-src="[item_img]">

<p>[item_description]</p>

<p>By [item_artistName]</p>

<br><a href="[item_link]&at=[affiliate_id]">Download from Itunes</a></div>


<div class="tempItunesmusicVideo"><img data-src="[item_img]">

<p>[video src="[item_previewUrl]"]</p>
<br>
<p>[item_description]</p>

<p>By [item_artistName]</p>

<br><a href="[item_link]&at=[affiliate_id]">Download from Itunes</a></div>


<div class="tempItunesaudiobook"><img data-src="[item_img]">

<p>[item_description]</p>

<p>By [item_artistName]</p>

<br><a href="[item_link]&at=[affiliate_id]">Download from Itunes</a></div>

<div class="tempItunesebook">
<img data-src="[item_img]">

<p>[item_description]</p>

<p>By [item_artistName]</p>
<br><a href="[item_link]&at=[affiliate_id]">Download from Itunes</a></div>


<div class="tempItunessoftware">
<img data-src="[item_img]">

<p>[item_description]</p>

[item_screenshot]

<p>By <a href="[item_artistViewUrl]">[item_artistName]</a></p>
<br><a href="[item_link]&at=[affiliate_id]">Download from Itunes</a></div>

<!-- Envato template -->

<div class="tempEnvatophotodune tempEnvatocodecanyon tempEnvatothemeforest tempEnvato3docean tempEnvatophotodune tempEnvatographicriver">[ad_1]

<a class="wp_automatic_demo_btn" target="_blank" href="[live_site_affiliate]">LIVE PREVIEW</a><a class="wp_automatic_buy_btn" target="_blank" href="[item_link_affiliate]">BUY FOR $[item_price]</a>

<img data-src="[preview_img]">
[item_description]
[ad_2]
<a href="[item_link_affiliate]">Source</a></div>
<div class="tempEnvatoaudiojungle">[ad_1]
[embed][preview_mp3][/embed]
<p>
<a class="wp_automatic_buy_btn" target="_blank" href="[item_link_affiliate]">BUY FOR $[item_price]</a>
<img  class="alignleft" data-src="[preview_icon]">[item_description]</p>
[ad_2]
<a href="[item_link_affiliate]">Source</a></div>
<div class="tempEnvatovideohive">[ad_1]
<img data-src="[preview_img]">
[embed][preview_vid][/embed]
[item_description]
[ad_2]
<a href="[item_link_affiliate]">Source</a></div>

<!-- DailyMotion template -->

<div class="tempDailyMotion"><?php   echo $dmplayer ?>[item_description]
<br><a href="[source_link]">View at DailyMotion</a></div>

<div class="tempSingle">[matched_content]</div>
