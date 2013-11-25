<?php
/* Plugin Name: AppendAd
Plugin URI: http://www.appendad.com/
Version: 1.0.2
Description: AppendAd is the first platform that enables publishers to create new ad placements anywhere on their website in seconds and in any format, without programmers or graphic designers. These placements can be monetized with the publisher's existing ad inventory or through AppendAd certified ad networks.
Author: AppendAd
Author URI: http://www.appendad.com/
*/

// Add settings link on plugin page
function your_plugin_settings_link($links) { 
  $settings_link = '<a href="options-general.php?page=appendad">Settings</a>'; 
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
	add_options_page( "AppendAd", "AppendAd", 'administrator', 'appendad', 'ssb_admin_function');
	//this adds the page: parameters are: "page title", "link title", "role", "slug","function that shows the result"
}



//conditional printing the script based on synchronous or asynchronous
if($ssb['synca']=="async"){
	add_action( 'wp_footer', 'ssb_output_g' ); // if asynchronous then in footer
}elseif($ssb['synca']=="sync"){
	add_action( 'wp_head', 'ssb_output_g' ); //else if synchronous then in header
}else  {
	print_r($ssb);
}



//main function, returns the output
function ssb_output()
{

//get the settings from database
$ssb = get_option("ssb_options");

//adding the script result in a variable
$output = "";
$output .= "<script>
(function(){
";

/// condition showing of script according to settings saved
if($ssb['acceler']=="true")
$output .="var apd_accelerate=1;
";

/// condition showing of script according to settings saved
if($ssb['dynmic']=="true")
$output .="var apd_disabledynamic=1;
";

$output .="var apd = document.createElement('script');
apd.type = 'text/javascript'; apd.async = true;
apd.src = ('https:' == document.location.protocol || window.parent.location!=window.location ? 'https://secure' : 'http://cdn') + '.appendad.com/apd.js?id=";

//adding of site id in output
$output .= $ssb['site_id'];


$output .="';var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(apd, s);})();</script>";

//outpur created, now returning it back to the calling element !
return $output;

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
			<?php screen_icon('plugins'); ?><h2>AppendAd Wordpress Site Tag Plugin</h2>
<p>AppendAd is a tool which allows you to easily add monetizeable ad units to your site. Once you have added your site id, please visit <a href="http://www.appendad.com">AppendAd.com</a> for more information on setting the ads up on your sites.</p>

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
					<tr valign="top">
						<th scope="row">
							<label for="param_2">
								Implementation Style:
							</label>
						</th>
						<td>
							<input id="parm1" type="radio" name="sync_async_vas" OnChange="chTXT()" value="sync"  <?php if($ssb['synca']=="sync"){echo "checked=checked";}?>checked="checked" />Synchronous<br />
							<input id="parm2"  type="radio" name="sync_async_vas" OnChange="chTXT()" value="async" <?php if($ssb['synca']=="async"){echo "checked=checked";}?> />A-Synchronous
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="acceler_vas">
								Enable Accelerated Loading:
							</label>
						</th>
						<td>
							<input  type="checkbox" id="acceler_vas" OnChange="chTXT()" name="acceler_vas" <?php if($ssb['acceler']=="true"){echo "checked=checked";}?> />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="dynamic_vas">
								Disable Checking for dynamic elements:
							</label>
						</th>
						<td>
							<input type="checkbox" id="dynamic_vas" OnChange="chTXT()" name="dynamic_vas" <?php if($ssb['dynmic']=="true"){echo "checked=checked";}?> />
						</td>
					</tr>

				</table>
				<p class="submit">
					<input name="update_settings" id="submit_options_form" type="submit" class="button-primary vasu_btn" value="Updated Embedded Code" />
				</p>

				<div id="setting-error-settings_updated" class="updated settings-error asd_saved" style="display:none; width: 76%;"><p><strong>Settings saved.</strong></p></div><p>Click <a href="#"> here </a> to access our current Ad Placement gallery and create a new placement</p>

				<p>The following code will be embedded in your site's template:<br />
<textarea style="width: 77%;height: 142px;" class="result_demo"><?php  echo htmlentities(ssb_output());?></textarea></p>
		</div>
	<?php
}

// this will add javascript in admin required for ajax
add_action( 'admin_footer', 'ssb_ajax_javascript' );
function ssb_ajax_javascript() {
?>
<script type="text/javascript" >

//this function is providing the functionality of live realtime change of code in textarea
	function chTXT(){
		//saving to variables
	var tmp_dynamic_vas = document.getElementById('dynamic_vas').checked;
	var tmp_acceler_vas = document.getElementById('acceler_vas').checked;
	var tmp_synch_vas = jQuery('input:radio[name=sync_async_vas]:checked').val();
	var tmp_site_id_vas = document.getElementById('site_id_vas').value;
	var output="";
	output= output+ "&lt;scri"+"pt\&gt;\n(function(){\n";
		if(tmp_acceler_vas){
			output= output+ "var apd_accelerate=1;\n";
		}
		if(tmp_dynamic_vas){
			output= output+ "var apd_disabledynamic=1;\n";
		}
		output = output+ "var apd = document.createElement('script');\napd.type = 'text/javascript'; apd.async = true;\napd.src = ('https:' == document.location.protocol || window.parent.location!=window.location ? 'https://secure' : 'http://cdn') + '.appendad.com/apd.js?id="+tmp_site_id_vas+"';";

	output=output+ "var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(apd, s);})();&lt;/scr"+"ipt\&gt;";

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
		var dynamic_vas = document.getElementById('dynamic_vas').checked;
		var acceler_vas = document.getElementById('acceler_vas').checked;
		var synch_vas = $('input:radio[name=sync_async_vas]:checked').val();
		var site_id_vas = document.getElementById('site_id_vas').value;

		//verifcation for a valid number
		if(!isNaN(site_id_vas) && site_id_vas!="")
		{
			//if verified, create a json varibale for sending
			var data = {action: 'my_action',
				a:site_id_vas,
				b:acceler_vas,
				c:synch_vas,
				d:dynamic_vas
			};
			//use post method to send the data
			$.post(ajaxurl, data, function(response)
			{
				//show the response in teatarea
				$('.result_demo').html(response);
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

	$a = $_POST['a'];	$b = $_POST['b'];	$c = $_POST['c'];	$d = $_POST['d']; //copying the received data in variables

	$ssb_settings = array('site_id'=>$a,'acceler'=>$b,'synca'=>$c,'dynmic'=>$d);// creating a settings array from the variables

	update_option("ssb_options", $ssb_settings); //save the settings


	 echo htmlentities(ssb_output()); //return the script result after saving

	die(); // this is required to return a proper result
}

