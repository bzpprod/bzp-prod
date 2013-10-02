<?php 	
	// ###### DESCRIPTION/KEY
	if(isset($meta_description)){ echo '<meta name="description" content="'.$meta_description.'" />'; }
	if(isset($meta_keywords)){ echo '<meta name="keywords" content="'.$meta_keywords.'" />'; }
	
	// ###### FACEBOOK METAS
	echo '<meta property="fb:app_id" content="'.Configure::read('Facebook.AppId').'" />';
	if(isset($fbmeta_title)){ echo '<meta property="og:title" content="'.$fbmeta_title.'" />'; }
	if(isset($fbmeta_type)){ echo '<meta property="og:type" content="'.(!empty($fbmeta_type) ? $fbmeta_type : 'website').'" />'; }
	if(isset($fbmeta_url)){ echo '<meta property="og:url" content="'.$fbmeta_url.'" />'; }
	if(isset($fbmeta_image)){ echo '<meta property="og:image" content="'.$fbmeta_image.'" />'; }
	if(isset($fbmeta_image_type)){ echo '<meta property="og:image:type" content="'.$fbmeta_image_type.'" />'; }
	if(isset($fbmeta_image_width)){ echo '<meta property="og:image:width" content="'.$fbmeta_image_width.'" />'; }
	if(isset($fbmeta_image_height)){ echo '<meta property="og:image:height" content="'.$fbmeta_image_height.'" />'; }
	if(isset($fbmeta_description)){ echo '<meta property="og:description" content="'.$fbmeta_description.'" />'; }

	if(isset($fbmeta_price)){ echo '<meta property="'.Configure::read('Facebook.Namespace').':price" content="'.$fbmeta_price.'" />'; }
	if(isset($fbmeta_condition)){ echo '<meta property="'.Configure::read('Facebook.Namespace').':condition" content="'.$fbmeta_condition.'" />'; }	