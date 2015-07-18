<?php
/* Plugin Name: FirstImpression
Plugin URI: http://www.firstimpression.io/
Version: 1.3.0
Description: FirstImpression is the first platform that allows publishers create different ad products anywhere on their website in seconds and with no coding.
Author: FirstImpression
Author URI: http://www.firstimpression.io/
*/


/*
 * Update this variable to modify plugin version text in actual site tag 
 */
$pluginVersion = '1.3.0';

// Add settings link on plugin page
function your_plugin_settings_link($links) { 
  $settings_link = '<a href="options-general.php?page=firstimpression">Settings</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}
 
$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", 'your_plugin_settings_link' );

//get the settings from database
$ssb = get_option("ssb_options");

//activate/de-activate hooks
//dont change it, its required
function ssb_activation() {}
function ssb_deactivation() {}
register_activation_hook(__FILE__, 'ssb_activation');
register_deactivation_hook(__FILE__, 'ssb_deactivation');


/// this is a wordpress action which adds a page link in the wordpress admin panel bu calling my function ssb_settings
add_action('admin_menu', 'ssb_settings');
//adding a page link in admin panel
function ssb_settings()
{
	add_options_page( "FirstImpression", "FirstImpression", 'administrator', 'firstimpression', 'ssb_admin_function');
	//this adds the page: parameters are: "page title", "link title", "role", "slug","function that shows the result"
}


if ( ! is_admin() ) {
     //printing the script
	add_action('wp_head','ssb_output_g'); //print in header
	add_action('wp_footer','ssb_page_data'); //print in footer
}





//main function, returns the output
function ssb_output()
{

    //get the settings from database
    $ssb = get_option("ssb_options");

    //adding the script result in a variable
    $output = "\n<!--BEGIN FIRSTIMPRESSION TAG -->\n";
    $output .= "<script data-cfasync='false' type='text/javascript'>\n";
    $output .= "	if (window.location.hash.indexOf('apdAdmin')!= -1){if(typeof(Storage) !== 'undefined') {localStorage.apdAdmin = 1;}}\n";
    $output .= "	var adminMode = ((typeof(Storage) == 'undefined') || (localStorage.apdAdmin == 1));\n";
    $output .= "	window.apd_options = {\n";
	$output .= "	\"accelerate\": 0,\n";    
	$output .= "	\"dynamicElements\": 1,\n";
	$output .= "	\"websiteId\": ".$ssb['site_id']."\n";
	$output .= "	};\n";
	$output .= "	(function() {\n";
	$output .= "		var apd = document.createElement('script'); apd.type = 'text/javascript'; apd.async = true;\n";
	$output .= "		if(adminMode){\n";
	$output .= "			apd.src = 'https://ecdn.firstimpression.io/apd.js?id=' + apd_options.websiteId;\n";
	$output .= "		}\n";
	$output .= "		else{\n";
	$output .= "			apd.src = (('https:' == document.location.protocol || window.parent.location!=window.location) ? 'https://' : 'http://') + 'ecdn.firstimpression.io/apd_client.js';\n";
	$output .= "		}\n";
	$output .= "		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(apd, s);\n";
	$output .= "	})();\n";
    $output .= "</script>\n";
    $output .= "<!-- END FIRSTIMPRESSION TAG -->\n";
    //output created, now returning it back to the calling element !
    return $output;

}

function ssb_page_data_demo() {
    global $pluginVersion, $wp_version;
    $output = "<!-- FirstImpression Targeting - Start -->\n"
            . "<div id='apdPageData' data-plugin-version='$pluginVersion' data-wp-version='$wp_version' style='display:none;visibility:hidden;'>\n"
            . "<span id='apdPageData_categories'>[categories]</span>\n"
            . "<span id='apdPageData_tags'>[tags]</span>\n"
            . "<span id='apdPageData_author'>[author]</span>\n"
            . "</div><!-- FirstImpression Targeting - End -->\n";
    
    echo $output;
}


function ssb_page_data() {
    global $post, $pluginVersion, $wp_version;
        
    //Returns All category Items
    $term_array = wp_get_post_terms($post->ID, 'category', array("fields" => "names"));
    $category_list = ( empty($term_array) OR is_wp_error($term_array) ) ? '' : implode(',', $term_array);
    
    //Returns Array of Tag Names
    $term_array = wp_get_post_terms($post->ID, 'post_tag', array("fields" => "names"));
    $tag_list = ( empty($term_array) OR is_wp_error($term_array) ) ? '' : implode(',', $term_array);
    
    $display_name = get_the_author_meta('display_name');
    $display_name = ( empty($display_name) OR is_wp_error($display_name) ) ? '' : $display_name;
    
    $output  = '<!-- FirstImpression Targeting - Start -->';
    $output .= "\n" . '<div id="apdPageData" data-plugin-version="' . $pluginVersion . '" data-wp-version="' . $wp_version . '" style="display:none;visibility:hidden;">';  
    $output .= "\n\t" . '<span id="apdPageData_categories">' . $category_list . '</span>';
    
    if(is_single()) {
        $output .= "\n\t" . '<span id="apdPageData_tags">' . $tag_list . '</span>';
        $output .= "\n\t" . '<span id="apdPageData_author">' . $display_name . '</span>';
    }
    
    $output .= "\n" . "</div>\n<!-- FirstImpression Targeting - End -->\n";
    
    echo $output;
}


//this echo's the code
function ssb_output_g()
{
    echo ssb_output();
}


//biggest function but worth it
function ssb_admin_function()
{
	//check if the user is allowed to edit wordress settings
	if(!current_user_can('manage_options'))
		wp_die('You do not have sufficient permissions to access this page.');
		//die if not allowed
	$ssb = get_option("ssb_options"); //get saved settings to initially show in form
	//here starts the html of form, cant document the html (Do you really need this ? do you!)
	?>
		<div class="wrap">
			<?php screen_icon('plugins'); ?><h2>FirstImpression Wordpress Site Tag Plugin</h2>
<p>FirstImpression is a tool which allows you to easily add monetizeable ad products to your site. This plugin will provide the integration to allow the placements on your site to be managed through FirstImpression's platform. Just add the site id you got from your account manager, click the "Updated Embedded Code" button and you are good to go.</p>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">
							<label for="site_id_vas">
								Site ID:
							</label>
						</th>
						<td>
							<input type="text" id="site_id_vas" onchange="chTXT()" name="site_id_vas" value="<?php echo $ssb['site_id'];?>"  /><span id="setting-error-settings_error" class="error settings-error asd_error " style=" border-color: #c00;display: inline-block;display:none;background-color: #ffebe8;border: 1px solid #c00;padding: 0 3px;margin-left: 10px;border-radius: 4px;"><p style="padding: 1px;margin: 0;"><strong>Enter A Valid Number.</strong></p></span>
						</td>
					</tr>
				</table>
				<p class="submit">
					<input name="update_settings" id="submit_options_form" type="submit" class="button-primary vasu_btn" value="Updated Embedded Code" />
				</p>

				<div id="setting-error-settings_updated" class="updated settings-error asd_saved" style="display:none; width: 76%;"><p><strong>Settings saved.</strong></p></div><p>Click <a href="https://admin.firstimpression.io"> here </a> to login to your admin console on FirstImpression and manage your placements</p>

				<p>The following code will be embedded in your site's template:<br />
<textarea style="width: 77%;height: 280px;" class="result_demo"><?php echo htmlentities(ssb_output());echo htmlentities(ssb_page_data_demo());?></textarea></p>
		</div>
	<?php
}

// this will add javascript in admin required for ajax
add_action( 'admin_footer', 'ssb_ajax_javascript' );
function ssb_ajax_javascript() {
    
?>
<script type="text/javascript" >
    var tags = "<?=ssb_page_data_demo()?>";
    //this function is providing the functionality of live realtime change of code in textarea
    function chTXT(){
                //saving to variables
        var tmp_site_id_vas = document.getElementById('site_id_vas').value;
        
        var output = "<!--BEGIN FIRSTIMPRESSION TAG -->\n&lt;scri"+"pt data-cfasync='false' type='text/javascript'\&gt;\n";
        output = output+ "if (window.location.hash.indexOf('apdAdmin')!= -1){if(typeof(Storage) !== 'undefined') {localStorage.apdAdmin = 1;}}\n";
		output = output+ "var adminMode = ((typeof(Storage) == 'undefined') || (localStorage.apdAdmin == 1));\n";
		output = output+ "window.apd_options = {\n";
		output = output+ " \"accelerate\": 0,\n";
		output = output+ " \"dynamicElements\": 1,\n";
		output = output+ " \"websiteId\": "+tmp_site_id_vas+"\n";
        output = output+ "};\n";
        output = output+ "(function() {\n";
        output = output+ "var apd = document.createElement('script'); apd.type = 'text/javascript'; apd.async = true;\n";
        output = output+ "if(adminMode){\n";
        output = output+ "apd.src = 'https://ecdn.firstimpression.io/apd.js?id=' + apd_options.websiteId;\n";
        output = output+ "}\n";
        output = output+ "else{\n";
        output = output+ "apd.src = (('https:' == document.location.protocol || window.parent.location!=window.location) ? 'https://' : 'http://') + 'ecdn.firstimpression.io/apd_client.js';";
		output = output+ "}\n";
		output = output+ "var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(apd, s);\n";
		output = output+ "})();\n";
		output=output+ "&lt;/scr"+"ipt\&gt;" + '\n<!-- END FIRSTIMPRESSION TAG -->\n\n' + tags;
        jQuery('.result_demo').html(output);

    }

//jquery document ready call
jQuery(document).ready(function($)
{


	//jquery sace button click call
	$('.vasu_btn').click(function ()
	{
		//hide the error box if visible
		$('.asd_error').hide();

		//clear the textarea
		$('.result_demo').html(" ");
		//save the values of form in variables
		var site_id_vas = document.getElementById('site_id_vas').value;

		//verifcation for a valid number
		if(!isNaN(site_id_vas) && site_id_vas!="")
		{
			//if verified, create a json varibale for sending
			var data = {action: 'my_action',
				a:site_id_vas,
			};
			//use post method to send the data
			$.post(ajaxurl, data, function(response)
			{
				//show the response in teatarea
				$('.result_demo').html(response.trim() + "\n\n" + tags);
				//show "saved" message
				$('.asd_saved').show('slow');
			});

		}else
		{
			//if verification fails, show the error message
			$('.asd_error').css("display", "inline-block");
		}
	});

});
// javascript ends here
</script>


<?php
///back to php
}


// ajax hook
add_action('wp_ajax_my_action', 'ssb_ajax_action');
//ajax hook function

function ssb_ajax_action() {
	global $wpdb; // this is how you get access to the database

	$a = $_POST['a']; //copying the received data in variables

	$ssb_settings = array('site_id'=>$a);// creating a settings array from the variables

	update_option("ssb_options", $ssb_settings); //save the settings


	 echo htmlentities(ssb_output()); //return the script result after saving

	die(); // this is required to return a proper result
}

