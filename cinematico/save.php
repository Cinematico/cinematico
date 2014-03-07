<?php

session_start();

// File locations.
$settings_file = "../config.php";
$htaccess_file = "../.htaccess";
$phpass_file   = '../cinematico/includes/phpass.php';

// Get existing settings.
if (file_exists($settings_file)) {
    include($settings_file);
}

if (file_exists($phpass_file)) {
    include($phpass_file);
    $hasher  = new PasswordHash(8,FALSE);
}

/*-----------------------------------------------------------------------------------*/
/* Save Submitted Settings
/*-----------------------------------------------------------------------------------*/

if ($_POST["submit"] == "submit" && (!file_exists($settings_file) || isset($_SESSION['user']))) {

    // For theme uploads.
    function rmdir_recursive($dir) {
        foreach(scandir($dir) as $file) {
           if ('.' === $file || '..' === $file) continue;
           if (is_dir("$dir/$file")) rmdir_recursive("$dir/$file");
           else unlink("$dir/$file");
       }
       
       rmdir($dir);
    }
    
    // Theme uploads.
    if($_FILES["theme_upload"]["name"]) {
    	$filename = $_FILES["theme_upload"]["name"];
    	$source = $_FILES["theme_upload"]["tmp_name"];
    	$type = $_FILES["theme_upload"]["type"];
    	
    	$name = explode(".", $filename);
    	$accepted_types = array('application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed');
    	foreach($accepted_types as $mime_type) {
    		if($mime_type == $type) {
    			$okay = true;
    			break;
    		} 
    	}
    	
    	$continue = strtolower($name[1]) == 'zip' ? true : false;
    	if(!$continue) {
    	    $message = "The file you are trying to upload is not a .zip file. Please try again.";
    	}
    
        // Define the upload path.
        $path = '../themes/';
        $filenoext = basename ($filename, '.zip');
        $filenoext = basename ($filenoext, '.ZIP');
        
        // Target directory.
        $targetdir = $path . $filenoext;
        
        // Target zip file.
        $targetzip = $path . $filename;
        
        // Create directory if not exists, otherwise overwrite the target directory is same as filename without extension.
        if (is_dir($targetdir))  rmdir_recursive ( $targetdir);
         
        mkdir($targetdir, 0777);
        
        // Things are happening here.	
    	if(move_uploaded_file($source, $targetzip)) {
    		$zip = new ZipArchive();
    		$x = $zip->open($targetzip);  // open the zip file to extract
    		if ($x === true) {
    			$zip->extractTo($targetdir); // place in the directory with same name  
    			$zip->close();
    	
    			unlink($targetzip);
    		}
    		$message = "Your .zip file was uploaded and unpacked.";
    	} else {	
    		$message = "There was a problem with the upload. Please try again.";
    	}
    }
    
    function upload_image($image, $format) {
    
        // Settings for image uploads.
        $allowed = array('jpeg','jpg','png');
        $temp = explode('.', $_FILES['site_' . $image]['name']);
        $extension = end($temp);
        
        // If the selected file passes all checks, attempt to upload the logo image.
        if ((($_FILES['site_' . $image]['type'] == 'image/jpeg') || ($_FILES['site_' . $image]['type'] == 'image/jpg') || ($_FILES['site_' . $image]['type'] == 'image/png')) && ($_FILES['site_' . $image]['size'] < 200000000) && in_array($extension, $allowed)) {
            
            // Upload the logo image.
            if ($_FILES['site_' . $image]['error'] > 0) {
            
                // Upload error.
            } else {
            
                // If there are no errors, upload the logo image.
                move_uploaded_file($_FILES['site_' . $image]['tmp_name'], '../uploads/' . $image . '.' . $format);
            }
        
        // If the selected file doesn't pass all checks, provide an error.    
        } else {
            
            // Not a valid file error.
            
            
        }
    }
    
    if(isset($_FILES['site_logo'])) {
        upload_image('logo', 'png');
    }
    
    if(isset($_FILES['site_favicon'])) {
        upload_image('favicon', 'png');
    }
    
    if(isset($_FILES['site_background'])) {
        upload_image('background', 'jpg');
    }
    
    // Delete the logo.
    if(isset($_POST['remove_site_logo'])) {
        unlink('../uploads/logo.png');
    }
    
    // Delete the favicon.
    if(isset($_POST['remove_site_favicon'])) {
        unlink('../uploads/favicon.png');
    }
    
    // Delete the background.
    if(isset($_POST['remove_site_background'])) {
        unlink('../uploads/background.jpg');
    }
    
    // Remove a theme function.
    function remove_theme($directory) {
        foreach(glob("{$directory}/*") as $file)
        {
            if(is_dir($file)) { 
                remove_theme($file);
            } else {
                unlink($file);
            }
        }
        rmdir($directory);
    }
    
    // Need to run this function for all themes.
    if (isset($_POST["remove_theme"])) {
        remove_theme('../themes/' . $_POST["remove_theme"]);
    }
    
    // Get submitted setup values.
    if (isset($_POST["site_url"])) {
        $site_url = $_POST["site_url"];
    }
    if (isset($_POST["site_title"])) {
        $site_title = addslashes($_POST["site_title"]);
    }
    if (isset($_POST["site_description"])) {
        $site_description = addslashes($_POST["site_description"]);
    }
    if (isset($_POST["video_service"])) {
        $video_service = $_POST["video_service"];
    }
    if (isset($_POST["youtube_username"])) {
        $youtube_username = $_POST["youtube_username"];
    }
    if (isset($_POST["youtube_display"])) {
        $youtube_display = $_POST["youtube_display"];
    }
    if (isset($_POST["youtube_channel"])) {
        $youtube_channel = $_POST["youtube_channel"];
    }
    if (isset($_POST["youtube_playlist"])) {
        $youtube_playlist = $_POST["youtube_playlist"];
    }
    if (isset($_POST["youtube_featured_video"])) {
        $youtube_featured_video = $_POST["youtube_featured_video"];
    }
    if (isset($_POST["vimeo_username"])) {
        $vimeo_username = $_POST["vimeo_username"];
    }
    if (isset($_POST["vimeo_display"])) {
        $vimeo_display = $_POST["vimeo_display"];
    }
    if (isset($_POST["vimeo_channel"])) {
        $vimeo_channel = $_POST["vimeo_channel"];
    }
    if (isset($_POST["vimeo_featured_video"])) {
        $vimeo_featured_video = $_POST["vimeo_featured_video"];
    }
    if (isset($_POST["site_theme"])) {
        $site_theme = $_POST["site_theme"];
    }
    if (isset($_POST["gallery_title"])) {
        $gallery_title = addslashes($_POST["gallery_title"]);
    }
    if (isset($_POST["gallery_description"])) {
        $gallery_description = addslashes($_POST["gallery_description"]);
    }
    if (isset($_POST["gallery_items_number"])) {
        $gallery_items_number = addslashes($_POST["gallery_items_number"]);
    }
    if (isset($_POST["footer_text"])) {
        $footer_text = addslashes($_POST["footer_text"]);
    }
    if (isset($_POST["about_title"])) {
        $about_title = addslashes($_POST["about_title"]);
    }
    if (isset($_POST["about_text"])) {
        $about_text = addslashes($_POST["about_text"]);
    }
    if (isset($_POST["not_found_title"])) {
        $not_found_title = addslashes($_POST["not_found_title"]);
    }
    if (isset($_POST["not_found_text"])) {
        $not_found_text = addslashes($_POST["not_found_text"]);
    }
    if (isset($_POST["profile_name"])) {
        $profile_name = $_POST["profile_name"];
    }
    if (isset($_POST["profile_twitter"])) {
        $profile_twitter = $_POST["profile_twitter"];
    }
    if (isset($_POST["profile_facebook"])) {
        $profile_facebook = $_POST["profile_facebook"];
    }
    if (isset($_POST["profile_email"])) {
        $profile_email = $_POST["profile_email"];
    }
    if (isset($_POST["site_analytics"])) {
        $site_analytics = $_POST["site_analytics"];
    }
    if (isset($_POST["username"])) {
        $username = $_POST["username"];
    }
    if (isset($_POST["email"])) {
        $email = $_POST["email"];
    }
    if (!isset($password) || !empty($_POST["password"])) {
        $password = $hasher->HashPassword($_POST["password"]);
    }

    // Setting format function.
    function settings_format($name, $value) {
        return sprintf("\$%s = \"%s\";", $name, $value);
    }
    
    // Output submitted setup values.
    $config[] = "<?php";
    $config[] = settings_format("site_url", $site_url);
    $config[] = settings_format("site_title", $site_title);
    $config[] = settings_format("site_description", $site_description);
    $config[] = settings_format("video_service", $video_service);
    $config[] = settings_format("youtube_username", $youtube_username);
    $config[] = settings_format("youtube_display", $youtube_display);
    $config[] = settings_format("youtube_channel", $youtube_channel);
    $config[] = settings_format("youtube_playlist", $youtube_playlist);
    $config[] = settings_format("youtube_featured_video", $youtube_featured_video);
    $config[] = settings_format("vimeo_username", $vimeo_username);
    $config[] = settings_format("vimeo_display", $vimeo_display);
    $config[] = settings_format("vimeo_channel", $vimeo_channel);
    $config[] = settings_format("vimeo_featured_video", $vimeo_featured_video);
    $config[] = settings_format("site_theme", $site_theme);
    $config[] = settings_format("gallery_title", $gallery_title);
    $config[] = settings_format("gallery_description", $gallery_description);
    $config[] = settings_format("gallery_items_number", $gallery_items_number);
    $config[] = settings_format("footer_text", $footer_text);
    $config[] = settings_format("about_title", $about_title);
    $config[] = settings_format("about_text", $about_text);
    $config[] = settings_format("not_found_title", $not_found_title);
    $config[] = settings_format("not_found_text", $not_found_text);
    $config[] = settings_format("profile_name", $profile_name);
    $config[] = settings_format("profile_twitter", $profile_twitter);
    $config[] = settings_format("profile_facebook", $profile_facebook);
    $config[] = settings_format("profile_email", $profile_email);
    $config[] = settings_format("site_analytics", $site_analytics);
    $config[] = settings_format("username", $username);
    $config[] = settings_format("email", $email);
    $config[] = "\$password = '".$password."';";
    
    // Create the settings file.
    file_put_contents($settings_file, implode("\n", $config));
    
    // Get subdirectory for the .htaccess below.
    $dir .= str_replace('cinematico/save.php', '', $_SERVER["REQUEST_URI"]);
    
    // Generate the .htaccess file on initial setup only.
    if (!file_exists($htaccess_file)) {
    
        // Parameters for the htaccess file.
        $htaccess[] = "# Pretty Permalinks";
        $htaccess[] = "RewriteRule ^(images)($|/) - [L]";
        $htaccess[] = "RewriteCond %{REQUEST_URI} !^action=logout [NC]";
        $htaccess[] = "RewriteCond %{REQUEST_URI} !^action=login [NC]";
        $htaccess[] = "Options +FollowSymLinks -MultiViews";
        $htaccess[] = "RewriteEngine on";
        $htaccess[] = "RewriteBase " . $dir;
        $htaccess[] = "RewriteCond %{REQUEST_URI} !index\.php";
        $htaccess[] = "RewriteCond %{REQUEST_FILENAME} !-f";
        $htaccess[] = "RewriteRule ^(.*)$ index.php?filename=$1 [NC,QSA,L]";
    
        // Generate the .htaccess file.
        file_put_contents($htaccess_file, implode("\n", $htaccess));
    }
    
    // Redirect
    if (file_exists($settings_file)) {
        // Redirect to the settings page.
        header("Location: " . $site_url . '/settings');
    } else {
        // Redirect to the site.
        header("Location: " . $site_url); 
    }
}

?>
