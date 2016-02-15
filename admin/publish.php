<?php
//Import the required classes
use TimelineAPI\Pin;
use TimelineAPI\PinLayout;
use TimelineAPI\PinLayoutType;
use TimelineAPI\PinIcon;
use TimelineAPI\PinReminder;
use TimelineAPI\Timeline;

add_action('publish_post', 'xyz_ptp_link_publish');
add_action('publish_page', 'xyz_ptp_link_publish');

$xyz_ptp_future_to_publish=get_option('xyz_ptp_future_to_publish');

if($xyz_ptp_future_to_publish==1)
	add_action('future_to_publish', 'xyz_link_ptp_future_to_publish');

function xyz_link_ptp_future_to_publish($post){
	$postid =$post->ID;
	xyz_ptp_link_publish($postid);
}



$xyz_ptp_include_customposttypes=get_option('xyz_ptp_include_customposttypes');
$carr=explode(',', $xyz_ptp_include_customposttypes);
foreach ($carr  as $cstyps ) {
	add_action('publish_'.$cstyps, 'xyz_ptp_link_publish');

}

function xyz_ptp_link_publish($post_ID) {
	$_POST_CPY=$_POST;
	$_POST=stripslashes_deep($_POST);

	$post_permissin=get_option('xyz_ptp_post_permission');
	if(isset($_POST['xyz_ptp_post_permission']))
		$post_permissin=$_POST['xyz_ptp_post_permission'];

	if ($post_permissin != 1) {
		$_POST=$_POST_CPY;
		return ;

	} else if (isset($_POST['_inline_edit'])  AND (get_option('xyz_ptp_default_selection_edit') == 0) ) {
		$_POST=$_POST_CPY;
		return;
	}

	$get_post_meta=get_post_meta($post_ID,"xyz_ptp",true);
	if($get_post_meta!=1)
		add_post_meta($post_ID, "xyz_ptp", "1");

	global $current_user;
	get_currentuserinfo();

	////////////fb///////////
	$appid=get_option('xyz_ptp_application_id');
	$appsecret=get_option('xyz_ptp_application_secret');
	$useracces_token=get_option('xyz_ptp_fb_token');


	$message=get_option('xyz_ptp_message');
	if(isset($_POST['xyz_ptp_message']))
		$message=$_POST['xyz_ptp_message'];

	$fbid=get_option('xyz_ptp_fb_id');

	$posting_method=get_option('xyz_ptp_po_method');
	if(isset($_POST['xyz_ptp_po_method']))
		$posting_method=$_POST['xyz_ptp_po_method'];


	$af=get_option('xyz_ptp_af');
	$apikey=get_option('xyz_ptp_pebble_apikey');
	$topic = get_option('xyz_ptp_pebble_topic');
	$timelineTime = get_option('xyz_ptp_pebble_time');

	$postpp= get_post($post_ID);global $wpdb;
	$entries0 = $wpdb->get_results( 'SELECT user_nicename FROM '.$wpdb->prefix.'users WHERE ID='.$postpp->post_author);

	foreach( $entries0 as $entry ) {
	$user_nicename=$entry->user_nicename;}

	if ($postpp->post_status == 'publish')
	{
		$posttype=$postpp->post_type;
		$fb_publish_status=array();

		if ($posttype=="page")
		{

			$xyz_ptp_include_pages=get_option('xyz_ptp_include_pages');
			if($xyz_ptp_include_pages==0)
			{
				$_POST=$_POST_CPY;
				return;
			}
		}

		if($posttype=="post")
		{
			$xyz_ptp_include_posts=get_option('xyz_ptp_include_posts');
			if($xyz_ptp_include_posts==0)
			{
				$_POST=$_POST_CPY;return;
			}

			$xyz_ptp_include_categories=get_option('xyz_ptp_include_categories');
			if($xyz_ptp_include_categories!="All")
			{
				$carr1=explode(',', $xyz_ptp_include_categories);

				$defaults = array('fields' => 'ids');
				$carr2=wp_get_post_categories( $post_ID, $defaults );
				$retflag=1;
				foreach ($carr2 as $key=>$catg_ids)
				{
					if(in_array($catg_ids, $carr1))
						$retflag=0;
				}


				if($retflag==1)
				{
					$_POST=$_POST_CPY;
					return;
				}
			}
		}

		include_once ABSPATH.'wp-admin/includes/plugin.php';

		$pluginName = 'bitly/bitly.php';

		if (is_plugin_active($pluginName)) {
			remove_all_filters('post_link');
		}
		$link = get_permalink($postpp->ID);


		$xyz_ptp_apply_filters=get_option('xyz_ptp_apply_filters');
		$ar2=explode(",",$xyz_ptp_apply_filters);
		$con_flag=$exc_flag=$tit_flag=0;
		if(isset($ar2[0]))
			if($ar2[0]==1) $con_flag=1;
		if(isset($ar2[1]))
			if($ar2[1]==2) $exc_flag=1;
		if(isset($ar2[2]))
			if($ar2[2]==3) $tit_flag=1;

		$content = $postpp->post_content;
		if($con_flag==1)
			$content = apply_filters('the_content', $content);
		$excerpt = $postpp->post_excerpt;
		if($exc_flag==1)
			$excerpt = apply_filters('the_excerpt', $excerpt);

		$content = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $content);
		$excerpt = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $excerpt);

		if($excerpt=="")
		{
			if($content!="")
			{
				$content1=$content;
				$content1=strip_tags($content1);
				$content1=strip_shortcodes($content1);

				$excerpt=implode(' ', array_slice(explode(' ', $content1), 0, 50));
			}
		}
		else
		{
			$excerpt=strip_tags($excerpt);
			$excerpt=strip_shortcodes($excerpt);
		}
		$description = $content;

		$description_org=$description;

		$attachmenturl=xyz_ptp_getimage($post_ID, $postpp->post_content);
		if($attachmenturl!="")
			$image_found=1;
		else
			$image_found=0;

		$name = $postpp->post_title;
		$caption = html_entity_decode(get_bloginfo('title'), ENT_QUOTES, get_bloginfo('charset'));
		if($tit_flag==1)
			$name = apply_filters('the_title', $name);


		$name=strip_tags($name);
		$name=strip_shortcodes($name);

		$description=strip_tags($description);
		$description=strip_shortcodes($description);

	   	$description=str_replace("&nbsp;","",$description);

		$excerpt=str_replace("&nbsp;","",$excerpt);

		if($apikey!="" && $topic!="" && $post_permissin==1)
		{
			$description_li=xyz_ptp_string_limit($description, 600);

			$user_page_id=get_option('xyz_ptp_fb_numericid');

			$xyz_ptp_pages_ids=get_option('xyz_ptp_pages_ids');
			if($xyz_ptp_pages_ids=="")
				$xyz_ptp_pages_ids=-1;

			$xyz_ptp_pages_ids1=explode(",",$xyz_ptp_pages_ids);


			foreach ($xyz_ptp_pages_ids1 as $key=>$value)
			{
				if($value!=-1)
				{
					$value1=explode("-",$value);
					$acces_token=$value1[1];$page_id=$value1[0];
				}
				else
				{
					$acces_token=$useracces_token;$page_id=$user_page_id;
				}

				//$fb=new FBAPFacebook(array(
				//		'appId'  => $acces_token,
				//		'secret' => $appsecret,
				//		'cookie' => true
				//));
				$message1=str_replace('{POST_TITLE}', $name, $message);
				$message2=str_replace('{BLOG_TITLE}', $caption,$message1);
				$message3=str_replace('{PERMALINK}', $link, $message2);
				$message4=str_replace('{POST_EXCERPT}', $excerpt, $message3);
				$message5=str_replace('{POST_CONTENT}', $description, $message4);
				$message5=str_replace('{USER_NICENAME}', $user_nicename, $message5);

				$message5=str_replace("&nbsp;","",$message5);

               $disp_type="feed";
				if($posting_method==1) //attach
				{
					$attachment = array('message' => $message5,
							'access_token' => $acces_token,
							'link' => $link,
							'name' => $name,
							'caption' => $caption,
							'description' => $description_li,
							'actions' => array(array('name' => $name,
									'link' => $link)),
							'picture' => $attachmenturl

					);
				}
				else if($posting_method==2)  //share link
				{
					$attachment = array('message' => $message5,
							'access_token' => $acces_token,
							'link' => $link,
							'name' => $name,
							'caption' => $caption,
							'description' => $description_li,
							'picture' => $attachmenturl


					);
				}
				else if($posting_method==3) //simple text message
				{

//					$attachment = array('message' => $message5,
//							'access_token' => $acces_token
//					);
					//Create some layouts which our pin will use
					$reminderlayout = new PinLayout(PinLayoutType::GENERIC_REMINDER, $name, null, null, $description_li, PinIcon::REACHED_FITNESS_GOAL);
					$pinlayout = new PinLayout(PinLayoutType::GENERIC_PIN, $name, null, null, $description_li, PinIcon::REACHED_FITNESS_GOAL);
					$date = new DateTime(date( 'Y-m-d H:i:s', current_time( 'timestamp', 0 )), new DateTimeZone(get_option('timezone_string')));
					// error_log('b4date: '.  $date -> format('Y-m-d-H-i-s') . "\n");
					$date -> setTimezone(new DateTimeZone('UTC'));
					$date -> add(new DateInterval('PT5M'));
					// error_log('date: '.  $date -> format('Y-m-d-H-i-s') . "\n");

					//Create a reminder which our pin will push before the event
					$reminder = new PinReminder($reminderlayout, $date) ;

					//Create the pin
					$date = new DateTime(date( 'Y-m-d H:i:s', current_time( 'timestamp', 0 )), new DateTimeZone(get_option('timezone_string')));
					$date -> setTime(intval($timelineTime),0);
					$date -> add(new DateInterval('P1D'));
//					error_log('b4date: '.  $date -> format('Y-m-d-H-i-s') . "\n");
					$date -> setTimezone(new DateTimeZone('UTC'));
					// error_log('date: '.  $date -> format('Y-m-d-H-i-s') . "\n");
					$pin = new Pin('PTP'.$post_ID, $date, $pinlayout);

					//Attach the reminder
					$pin -> addReminder($reminder);

				}
				else if($posting_method==4 || $posting_method==5) //text message with image 4 - app album, 5-timeline
				{
					if($attachmenturl!="")
					{

						if($posting_method==5)
						{
							try{
							$albums = $fb->api("/$page_id/albums", "get", array('access_token'  => $acces_token));
							}
							catch(Exception $e)
							{
								$fb_publish_status[$page_id."/albums"]=$e->getMessage();
							}
							foreach ($albums["data"] as $album) {
								if ($album["type"] == "wall") {
									$timeline_album = $album; break;
								}
							}
							if (isset($timeline_album) && isset($timeline_album["id"])) $page_id = $timeline_album["id"];
						}


						$disp_type="photos";
						$attachment = array('message' => $message5,
								'access_token' => $acces_token,
								'url' => $attachmenturl

						);
					}
					else
					{
						$attachment = array('message' => $message5,
								'access_token' => $acces_token

						);
					}

				}
				try{
				//$result = $fb->api('/'.$page_id.'/'.$disp_type.'/', 'post', $attachment);}
				  // error_log('prior to api call: '. $post_ID .' '.$apikey. ' ' . $topic);
				  $result = Timeline::pushSharedPin($apikey, array($topic), $pin);
					// error_log('var dump: '. print_r( $result,TRUE));
					if ($result['status']['code']!='100') {
						$fb_publish_status[$page_id."/".$disp_type]=$result['status']['message'].';'.$result['result']['errorDetails'][0]['message'];
					}
			  }
					catch(Exception $e)
							{
								$fb_publish_status[$page_id."/".$disp_type]=$e->getMessage();
							}
			}


			if(count($fb_publish_status)>0)
				$fb_publish_status_insert=serialize($fb_publish_status);
			else
				$fb_publish_status_insert=1;

			$time=time();
			$post_fb_options=array(
					'postid'	=>	$post_ID,
					'acc_type'	=>	"Facebook",
					'publishtime'	=>	$time,
					'status'	=>	$fb_publish_status_insert
			);

			$update_opt_array=array();

			$arr_retrive=(get_option('xyz_ptp_post_logs'));

			$update_opt_array[0]=isset($arr_retrive[0]) ? $arr_retrive[0] : '';
			$update_opt_array[1]=isset($arr_retrive[1]) ? $arr_retrive[1] : '';
			$update_opt_array[2]=isset($arr_retrive[2]) ? $arr_retrive[2] : '';
			$update_opt_array[3]=isset($arr_retrive[3]) ? $arr_retrive[3] : '';
			$update_opt_array[4]=isset($arr_retrive[4]) ? $arr_retrive[4] : '';

			array_shift($update_opt_array);
			array_push($update_opt_array,$post_fb_options);
			update_option('xyz_ptp_post_logs', $update_opt_array);


		}

	}

	$_POST=$_POST_CPY;
}

?>
