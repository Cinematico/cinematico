<?php

if (file_exists('config.php')) {

/*-----------------------------------------------------------------------------------*/
/* Network Installation Settings
/*-----------------------------------------------------------------------------------*/

$network_install = false;
$network_url = 'http://localhost/cinematico';

/*-----------------------------------------------------------------------------------*/
/* Setup
/*-----------------------------------------------------------------------------------*/

include('config.php');

// Get the base directory.
if ($network_install) {
    $base_url = $network_url;
    $base_dir = '../';
} else {
    $base_url = $site_url;  
    $base_dir = '';  
}

// Global definitions.
define('BASE_DIR', $base_dir);
define('BASE_URL', $base_url);
define('SITE_THEME', $site_theme);
define('SITE_URL', $site_url);
define('SITE_TITLE', $site_title);
define('SITE_DESCRIPTION', $site_description);
define('VIDEO_SERVICE', $video_service);
define('YOUTUBE_USERNAME', $youtube_username);
define('YOUTUBE_DISPLAY', $youtube_display);
define('YOUTUBE_CHANNEL', $youtube_channel);
define('YOUTUBE_PLAYLIST', $youtube_playlist);
define('YOUTUBE_FEATURED_VIDEO', $youtube_featured_video);
define('VIMEO_USERNAME', $vimeo_username);
define('VIMEO_DISPLAY', $vimeo_display);
define('VIMEO_CHANNEL', $vimeo_channel);
define('VIMEO_FEATURED_VIDEO', $vimeo_featured_video);
define('GALLERY_ITEMS_NUMBER', $gallery_items_number);
define('SITE_ANALYTICS', $site_analytics);

// Set the active theme url.
$theme_url = BASE_URL . '/themes/' . SITE_THEME;

// Set the tools url.
$tools_url = BASE_URL . '/cinematico';

// Set the assets url.
$assets_url = BASE_URL . '/cinematico/assets';

/*-----------------------------------------------------------------------------------*/
/* Includes
/*-----------------------------------------------------------------------------------*/

// For plugins.
include(BASE_DIR . 'cinematico/includes/actions.php');

// For RSS feeds.
include(BASE_DIR . 'cinematico/includes/feedwriter.php');

// All Cinematico functions.
include(BASE_DIR . 'cinematico/includes/functions.php');

/*-----------------------------------------------------------------------------------*/
/* Page Setup
/*-----------------------------------------------------------------------------------*/

// The home page.
if (is_page('home')) {
    
    // Get & define the video ID.
    if (VIDEO_SERVICE == 'youtube') {
        $the_video_id = YOUTUBE_FEATURED_VIDEO;
    } elseif (VIDEO_SERVICE == 'vimeo') {
        $the_video_id = VIMEO_FEATURED_VIDEO;
    }
    define('THE_VIDEO_ID', $the_video_id);
    
    // Set the page title and description.
    $page_title = SITE_TITLE;
    $meta_description = SITE_DESCRIPTION;
    
    // Variable setup for pagination.
    $page_number = $_GET['page'];
    define('PAGE_NUMBER', $page_number);

// Single video pages.    
} elseif (is_page('single')) {
    
    // Get & define the video ID.
    $the_current_location = $_SERVER["REQUEST_URI"];
    $the_video_id = end((explode('/', $the_current_location)));
    define('THE_VIDEO_ID', $the_video_id);
    
    // Set the page title and description.
    $page_title = get_video('title', $the_video_id, '');
    $meta_description = get_video('description', $the_video_id, '');
    
    // Variable setup for pagination.
    $page_number = $_GET['page'];
    define('PAGE_NUMBER', $page_number);
    
// About page.    
} elseif (is_page('about')) {
    
    // Set the page title and description.
    $page_title = $about_title;
    $meta_description = $about_text;
    
// 404 page.    
} else {
    
    // Set the page title and description.
    $page_title = $not_found_title;
    $meta_description = $not_found_text;
}

/*-----------------------------------------------------------------------------------*/
/* The Page Meta
/*-----------------------------------------------------------------------------------*/

if ($site_image) {
    $site_meta_image = $site_image;
} else {
    $site_meta_image = $site_url . '/cinematico/assets/images/logo.jpg';
}

// Get the page description and author meta.
$page_meta[] = '<meta name="description" content="' . $meta_description . '">';
$page_meta[] = '<meta name="author" content="' . $page_title . '">';

// Get the Twitter card meta.
$page_meta[] = '<meta name="twitter:card" content="summary">';
$page_meta[] = '<meta name="twitter:site" content="' . $site_title . '">';
$page_meta[] = '<meta name="twitter:site:id" content="' . $profile_twitter . '">';
$page_meta[] = '<meta name="twitter:title" content="' . $page_title . '">';
$page_meta[] = '<meta name="twitter:description" content="' . $meta_description . '">';
$page_meta[] = '<meta name="twitter:creator" content="' . $profile_name . '">';
$page_meta[] = '<meta name="twitter:domain" content="' . $site_url . '">';
$page_meta[] = '<meta name="twitter:image:src" content="' . $site_meta_image . '">';

// Get the Open Graph meta.
$page_meta[] = '<meta property="og:type" content="website">';
$page_meta[] = '<meta property="og:site_name" content="' . $site_title . '">';
$page_meta[] = '<meta property="og:title" content="' . $page_title . '">';
$page_meta[] = '<meta property="og:description" content="' . $meta_description . '">';
$page_meta[] = '<meta property="og:url" content="' .$site_url . '">';
$page_meta[] = '<meta property="og:image" content="' . $site_meta_image . '">';

// Get all page meta.
$page_meta = implode("\n", $page_meta);

/*-----------------------------------------------------------------------------------*/
/* Get the Selected Theme
/*-----------------------------------------------------------------------------------*/

if ($_GET['filename'] == 'settings') {
    
    // Get the settings index.
    include(BASE_DIR . 'cinematico/settings.php');
    
} else if ($_GET['filename'] == 'rss') {

    // RSS integration needs to happen here.

} else {
     
    // Get the theme index.
    include(BASE_DIR . 'themes/' . SITE_THEME . '/index.php');
    
}

/*-----------------------------------------------------------------------------------*/
/* Run the Setup if There's no Config File
/*-----------------------------------------------------------------------------------*/

} else {
    // Get the components of the current url.
    $protocol = @( $_SERVER["HTTPS"] != 'on') ? 'http://' : 'https://';
    $domain = $_SERVER["SERVER_NAME"];
    $port = $_SERVER["SERVER_PORT"];
    $path = $_SERVER["REQUEST_URI"];

    // Check if running on alternate port.
    if ($protocol === "https://") {
        if ($port == 443)
            $site_url = $protocol . $domain;
        else
            $site_url = $protocol . $domain . ":" . $port;
    } elseif ($protocol === "http://") {
        if ($port == 80)
            $site_url = $protocol . $domain;
        else
            $site_url = $protocol . $domain . ":" . $port;
    }

    $site_url .= $path;
    
    // Check if the install directory is writable.
    $is_writable = (TRUE == is_writable(dirname(__FILE__) . '/'));
    ?>
    
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        
        <!-- Page Meta -->
        <title>Install Cinematico</title>
        
        <!-- Tame the Viewport -->
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <!-- Google Fonts -->
        <link href="http://fonts.googleapis.com/css?family=Lato:300,400,700,400italic,700italic" rel="stylesheet" type="text/css">
        
        <!-- Theme Styles -->
        <link rel="stylesheet" href="<?php echo(rtrim($site_url, '/')); ?>/cinematico/assets/css/normalize.css">
        <link rel="stylesheet" href="<?php echo(rtrim($site_url, '/')); ?>/cinematico/assets/css/style.css">
        
        <!-- Favicon -->
        <link href="<?php echo(rtrim($site_url, '/')); ?>/cinematico/assets/images/favicon.png" rel="shortcut icon">
    </head>
    
    <body>
        <div id="content">
            <div class="row header border">
                <div class="content">
                    <h1>Welcome</h1>
                    <p>Let’s get started.</p>
                </div>
            </div>
            
            <form id="settings" method="post" action="./cinematico/save.php">
                <input type="hidden" name="site_url" id="site_url" value="<?php echo(rtrim($site_url, '/')); ?><?php if ($site_url == $domain) { ?>/<?php } ?>">
                <input type="hidden" name="site_title" id="site_title" value="Welcome to Cinematico">
                <input type="hidden" name="site_description" id="site_description" value="The easiest way to create an exceptional website for your videos.">
                <input type="hidden" name="video_service" id="video_service" value="youtube">
                <input type="hidden" name="youtube_username" id="youtube_username" value="cinematico">
                <input type="hidden" name="youtube_display" id="youtube_display" value="playlist">
                <input type="hidden" name="youtube_playlist" id="youtube_playlist" value="PLwfMsiHMs_Z1XtTQClWxs9xwsWjLdBAE3">
                <input type="hidden" name="site_theme" id="focus" value="focus" />
                <input type="hidden" name="gallery_title" id="gallery_title" value="Getting Started">
                <input type="hidden" name="gallery_description" id="gallery_description" value="A short series of videos to help you get started.">
                <input type="hidden" name="gallery_items_number" id="gallery_items_number" value="6">
                <input type="hidden" name="footer_text" id="footer_text" value="Copyright &copy; <?php echo date("Y") ?> Cinematico">
                <input type="hidden" name="about_title" id="about_title" value="My About Title">
                <input type="hidden" name="about_text" id="about_text" value="This text is displayed on your “about” page. Write a little something about yourself.">
                <input type="hidden" name="not_found_title" id="not_found_title" value="Not Found">
                <input type="hidden" name="not_found_text" id="not_found_text" value="Sorry, but what you're looking for isn't here.">
                <input type="hidden" name="profile_name" id="profile_name" value="Cinematico">
                <input type="hidden" name="profile_twitter" id="profile_twitter" value="TryCinematico">
                
                <div class="row border">
                    <div class="content">
                        <h2>Your Account</h2>
                        <p>Set your account username, email and password.</p>
                        
                        <h3>Username</h3>
                        <div class="icon-edit">
                            <input type="text" name="username" id="username" placeholder="username" required>
                        </div>
                        
                        <h3>Email</h3>
                        <div class="icon-edit">
                            <input type="email" name="email" id="email" placeholder="e.g. you@youremail.com" required>
                        </div>
                        
                        <h3>Password</h3>
                        <div class="icon-edit">
                            <input type="password" name="password" id="password" value="" placeholder="&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;" required>
                        </div>
                    </div>
                </div>
                
                <button class="icon-check submit" type="submit" name="submit" value="submit"></button>
            </form>
            
            <div class="row footer">
                <div class="content">
                    <p><a href="http://cinemati.co" target="_blank">Powered by Cinematico</a></p>
                </div>
            </div>
        </div>
    </body>
    </html>

<?php 

/*-----------------------------------------------------------------------------------*/
/* That's All There is to It
/*-----------------------------------------------------------------------------------*/

}

?>