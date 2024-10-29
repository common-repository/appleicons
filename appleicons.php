<?php
/*
Plugin Name: Apple Icons and Loading Screen
Plugin URI: http://developerextensions.com
Description: Simply add the Icons and loading screens for various versions of iPhone, iPad and IPod.
Version: 3.0.1
Author: surinder83singh@gmail.com, sunildhanda
Author URI: http://developerextensions.com
License: GPL2
*/
//for admin dashboard add_dashboard_page();

class deAppleIcons {
	var $browserName;
	var $nonceName = 'deAppleIcons';
	var $menuSlug	= 'deAppleIcons';
	function deAppleIcons(){
		if(is_admin()){
			add_action('admin_init', array($this, 'deAppleIconsRegisterSettings') );
			add_action('activate_appleicons/appleicons.php',array($this,'addDefaultIconImages')); 
			add_action('admin_menu', array($this, 'deAddToAdminMenu') );
			add_filter('plugin_action_links', array($this, 'pluginActionLinks'), 10, 2 );
			//add_action('admin_head-deAppleIcons', array($this, 'media_upload_styles'));
			add_action('admin_enqueue_styles', array($this, 'media_upload_styles'));
			add_action('admin_enqueue_scripts', array($this, 'media_upload_scripts'));
			//add_action('admin_print_scripts-deAppleIcons', array($this, 'media_upload_scripts'));
		}
		add_action('wp_head', array($this, 'deAppleIconReplaceText'));
	}
	function addDefaultIconImages(){
		$oldOptionsRef = array(
			'iphone_icon'=>'iphone_icon',
			'iphone2x_icon'=>'iphone_icon4',
			'ipad_icon'=>'ipad_icon',
			'ipad2x_icon'=>'ipad_icon2',
			'iphone_image'=>'iphone_image',
			'iphone2x_image'=>'iphone_image4',
			'iphone5_image'=>'iphone5_image',
			'ipad_image'=>'ipad_image',
			'ipad_land_image'=>'ipad_land_image',
			'ipad2x_image'=>'ipad_image2',
			'ipad2x_land_image'=>'ipad2x_land_image'
		);
		$options = array(
			'iphone_icon'=>'iphone_icon57x57.png',
			'iphone2x_icon'=>'iphone2x_icon114x114.png',
			'ipad_icon'=>'ipad_icon72x72.png',
			'ipad2x_icon'=>'ipad2x_icon144x144.png',
			'iphone_image'=>'iphone_image320x460.png',
			'iphone2x_image'=>'iphone2x_image640x920.png',
			'iphone5_image'=>'iphone5_image640x1096.png',
			'ipad_image'=>'ipad_image768x1004.png',
			'ipad_land_image'=>'ipad_land_image748x1024.png',
			'ipad2x_image'=>'ipad2x_image1536x2008.png',
			'ipad2x_land_image'=>'ipad2x_land_image1496x2048.png'
		);
		$alreadyInstall = get_option('iphone_icon');
		if($alreadyInstall){
			foreach($oldOptionsRef as $newOption=>$oldOption){
				$oldPath = get_option($oldOption);
				if($oldPath){
					$path = $oldPath;
				}else{
					$path = $this->getDynamicUrl('/wp-content/plugins/appleicons/images/icons/'.$options[$newOption]);
				}
				delete_option($oldOption);
				update_option($newOption, $path);
				
			}
			return;
		}
		
		
		foreach($options as $option=>$image){
			update_option($option, $this->getDynamicUrl('/wp-content/plugins/appleicons/images/icons/'.$image));
		}
	}
	function pluginActionLinks( $links, $file ) {
		//var_dump($file);
		if ( strpos($file, '/appleicons.php' )>0 ) {
			$links[] = '<a href="admin.php?page='.$this->menuSlug.'">'.__('Settings').'</a>';
		} 
		return $links;
	}

	function deAddToAdminMenu(){
		$this->pagehook = add_options_page('Apple Icon Settings', 'Apple Icons', 'administrator', $this->menuSlug, array($this, 'pluginPageContentCallback'));
	}
	function pluginsImageSrc($image_name){
		return plugins_url( 'images/'.$image_name , __FILE__ );
	}
	function pluginPageContentCallback(){
		wp_enqueue_script('common');
		wp_enqueue_script('wp-lists');
		wp_enqueue_script('postbox');
		wp_enqueue_script('thickbox');
		wp_enqueue_style('thickbox');
		wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
		wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false );
		
		add_meta_box( 'wpde_appleicons_icons', __( 'Icons' ), array($this, 'renderIcons'), $this->pagehook, 'normal', 'core' );
		add_meta_box( 'wpde_appleicons_screen', __( 'Starup Screens' ), array($this, 'renderStartupScreens'), $this->pagehook, 'normal', 'core' );
		add_meta_box( 'wpde_appleicons_helpdesk', __( 'Help Desk' ), array($this, 'renderhelpDesk'), $this->pagehook, 'side', 'core' );
		add_meta_box( 'wpde_appleicons_newsdesk', __( 'News Desk' ), array($this, 'renderNewsDesk'), $this->pagehook, 'side', 'core' );
		

		?>
		<style>
        #wpde_appleicons_helpdesk ul{list-style:circle inside;}
		#wpde_appleicons_newsdesk .des {padding:10px 10px 20px;}
		
		input[type="text"]{width:100%;display:block;margin-bottom:5px;}
		.wp-core-ui .upload_image_button, .wp-core-ui .upload_image_reset{line-height: 19px;height: auto;}
        th.vMiddle, td.vMiddle {
            vertical-align: middle;
        }
        .deIconContainer {
            position:relative;
        }
        table div.precomposed {
            display:none;
        }
        table.deApplePrecomposed div.precomposed {
            display:block;
            position:absolute;
            top:0px;
            left:0px;
         	background:url(<?php echo $this->pluginsImageSrc('glossy.png');?>);
            background-size:100% 100%;
            width:100%;
            height:100%;
        }
        div.deIconContainer img {
            border-radius:12px;
        }
        div.current_url {
            font-size:9px;
            color:#aaa;
        }
        </style>
        <div class="wrap">
        	<div class="update-nag">If this plugin fulfill your requirement, please give a <a href="http://wordpress.org/support/view/plugin-reviews/appleicons" target="_blank">review</a> or you have any query or suggestion, please share it <a href="http://tidduinfotech.com/" target="_blank">here</a>.</div>
            <?php screen_icon(); ?>
            <script>
                jQuery(document).ready( function($){
                    //close postboxes that should be closed
                    $('.if-js-closed').removeClass('if-js-closed').addClass('closed');
                    //postboxes setup
                    //console.dir(postboxes);
                    postboxes.add_postbox_toggles('<?php echo $this->pagehook;?>');
                });
            </script>
            <h2>Apple Icons and Loading Screens</h2>
            <form method="post" action="options.php" enctype="multipart/form-data">
            	<div id="poststuff">
                    <div id="post-body" class="metabox-holder columns-2">
                        <div id="post-body-content" class="postbox-container">
						<?php 
							do_meta_boxes($this->pagehook, 'normal', array());
							
                            settings_fields( 'de-apple-icon-group' );
                            do_settings_fields('page_url_slug', 'de-apple-icon-group' );
                            wp_nonce_field( plugin_basename( __FILE__ ), $this->nonceName );
                        ?>
                		</div>
						<div id="postbox-container-1" class="postbox-container">
						<?php do_meta_boxes($this->pagehook, 'side', array()); ?>
						</div>
                    </div>
                </div>
            </form>
        </div>
<?php
	}
	function renderhelpDesk(){
	
	?>
	<p>You can post your query on following sites. We will solve your issues free of cost.</p>
	<ul>
		<li>
			<a href="http://developerextensions.com" title="We provide free help to web developers" target="_blank" >http://developerextensions.com</a>
		</li>
		<li>
			<a href="https://www.facebook.com/developerextensions" title="Like us on facebook" target="_blank" >https://www.facebook.com/developerextensions</a>
		</li>
		<li>
			<a href="http://tiddu.com" title="We provide free help to web developers" target="_blank" >http://tiddu.com</a>
		</li>
		<li>
			<a href="http://sunilkumardhanda.me" title="We provide free help to web developers" target="_blank" >http://sunilkumardhanda.me</a>
		</li>
	</ul>
	<?php
	
	}
	function renderNewsDesk(){
		$rssUrl = 'http://developerextensions.com/news.rss';
		$data  = @file_get_contents($rssUrl);
		$items = array();
		if($data){
			$rss  		= @simplexml_load_string($data);
			if($rss && $rss->channel){
				$channel 	= (array)$rss->channel;
				$items   	= $channel['item'];
			}	
		}
	?>
	<ul>
		<?php 
		if(count($items)){
		foreach($items as $item){ ?>
		<li>
			<a href="<?php echo $item->link;?>" title="<?php echo $item->title;?>" target="_blank" ><?php echo $item->title; ?></a>
			<div class="des"><?php echo (string)$item->description; ?></div>
		</li>
		<?php } 
		
		}else { ?>
		<li>
			Checkout our other extensions at <a href="http://developerextensions.com" title="Our other extensions" target="_blank" >http://developerextensions.com</a>
		</li>
		<?php } ?>
	</ul>
	<?php
	
	}
	function renderIcons(){
		?>
        <table class="form-table <?php echo (get_option('precomposed'))?'':'deApplePrecomposed'; ?>">
            <tr valign="top">
                <th class="vMiddle" scope="row">Precomposed</th>
                <td class="vMiddle"><input type="checkbox" onclick="jQuery(this).parents('table').toggleClass('deApplePrecomposed');" name="precomposed" value="yes" <?php if(get_option('precomposed')){ echo 'checked';} ?>  /></td>
            </tr>
            <tr valign="top">
                <th scope="row" width="30%" class="de-title">iPhone and iPod touch</th>
                <th width="15%">(57x57)</th>
                <td width="10%">
					<div class="deIconContainer">
						<img width="57" height="57" src="<?php echo get_option('iphone_icon'); ?>" title="iPhone and iPod touch Icon (57x57)"/>
                        <div class="precomposed"></div>
                    </div>
				</td>
                <td width="45%">
                	<input id="iphone_icon" class="media_field" type="text" name="iphone_icon" value="<?php echo get_option('iphone_icon'); ?>" />
                    <div class="upload_buttons">
                        <span class="button upload_image_reset"><?php esc_html_e('Reset', $this->nonceName); ?></span>
                        <input class="button-primary upload_image_button" type="button" data-button_text="<?php esc_attr_e('Upload Image', $this->nonceName); ?>" value="<?php esc_attr_e('Upload Image', $this->nonceName); ?>" />
                    </div>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="de-title">iPhone and iPod touch<br> (2x)</th>
                <th>(114x114)</th>
                <td>
					<div class="deIconContainer">
        				<img width="57" height="57" src="<?php echo get_option('iphone2x_icon'); ?>" title="iPhone and iPod touch 2x Icon (114x114)"/>
                        <div class="precomposed"></div>
                   	</div>
				</td>
                <td>
                    <input id="iphone2x_icon" class="media_field" type="text" name="iphone2x_icon" value="<?php echo get_option('iphone2x_icon'); ?>" />
                    <div class="upload_buttons">
                        <span class="button upload_image_reset"><?php esc_html_e('Reset', $this->nonceName); ?></span>
                        <input class="button-primary upload_image_button" type="button" data-button_text="<?php esc_attr_e('Upload Image', $this->nonceName); ?>" value="<?php esc_attr_e('Upload Image', $this->nonceName); ?>" />
                    </div>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="de-title">iPad</th>
                <th>(72x72)</th>
                <td>
					<div class="deIconContainer">
        				<img width="57" height="57" src="<?php echo get_option('ipad_icon'); ?>" title="iPad Icon (72x72)"/>
                        <div class="precomposed"></div>
                   	</div>
				</td>
                <td>
                	<input id="ipad_icon" class="media_field" type="text" name="ipad_icon" value="<?php echo get_option('ipad_icon'); ?>" />
                    <div class="upload_buttons">
                        <span class="button upload_image_reset"><?php esc_html_e('Reset', $this->nonceName); ?></span>
                        <input class="button-primary upload_image_button" type="button" data-button_text="<?php esc_attr_e('Upload Image', $this->nonceName); ?>" value="<?php esc_attr_e('Upload Image', $this->nonceName); ?>" />
                    </div>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="de-title">iPad 2</th>
                <th>(144x144)</th>
                <td>
					<div class="deIconContainer">
        				<img width="57" height="57" src="<?php echo get_option('ipad2x_icon'); ?>" title="iPad2 Icon (144x144)"/>
                        <div class="precomposed"></div>
                   	</div>
				</td>
                <td>
                	<input id="ipad2x_icon" class="media_field" type="text" name="ipad2x_icon" value="<?php echo get_option('ipad2x_icon'); ?>" />
                    <div class="upload_buttons">
                        <span class="button upload_image_reset"><?php esc_html_e('Reset', $this->nonceName); ?></span>
                        <input class="button-primary upload_image_button" type="button" data-button_text="<?php esc_attr_e('Upload Image', $this->nonceName); ?>" value="<?php esc_attr_e('Upload Image', $this->nonceName); ?>" />
                    </div>
               	</td>
            </tr>
            <tr valign="top">
                <th scope="row" colspan="4">
					<?php submit_button(); ?>
                </th>
            </tr>
        </table>
		<?php
	}
	
	function renderStartupScreens(){
		?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row" width="30%" class="de-title"></th>
                <th width="15%">(320x460)</th>
                <td width="10%"><img width="80" src="<?php echo get_option('iphone_image'); ?>" title="iPhone and iPod touch StartUp Image (320x460)"/></td>
                <td width="45%">
                	<input id="iphone_image" class="media_field" type="text" name="iphone_image" value="<?php echo get_option('iphone_image'); ?>" />
                    <div class="upload_buttons">
                        <span class="button upload_image_reset"><?php esc_html_e('Reset', $this->nonceName); ?></span>
                        <input class="button-primary upload_image_button" type="button" data-button_text="<?php esc_attr_e('Upload Image', $this->nonceName); ?>" value="<?php esc_attr_e('Upload Image', $this->nonceName); ?>" />
                    </div>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="de-title">iPhone and iPod touch<br> (2x)</th>
                <th>(640x920)</th>
                <td><img width="80"  src="<?php echo get_option('iphone2x_image'); ?>" title="iPhone and iPod touch (2x) StartUp Image (640x920)"/></td>
                <td>
                	<input id="iphone2x_image" class="media_field" type="text" name="iphone2x_image" value="<?php echo get_option('iphone2x_image'); ?>" />
                    <div class="upload_buttons">
                        <span class="button upload_image_reset"><?php esc_html_e('Reset', $this->nonceName); ?></span>
                        <input class="button-primary upload_image_button" type="button" data-button_text="<?php esc_attr_e('Upload Image', $this->nonceName); ?>" value="<?php esc_attr_e('Upload Image', $this->nonceName); ?>" />
                    </div>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="de-title">iPhone 5 and iPod touch (5th generation)</th>
                <th>(640x1096 pixels)</th>
                <td><img width="80"  src="<?php echo get_option('iphone5_image'); ?>" title="iPhone 5 and iPod touch (5th generation) StartUp Image  (640x1096)"/></td>
                <td>
                	<input id="iphone5_image" class="media_field" type="text" name="iphone5_image" value="<?php echo get_option('iphone5_image'); ?>" />
                    <div class="upload_buttons">
                        <span class="button upload_image_reset"><?php esc_html_e('Reset', $this->nonceName); ?></span>
                        <input class="button-primary upload_image_button" type="button" data-button_text="<?php esc_attr_e('Upload Image', $this->nonceName); ?>" value="<?php esc_attr_e('Upload Image', $this->nonceName); ?>" />
                    </div>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="de-title">iPad</th>
                <th>(768x1004)</th>
                <td><img width="80"  src="<?php echo get_option('ipad_image'); ?>" title="iPad StartUp Image  (768x1004)"/></td>
                <td>
                	<input id="ipad_image" class="media_field" type="text" name="ipad_image" value="<?php echo get_option('ipad_image'); ?>" />
                    <div class="upload_buttons">
                        <span class="button upload_image_reset"><?php esc_html_e('Reset', $this->nonceName); ?></span>
                        <input class="button-primary upload_image_button" type="button" data-button_text="<?php esc_attr_e('Upload Image', $this->nonceName); ?>" value="<?php esc_attr_e('Upload Image', $this->nonceName); ?>" />
                    </div>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="de-title">iPad Landscape</th>
                <th>(748x1024)</th>
                <td><img width="80"  src="<?php echo get_option('ipad_land_image'); ?>" title="iPad Landscape StartUp Image  (748x1024)"/></td>
                <td>
                	<input id="ipad_land_image" class="media_field" type="text" name="ipad_land_image" value="<?php echo get_option('ipad_land_image'); ?>" />
                    <div class="upload_buttons">
                        <span class="button upload_image_reset"><?php esc_html_e('Reset', $this->nonceName); ?></span>
                        <input class="button-primary upload_image_button" type="button" data-button_text="<?php esc_attr_e('Upload Image', $this->nonceName); ?>" value="<?php esc_attr_e('Upload Image', $this->nonceName); ?>" />
                    </div>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="de-title">iPad 2</th>
                <th>(1536x2008)</th>
                <td><img width="80"  src="<?php echo get_option('ipad2x_image'); ?>" title="iPad 2 StartUp Image  (1536x2008)"/></td>
                <td>
                	<input id="ipad2x_image" class="media_field" type="text" name="ipad2x_image" value="<?php echo get_option('ipad2x_image'); ?>" />
                    <div class="upload_buttons">
                        <span class="button upload_image_reset"><?php esc_html_e('Reset', $this->nonceName); ?></span>
                        <input class="button-primary upload_image_button" type="button" data-button_text="<?php esc_attr_e('Upload Image', $this->nonceName); ?>" value="<?php esc_attr_e('Upload Image', $this->nonceName); ?>" />
                    </div>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="de-title">iPad 2 Landscape</th>
                <th>(1496x2048)</th>
                <td><img width="80"  src="<?php echo get_option('ipad2x_land_image'); ?>" title="iPad 2 Landscape StartUp Image (1496x2048)"/></td>
                <td>
                	<input id="ipad2x_land_image" class="media_field" type="text" name="ipad2x_land_image" value="<?php echo get_option('ipad2x_land_image'); ?>" />
                    <div class="upload_buttons">
                        <span class="button upload_image_reset"><?php esc_html_e('Reset', $this->nonceName); ?></span>
                        <input class="button-primary upload_image_button" type="button" data-button_text="<?php esc_attr_e('Upload Image', $this->nonceName); ?>" value="<?php esc_attr_e('Upload Image', $this->nonceName); ?>" />
                    </div>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" colspan="4">
					<?php submit_button(); ?>
                </th>
            </tr>
        </table>
        <?php
	}


	function deAppleIconReplaceText(){
		$this->getUserBrowser();
		$icons		= array(
			'iphone_icon'=>'57x57',
			'iphone2x_icon'=>'114x114',
			'ipad_icon'=>'72x72',
			'ipad2x_icon'=>'144x144'
		);
		$appleTouchIconPrecomposed 	= 'apple-touch-icon'.((get_option('precomposed')!='')?'-precomposed':'');
		if($this->browserName == "safari" || true){ ?>
<meta name="apple-mobile-web-app-capable" content="yes" />
<script type="text/javascript">
	var appleIcons_startUpImages = {
		"iphone":"<?php echo $this->getDynamicUrl(get_option('iphone_image'));?>",
		"iphone4":"<?php echo $this->getDynamicUrl(get_option('iphone2x_image'));?>",
		"iphone5":"<?php echo $this->getDynamicUrl(get_option('iphone5_image'));?>",
		"ipad":"<?php echo $this->getDynamicUrl(get_option('ipad_image'));?>",
		"ipad_land":"<?php echo $this->getDynamicUrl(get_option('ipad_land_image'));?>",
		"ipad2":"<?php echo $this->getDynamicUrl(get_option('ipad2x_image'));?>",
		"ipad2_land":"<?php echo $this->getDynamicUrl(get_option('ipad2x_land_image'));?>"
	};

	(function(){
		function AddLink(url, media){
			var link 	= document.createElement("link");
			link.rel	= "apple-touch-startup-image";
			link.href	= url;
			if(media){
				link.media	= media;
			}
			document.getElementsByTagName("head")[0].appendChild(link);
		}
		
		var image 				= false;
		var land_image			= false;
		var userAgent 			= navigator.userAgent;
		var devicePixelRatio 	= window.devicePixelRatio ? window.devicePixelRatio:0;
		if(userAgent.indexOf("iPhone")>-1){
			if( devicePixelRatio>1){	
				image = appleIcons_startUpImages["iphone4"];
				if(window.screen.height == 568){
					image = appleIcons_startUpImages["iphone5"];
				}
			}else{
				image = appleIcons_startUpImages["iphone"];
			}
		}else if(userAgent.indexOf("iPad")>-1){
			if(devicePixelRatio>1){	
				image 		= appleIcons_startUpImages["ipad2"];
				land_image 	= appleIcons_startUpImages["ipad2_land"];
			}else{
				image 		= appleIcons_startUpImages["ipad"];
				land_image 	= appleIcons_startUpImages["ipad_land"];
			}
		}
		if(image){
			AddLink(image, ((userAgent.indexOf("iPad")>-1) ? "(orientation: portrait)":false) );
		}
		if(land_image){
			AddLink(land_image, "(orientation: landscape)");
		}
	})();
</script>
<?php
			foreach ($icons as $name=>$size){
				$url = $this->getDynamicUrl(get_option($name));
				if($url){
?>
<link href="<?php echo $url;?>" rel="<?php echo $appleTouchIconPrecomposed;?>" sizes="<?php echo $size;?>" />
<?php 
				}
			 } 
		}
	}

	function validateSetting($name='11111'){
		$value = get_option($name);
		if ( !wp_verify_nonce($_POST[$this->nonceName], plugin_basename( __FILE__ )) ){
			return $value;
		}
		$image 	= $_POST[$name];//$_FILES[$name]; 
		if(preg_match('/png$/', $image)){
			$value	= $image;
		}
		/*if($image['size']){   
			if(preg_match('/png$/', $image['type'])){       
				$override = array('test_form' =>false); 
				$file = wp_handle_upload( $image, $override );  
				if($file){     
					$value = $file['url'];
				}
			}
		}*/
		return $value;
	}
	
	function __call($name, $arguments){
		 $name  = explode('__', $name);
		 $functionName = $name[0]; 
		 return $this->$functionName($name[1]);
    }
	
	function deAppleIconsRegisterSettings(){
		// whitelist options precomposed
		register_setting( 'de-apple-icon-group', 'precomposed');
		register_setting( 'de-apple-icon-group', 'iphone_icon', array($this, 'validateSetting__iphone_icon'));
		register_setting( 'de-apple-icon-group', 'iphone_image', array($this, 'validateSetting__iphone_image'));
		register_setting( 'de-apple-icon-group', 'iphone2x_icon', array($this, 'validateSetting__iphone2x_icon'));
		register_setting( 'de-apple-icon-group', 'iphone2x_image', array($this, 'validateSetting__iphone2x_image'));
		register_setting( 'de-apple-icon-group', 'iphone5_image', array($this, 'validateSetting__iphone5_image'));
		register_setting( 'de-apple-icon-group', 'ipad_icon', array($this, 'validateSetting__ipad_icon'));
		register_setting( 'de-apple-icon-group', 'ipad_image', array($this, 'validateSetting__ipad_image'));
		register_setting( 'de-apple-icon-group', 'ipad_land_image', array($this, 'validateSetting__ipad_land_image'));
		register_setting( 'de-apple-icon-group', 'ipad2x_icon', array($this, 'validateSetting__ipad2x_icon'));
		register_setting( 'de-apple-icon-group', 'ipad2x_image', array($this, 'validateSetting__ipad2x_image'));
		register_setting( 'de-apple-icon-group', 'ipad2x_land_image', array($this, 'validateSetting__ipad2x_land_image'));
	}
	 
	function getUserBrowser(){
		if(!$this->browserName){
			$user_agent = $_SERVER['HTTP_USER_AGENT'];
			if(preg_match('/Opera/i',$user_agent)){
				$this->browserName = "opera";
			}elseif(preg_match('/firefox/i',  $user_agent)){
				$this->browserName = "firefox";
			}elseif(preg_match('/chrome/i',  $user_agent)){ 
				$this->browserName = "chrome";
			}elseif(preg_match('/safari/i',  $user_agent)){
				$this->browserName = "safari";
			}
		}
	}
	
	function getDevice(){
		$device_name;
		$device_agent=$_SERVER['HTTP_USER_AGENT'];	
		if(preg_match('/iPad/i', $device_agent)){
			$device_name="iPad";
		}elseif(preg_match('/iPhone/i',$device_agent)){
			$device_name="iPhone";
		}
		return $device_name;
	}
	
	function getDynamicUrl($url){
		$siteUrl = site_url();
		if(!$url){
			return false;
		}
		$url = explode('wp-content', $url);
		$url = $siteUrl.'/wp-content'.$url[1];
		return $url;
	}
	
	function media_upload_styles() {
		wp_enqueue_style('thickbox');
	}
	
	function media_upload_scripts($hook){
		if($hook=='settings_page_deAppleIcons'){
			wp_enqueue_script('media-upload');
			wp_enqueue_script('thickbox');
			wp_register_script('de_panel_uploader', plugin_dir_url('').'appleicons/appleicons.js', array('jquery','media-upload','thickbox'));
			wp_enqueue_script('de_panel_uploader');
			wp_enqueue_media();
			wp_localize_script( 'de_panel_uploader', 'de_panel_uploader', array(
					'media_window_title' => __( 'Choose an Image', $this->nonceName ),
				)
			);
		}
	}
	
	function print_scripts(){
	}
}

$deAppleIcons = new deAppleIcons();

?>