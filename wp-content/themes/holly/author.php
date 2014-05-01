<?php 
if (!defined('WP_THEME_URL')) {
	define( 'WP_THEME_URL', get_bloginfo('stylesheet_directory'));
}
if (!defined('WP_CONTENT_DIR')) {
	define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
}

// process ajaxupload of avatar
if(isset($_POST['upload_avatar'])) {
	$profile_id = intval($_POST['profile_id']);
	if(is_user_logged_in() and $profile_id) { 
		if($user_ID != $profile_id) {
			die('Login authentication failed.');
		}
		$userdata = get_userdata($user_ID);
		$prefix=substr($user_ID+1000,-3);
		$filepath =  WP_CONTENT_DIR.'/uploads/avatars/'.$prefix;
		if(!file_exists($filepath)) {
			if(!file_exists(WP_CONTENT_DIR.'/uploads/avatars'))
				mkdir(WP_CONTENT_DIR.'/uploads/avatars');
			if(file_exists(WP_CONTENT_DIR.'/uploads/avatars'))
				mkdir($filepath);
			if(!file_exists($filepath))
				die('Could not save the file on the webserver. ');
		}
        $uniqFileName = $user_ID.'_'.time().'_'.rand();
		$filename = $filepath.'/avatar_'.$uniqFileName.'.tmp';
		if (move_uploaded_file($_FILES['userprofile']['tmp_name'], $filename)) {

		// check it is valid
			$imagetype = exif_imagetype($filename);
			switch($imagetype) {
				case IMAGETYPE_GIF:  $img = imagecreatefromgif($filename);  $extn='.gif'; break;
				case IMAGETYPE_JPEG: $img = imagecreatefromjpeg($filename); $extn='.jpg'; break;
				case IMAGETYPE_PNG:  $img = imagecreatefrompng($filename);  $extn='.png'; break;
			default: $img = false;
			}
			if(!$img)
				die('The uploaded image appears to be invalid. ');
				
			if(!sg_savethumbnail($filename, 50, $filepath.'/thumb_'.$uniqFileName.$extn))
				die('The uploaded image appears to be invalid.');
			if(!sg_savethumbnail($filename, 125, $filepath.'/avatar_'.$uniqFileName.$extn))
				die('The uploaded image appears to be invalid. ');
			// delete the original
			unlink($filename);
            
            // delete prev file
            $prevAvatarUrl = get_usermeta($user_ID,'wp_avatar');
            if(!empty($prevAvatarUrl)){
                $prevUniq = substr(basename($prevAvatarUrl), 6);
                // delete thumb
                if(file_exists($filepath. '/thumb_'.$prevUniq)){
                    unlink($filepath. '/thumb_'.$prevUniq);
                }
                // delete avatar
                if(file_exists($filepath. '/avatar_'.$prevUniq)){
                    unlink($filepath. '/avatar_'.$prevUniq);
                }
            }
			// create the user_meta entry
			update_usermeta( $user_ID,'wp_avatar', WP_CONTENT_URL.'/uploads/avatars/'.$prefix.'/thumb_'.$uniqFileName.$extn );
			echo 'OK';
		} else {
			die('Failed to save the file on the webserver. ');
		}
	}
	die();
}

if(isset($_GET['delete_sg_avatar']) and is_user_logged_in()) {
	$userdata = get_userdata($user_ID);
    $avatarUrl = get_usermeta($user_ID,'wp_avatar');
    if(!empty($avatarUrl)){
        $prefix=substr($user_ID+1000,-3);
        $filepath =  WP_CONTENT_DIR.'/uploads/avatars/'.$prefix;
        $prevUniq = substr(basename($avatarUrl), 6);
        // delete thumb
        if(file_exists($filepath. '/thumb_'.$prevUniq)){
            unlink($filepath. '/thumb_'.$prevUniq);
        }
        // delete avatar
        if(file_exists($filepath. '/avatar_'.$prevUniq)){
            unlink($filepath. '/avatar_'.$prevUniq);
        }        
    }
	delete_usermeta( $user_ID, 'wp_avatar');
	header('Location: /author/'.$userdata->user_nicename.'/');
}

// Process EDIT post
if(isset($_POST['profile_id']) and is_user_logged_in()){
	// check auth 
	$profile_id = intval($_POST['profile_id']);
	$userdata = get_userdata($user_ID);

	if(isset($userdata->ID) AND ($userdata->ID==$profile_id) ) {
		$edit_errors = array();
		if(!$userdata->fbuid) {
			// clean data & update details
			$newdata = array('ID' => $profile_id);
			
			if($_POST['displayname']!=$userdata->display_name) {
				$newdata['display_name'] = trim($_POST['displayname']);
			}
			
			if($_POST['email']!=$userdata->user_email) {
				$email = trim(strip_tags($_POST['email']));
				if(!is_email($email)){
					$edit_errors['email'] = 'The email address entered was invalid.';
				} else {
					$newdata['user_email'] = $email;
				}
			}
			if($_POST['website']!=$userdata->user_url) {
				$newdata['user_url'] = trim(strip_tags($_POST['website']));
			}
			
			if($_POST['password1']!=$_POST['password2']) {
				$edit_errors['password'] = 'The passwords you entered did not match.';
			}
			if ( $_POST['password1'] ) $newdata['user_pass'] = $_POST['password1'];
			
			// update user record if we must
			if(count($newdata)>1)
				wp_update_user($newdata);
		} else {
			$newdata = array('ID' => $profile_id);
			if($_POST['website']!=$userdata->user_url) {
				$newdata['user_url'] = trim(strip_tags($_POST['website']));
			}
			if(count($newdata)>1)
				wp_update_user($newdata);
		}
		
		
		
		update_usermeta($profile_id,'birthday',$_POST['month'] . "-" . $_POST['day']  . "-" . $_POST['year']);

		if(isset($_POST['gender'])) {
			$gender = $_POST['gender']=='F'?'F':'M';
			if($gender!=$userdata->gender)
				update_usermeta($profile_id,'gender',$gender);
		}
		
		
		if(isset($_POST['zip_code'])) {
			$zip_code = trim(strip_tags($_POST['zip_code']));
			if( $zip_code && !validateUSAZip( $zip_code ) ){
					$edit_errors['zip_code'] = 'The zip code you entered was invalid.';
				} else {
			if($zip_code!=$userdata->zip_code)
				update_usermeta($profile_id,'zip_code',$zip_code);
			}
		}
		
		if(isset($_POST['description'])) {
				$description = trim(strip_tags($_POST['description']));
				if($description!=$userdata->description)
				update_usermeta($profile_id,'description',$description);
			}
		
		
		// load the existing values and update the posted fields
		$networks = get_usermeta( $profile_id,'networks');
		$networks['facebook'] = trim($_POST['facebook']);
		$networks['twitter'] = trim($_POST['twitter']);
		$networks['hypem'] = trim($_POST['hypem']);
		$networks['lastfm'] = trim($_POST['lastfm']);
		$networks['myspace'] = trim($_POST['myspace']);
		update_usermeta($profile_id,'networks',$networks);
		
		$_GET['author_name'] = $userdata->user_nicename;
		$_GET['edit'] = true;
		
		if(count($edit_errors)==0) { //redirect to avoid POST refresh
			header('Location: /author/'.$userdata->user_nicename.'/');
		}

	}		
}

$network_prefix = array( 
'facebook' => 'http://facebook.com/', 
'myspace' => 'http://myspace.com/',
'twitter' => 'http://twitter.com/', 
'hypem' => 'http://hypem.com/', 
'lastfm' => 'http://last.fm/user/');

?>

<?php get_header(); ?>
<div id="content-column">
	<div id="content" role="main">

<?php

// This sets the $profile variable
if(isset($_GET['author_name'])) {
	$profile = get_userdatabylogin($author_name);
} else {
	$profile = get_userdata(intval($author));
}

$pagetitle = 'Profile';
$is_my_profile = ($userdata->ID && ($userdata->ID == $profile->ID));
$edit_profile  = ($is_my_profile && isset($_GET['edit']));
if($is_my_profile)
	$pagetitle = $edit_profile ? 'Edit Profile':'Your Profile';

if(!$profile) {
	echo '<h2>Profile not found.</h2>	Sorry, the profile you requested is not available.</div>';
} else {
	$profile->user_id = $profile->ID; // for FB avatar
?>

<?php 
// edit mode
if($edit_profile) { 
	if(!$profile->fbuid) { // include image upload script 
		echo '<script type="text/javascript" src="'.WP_THEME_URL.'/js/ajaxupload.js"></script>'; ?>
<script type="text/javascript">jQuery(document).ready( function() {
	jQuery('#delete_button').click(function(){window.location='/author/<?php echo $userdata->user_nicename; ?>/?delete_sg_avatar';});
	new AjaxUpload('upload_button', {
  // Location of the server-side upload script
  // NOTE: You are not allowed to upload files to another domain
  action: '/author/<?php echo $userdata->user_nicename; ?>/',
  // File upload name
  name: 'userprofile',
  // Additional data to send
  data: {
    profile_id : '<?php echo $userdata->ID; ?>',
    upload_avatar : 1
  },
  // Submit file after selection
  autoSubmit: true,
  // The type of data that you're expecting back from the server.
  // HTML (text) and XML are detected automatically.
  // Useful when you are using JSON data as a response, set to "json" in that case.
  // Also set server response type to text/html, otherwise it will not work in IE6
  responseType: false,
  // Fired after the file is selected
  // Useful when autoSubmit is disabled
  // You can return false to cancel upload
  // @param file basename of uploaded file
  // @param extension of that file
  onChange: function(file, extension){},
  // Fired before the file is uploaded
  // You can return false to cancel upload
  // @param file basename of uploaded file
  // @param extension of that file
  onSubmit: function(file, extension) {
			if (! (extension && /^(jpg|png|jpeg|gif)$/.test(extension.toString().toLowerCase()))){
			alert('Error: The file you selected has an invalid file extension.'); // extension is not allowed
			return false; // cancel upload
		}
		this.disable();
		jQuery('#upload_progress').show();
		jQuery('#upload_form').hide();
		// display uploader bar 
	},
  // Fired when file upload is completed
  // WARNING! DO NOT USE "FALSE" STRING AS A RESPONSE!
  // @param file basename of uploaded file
  // @param response server response
  onComplete: function(file, response) {
			if(response=='OK') { window.location='/author/<?php echo $userdata->user_nicename; ?>/?edit';	
		}	else { 
			jQuery('#upload_progress').hide();
			alert('UPLOAD FAILED: '+response);
			this.enable();
			jQuery('#upload_form').show();
		}
	}
	}); 
}); </script>
<?php } ?>	

<!-- Edit Profile Page -->
<div class="profile_edit">
<div id="login-container"><h1 class="login">Profile</h1>
<div class="profile" id="your-profile">
<table class="form-table">
<tr>
<td class="profile-avatar">
	<table cellspacing="0" cellpadding="0"><tr><td width="60"><div style="width:50px;height:50px;"><?php 
		if ( $profile->fbuid )
			echo '<div class="avatar avatar-32"><img src="https://graph.facebook.com/' . $profile->fbuid . '/picture" width="50" heigth="50"></div>';
		else
		{
			$avatarHTML = get_avatar($profile,$size='50', WP_THEME_URL.'/images/default_avatar.jpg');
			echo $avatarHTML;
		}
	?></td><td>
	<?php if($profile->fbuid) { 
		echo 'Connected Via Facebook'; 
	} else {
		echo $profile->user_login; 
	}
	?>
	</td></tr></table>
</td></tr>
<?php if($profile->fbuid) {
	echo '<form method="POST" action="/author/'.$profile->user_nicename.'/">';
} else {
	$radio_gender = array();
	$radio_gender[$profile->gender] = ' checked="checked"';
	
	
	
	if(count($edit_errors)){
		echo '<tr><td>';
		foreach($edit_errors as $error) { echo '<div style="color:#f00;font-weight:bold;">'.$error.'</div>';}
		echo '</td></tr>';
	}
 ?>
	<tr><td class="form_title">Change Photo</td></tr>
	<tr><td><small>If you have a <a href="http://en.gravatar.com/" target="_blank">Gravatar</a> image set up for your registered email we will use that, 
			alternatively you can <a href="#" onclick="jQuery('#upload_div').toggle();return false;">upload your own image</a>.</small>
			<div id="upload_div" style="display:none;">
			<div id="upload_progress" style="display:none;text-align:center;padding:10px">Uploading:<br><img src="<?php echo WP_THEME_URL; ?>/images/loadingAnimation.gif"></div>
			<div id="upload_form">
			<table style="padding:10px 5px"><tr valign="top"><td>
					&nbsp;<button  id="upload_button" class="btn_pill" style="margin:5px">Upload</button></td><td>Images will be auto-cropped to a square, maximum filesize of 1MB, Must be JPG, PNG or GIF.
				</td></tr>
				<?php if(isset($profile->wp_avatar)) { ?>
				<tr valign="top"><td>
					&nbsp;<button id="delete_button" class="btn_pill" style="margin:5px">Delete</button></td><td>Delete the current image and use Gravatar or default image.
				</td></tr> 
				<?php  } ?></table>
				</div>
			</div>
		</td></tr>
		
	<form method="POST" action="/author/<?php echo $profile->user_nicename; ?>/">
	<tr><td class="form_title">Display Name</td></tr>
	<tr></tr><td><input name="displayname" class="longfield" value="<?php echo esc_attr($profile->display_name); ?>"></td></tr>
	<tr><td class="form_title">Email</td></tr>
	<tr><td><input name="email" class="longfield" value="<?php echo esc_attr($profile->user_email); ?>"></td></tr>
	<tr><td class="form_title">Change Password</td></tr>
	<tr><td><input type="password" name="password1" class="longfield" value=""></td></tr>
	<tr><td class="form_title">Retype Password</td></tr>
	<tr><td><input type="password" name="password2" class="longfield" value=""></td></tr>

	<tr><td class="form_title">Birthday</td></tr>
	<tr><td>
	<select name="month" class="mid-mini" id="month"> 
		<?php 		
		$currentbday = $profile->birthday;
		$birthday = explode('-', $currentbday);
		$bmonth = $birthday[0];
		$bday = $birthday[1];
		$byear = $birthday[2];
		
		$currentYear = date("Y");
		$currentMonth = date("F");
		$currentDay = date("j");
		$months = array (1 => 'January', 'February', 'March', 'April', 'May', 'June','July', 'August', 'September', 'October', 'November', 'December');
		$days = range (1, 31);
		
		foreach ($months as $value) {
			if (isset($bmonth)) {
				if ($value == $bmonth) {
					echo "<option selected>$value</option>";
				}
				
			} else {
				if ($value == $currentMonth) {
					echo "<option selected>$value</option>";
				}
			}
		echo "<option value='$value'>$value</option>";
		} 
		?>
		</select>
	
		<select name="day" class="mid-mini" id="day">
		<?php 
foreach ($days as $value) {
		if (isset($birthday)) {
				if ($value == $bday) {
					echo "<option selected>$value</option>";
				}
				
			} else {
				if ($value == $currentDay) {
					echo "<option selected>$value</option>";
				}
			}
		echo "<option value='$value'>$value</option>";
		} 

		 ?>
		</select>
		
		<select name="year" class="mid-mini" id="year">
		<?php 

	for($i=$currentYear;$i>$currentYear-90;$i--) {
		if (isset($birthday)) {
						if ($i == $byear) {
							echo "<option selected>$i</option>";
						}
						
					} else {
						if ($currentYear == $i) {
							echo "<option selected>$i</option>";
						}
					}
		echo "<option value='$i'>$i</option>";
		} 

		?>
		</select>
	</td></tr>
	<tr><td class="form_title">Gender</td></tr>
	<tr><td>
		<input name="gender" id="gender_M" type="radio" value="M"<?php echo $radio_gender['M']; ?> > <label for="gender_M">Male</label>&nbsp;&nbsp;&nbsp;&nbsp;
		<input name="gender" id="gender_F" type="radio" value="F"<?php echo $radio_gender['F']; ?> > <label for="gender_F">Female</label>
	</td></tr>
	<tr><td class="form_title"><label for="zip_code">Zip Code</label></td></tr>
	<tr><td><input type="text" name="zip_code" maxlength="5" id="zip_code" value="<?php echo $profile->zip_code; ?>" class="mid-mini" /></td></tr>
	<tr><td class="form_title"><label for="description">About Me</label></td></tr>
	<tr><td><textarea name="description" class="longfield" id="description" rows="5" cols="66"><?php echo $profile->description ?></textarea></td>
	</tr>



	<?php } ?>
<table class="contact-table">
<tr><td colspan="2">Help people find you on the web:</td></tr>
<tr><td  colspan="2" class="form_title">My website
<br />
<input name="website" class="longfield" value="<?php echo esc_attr($profile->user_url); ?>"></td></tr>
<tr><td colspan="2"><img src="<?php echo WP_THEME_URL; ?>/images/facebook_button_hover.jpg">&nbsp;http://facebook.com/
<br />
<input name="facebook" class="longfield" value="<?php echo esc_attr($profile->networks['facebook']); ?>"></td></tr>
<tr>
	<!--td class="cancel"><span class="cancel"><a href="/"></a></span></td-->
	<td class="fbsubmit"><input type="submit" id="fb-submit" value="Save"></td>
</tr>
</table>
<input type="hidden" name="profile_id" value="<?php echo $profile->ID; ?>">
</form>
</table>
</div></div></div></div></div>

<?php } else { ?>
<!-- Front facing profile page --> 
<div id="login-container"><h1 class="login">Profile</h1>
<div class="profile" id="your-profile">
<table class="form-table">
<tr>
<td class="profile-avatar" width="60">
<div style="width:50px;height:50px;background:url('<?php echo WP_THEME_URL; ?>/images/default_avatar.jpg')">
	<?php 
	if(!$profile->fbuid) {
		echo get_avatar($profile,$size='50', WP_THEME_URL.'/images/default_avatar.jpg');
	} else {
		// try and get a decent FB avatar
		echo '<div class="avatar avatar-32"><img src="https://graph.facebook.com/' . $profile->fbuid . '/picture" width="50" heigth="50"></div>';
	}
	
	?></div>

</td>
<td class="profile-info"><label for="display_name">
<?php echo $profile->display_name;
	if($is_my_profile) {
		echo ' <a href="?edit">(Edit)</a>';
	}
?></label>
<?php 
	if(!empty($profile->user_url)) {
	echo '<br /><label for="website">Website:</label>';
	echo '<a href="'.$profile->user_url.'" target="_blank">'.$profile->user_url.'</a>'; 
	}?>
</td>
</tr>
</table>
</div></div></div>
</div>

	<?php } ?>


<?php } ?>

<?php 
get_sidebar(); 
echo '</div>';
get_footer();
 ?>
