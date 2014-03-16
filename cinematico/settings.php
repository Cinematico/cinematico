<?php

session_start();

/*-----------------------------------------------------------------------------------*/
/* Include 3rd Party Functions
/*-----------------------------------------------------------------------------------*/

include('includes/phpass.php');

/*-----------------------------------------------------------------------------------*/
/* User Machine
/*-----------------------------------------------------------------------------------*/

// Password hashing via phpass.
$hasher  = new PasswordHash(8,FALSE);

if (isset($_GET['action']))
{
    $action = $_GET['action'];
    switch ($action)
    {

        // Logging in.
        case 'login':
            if ((isset($_POST['username'])) && (isset($_POST['password'])) && ($_POST['username']===$username) && $hasher->CheckPassword($_POST['password'], $password)) {
                $_SESSION['user'] = true;

                // Redirect if authenticated.
                header('Location: ' . $site_url . '/settings');
            } else {
            
                // Display error if not authenticated.
                $login_error = 'Nope, try again or <a href="?action=forgot">reset your password</a>.';
            }
        break;

        // Logging out.
        case 'logout':
            session_unset();
            session_destroy();

            // Redirect to dashboard on logout.
            header('Location: ' . $site_url . '/settings');
        break;
        
        // Fogot password.
        case 'forgot':
            
            // The verification file.
            $verification_file = "./verify.php";
            
            // If verified, allow a password reset.
            if (!isset($_GET["verify"])) {
            
                $code = sha1(md5(rand()));

                $verify_file_contents[] = "<?php";
                $verify_file_contents[] = "\$verification_code = \"" . $code . "\";";
                file_put_contents($verification_file, implode("\n", $verify_file_contents));

                $recovery_url = sprintf("%s/index.php?action=forgot&verify=%s,", $site_url, $code);
                $message      = sprintf("To reset your password go to: %s", $recovery_url);

                $headers[] = "From: " . $email;
                $headers[] = "Reply-To: " . $email;
                $headers[] = "X-Mailer: PHP/" . phpversion();

                mail($email, $site_title . " - Recover your Cinematico Password", $message, implode("\r\n", $headers));
                $login_error = "Details on how to recover your password have been sent to your email.";
            
            // If not verified, display a verification error.   
            } else {

                include($verification_file);

                if ($_GET["verify"] == $verification_code) {
                    $_SESSION["user"] = true;
                    unlink($verification_file);
                } else {
                    $login_error = "That's not the correct recovery code!";
                }
            }
        break;
        
        // Invalidation            
        case 'invalidate':
            if (!$_SESSION['user']) {
                $login_error = 'Nope, try again!';
            } else {
                if (!file_exists($upload_dir . 'cache/')) {
                    return;
                }
                
                $files = glob($upload_dir . 'cache/*');
                
                foreach ($files as $file) {
                    if (is_file($file))
                        unlink($file);
                }
            }
            
            header('Location: ' . './');
        break;
    }
    
}

/*-----------------------------------------------------------------------------------*/
/* Begin the Settings Page
/*-----------------------------------------------------------------------------------*/

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    
    <!-- Page Meta -->
    <title>Settings</title>
    
    <!-- Tame the Viewport -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Google Fonts -->
    <link href="http://fonts.googleapis.com/css?family=Lato:300,400,700,400italic,700italic" rel="stylesheet" type="text/css">
    <link href='http://fonts.googleapis.com/css?family=Merriweather:400,700' rel='stylesheet' type='text/css'>
    
    <!-- Theme Styles -->
    <link rel="stylesheet" href="<?php echo($tools_url); ?>/assets/css/normalize.css">
    <link rel="stylesheet" href="<?php echo($tools_url); ?>/assets/css/style.css">
    
    <!-- Favicon -->
    <link href="<?php echo($tools_url); ?>/assets/images/favicon.png" rel="shortcut icon">
</head>

<body>
    <div class="bar"></div>
    <div id="content">
        <?php if (!isset($_SESSION['user'])) { ?>
        
        <div class="row header border">
            <div class="content">
                <h1>Welcome Back</h1>
                <p>A welcome back message.</p>
            </div>
        </div>
        
        <form method="post" action="?action=login">
            <div class="row border">
                <div class="content">
                    <h2>Sign In</h2>
                    <p>Sign in to modify your settings.</p>
                    
                    <h3>Username</h3>
                    <div class="icon-edit">
                        <input type="text" name="username" id="username" placeholder="username" required>
                    </div>
                    
                    <h3>Password</h3>
                    <div class="icon-edit">
                        <input type="password" name="password" id="password" placeholder="&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;" required>
                    </div>
                    
                    <?php if (isset($login_error)) { ?>
                        <p class="notify"><?php echo($login_error); ?></p>    
                    <?php }; ?>
                </div>
            </div>
            
            <button class="icon-check submit" type="submit" name="submit" value="submit"></button>
        </form>
        
        <div class="buttons">
        
        <?php } else { ?>
        
        <div class="row header border">
            <div class="content">
                <h1>Settings</h1>
                <p>Customize your site design, text, videos and more.</p>
            </div>
        </div>
        
        <form id="settings" method="post" action="./cinematico/save.php" enctype="multipart/form-data">
            <div class="row border">
                <div class="content">
                    <h2>Site Title &amp; Description</h2>
                    <p>Your site title and description options.</p>
                    
                    <h3>Site Title</h3>
                    <div class="icon-edit">
                        <input type="text" name="site_title" id="site_title" value="<?php get_text($site_title); ?>"  placeholder="No pressure.">
                    </div>
                    
                    <h3>Site Description</h3>
                    <div class="icon-edit">
                        <textarea name="site_description" id="site_description" placeholder="Something short and to the point."><?php get_text($site_description); ?></textarea>
                    </div>
                </div>
            </div>
            
            <div id="site-logo" class="row border">
                <div class="content">
                    <h2>Site Images</h2>
                    <p>Your site background image, logo and favicon options.</p>
                        
                    <?php if (!file_exists('./uploads/logo.png')) { ?>
                    <h3>Site Logo</h3>
                    <label class="button choose" for="site_logo">Choose a Logo Image</label>
                    <?php } else { ?>
                    <div class="row images">
                        <h3>Current Logo</h3>
                        <div class="frame logo">
                            <img src="./uploads/logo.png" />
                            <input type="checkbox" name="remove_site_logo" id="remove_site_logo" value="remove_site_logo"> 
                            <label class="icon-trash remove" for="remove_site_logo"></label>
                        </div>
                    </div>
                    
                    <h3>Update Your Logo</h3>
                    <label class="button choose" for="site_logo">Choose a Logo Image</label>
                    <?php } ?>
                    <input type="file" name="site_logo" id="site_logo">
                    
                    <?php if (!file_exists('./uploads/favicon.png')) { ?>
                    <h3>Site Favicon</h3>
                    <label class="button choose" for="site_favicon">Choose a Favicon Image</label>
                    <?php } else { ?>
                    <div class="row images">
                        <h3>Current Favicon</h3>
                        <div class="frame favicon">
                            <img src="./uploads/favicon.png" />
                            <input type="checkbox" name="remove_site_favicon" id="remove_site_favicon" value="remove_site_favicon"> 
                            <label class="icon-trash remove" for="remove_site_favicon"></label>
                        </div>
                    </div>
                    
                    <h3>Update Your Favicon</h3>
                    <label class="button choose" for="site_favicon">Choose a Logo Image</label>
                    <?php } ?>
                    <input type="file" name="site_favicon" id="site_favicon">
                    
                    <?php if (!file_exists('./uploads/background.jpg')) { ?>
                    <h3>Site Background</h3>
                    <label class="button choose" for="site_background">Choose a Background Image</label>
                    <?php } else { ?>
                    <div class="row images">
                        <h3>Current Background</h3>
                        <div class="frame background">
                            <img src="./uploads/background.jpg" />
                            <input type="checkbox" name="remove_site_background" id="remove_site_background" value="remove_site_background"> 
                            <label class="icon-trash remove" for="remove_site_background"></label>
                        </div>
                    </div>
                    
                    <h3>Update Your Background</h3>
                    <label class="button choose" for="site_background">Choose a Logo Image</label>
                    <?php } ?>
                    <input type="file" name="site_background" id="site_background">
                </div>
            </div>
            
            <div class="row border">
                <div class="content">
                    <h2>Video Service Options</h2>
                    <p>Your video service options and display preferences.</p>
                    
                    <h3>Your Video Service</h3>
                    <div id="video-service-selection">
                        <input type="radio" name="video_service" id="youtube" value="youtube" data-rel="video-service-youtube" <?php if($video_service == 'youtube') { ?>checked<?php } ?> />
                        <label class="button" for="youtube">YouTube</label>
                        <input type="radio" name="video_service" id="vimeo" value="vimeo" data-rel="video-service-vimeo" <?php if($video_service == 'vimeo') { ?>checked<?php } ?> />
                        <label class="button" for="vimeo">Vimeo</label>
                    </div>
                    
                    <div id="video-service-youtube" class="video-service selection<?php if($video_service == 'youtube') { ?> selected<?php } ?>">
                        <h3>Username</h3>
                        <div class="icon-edit">
                            <input type="text" name="youtube_username" id="youtube_username" placeholder="Your YouTube username." value="<?php get_text($youtube_username); ?>">
                        </div>
                        
                        <h3>Video Source</h3>
                        <div id="youtube-display-selection">
                            <input type="radio" name="youtube_display" id="youtube_user" value="user" <?php if($youtube_display == 'user') { ?>checked<?php } ?> />
                            <label class="button" for="youtube_user">User</label>
                            <input type="radio" name="youtube_display" id="youtube_channel" value="channel" data-rel="youtube-channel" <?php if($youtube_display == 'channel') { ?>checked<?php } ?> />
                            <label class="button" for="youtube_channel">Channel</label>
                            <input type="radio" name="youtube_display" id="youtube_playlist" value="playlist" data-rel="youtube-playlist" <?php if($youtube_display == 'playlist') { ?>checked<?php } ?> />
                            <label class="button" for="youtube_playlist">Playlist</label>
                        </div>
                        
                        <div id="youtube-channel" class="youtube-display selection<?php if($youtube_display == 'channel') { ?> selected<?php } ?>">
                            <h3>Channel ID</h3>
                            <div class="icon-edit">
                                <input type="text" name="youtube_channel" id="youtube_channel" placeholder="A valid YouTube channel ID." value="<?php get_text($youtube_channel); ?>">
                            </div>
                        </div>
                        
                        <div id="youtube-playlist" class="youtube-display selection<?php if($youtube_display == 'playlist') { ?> selected<?php } ?>">
                            <h3>Playlist ID</h3>
                            <div class="icon-edit">
                                <input type="text" name="youtube_playlist" id="youtube_playlist" placeholder="A valid YouTube playlist ID." value="<?php get_text($youtube_playlist); ?>">
                            </div>
                        </div>
                        
                        <h3>Featured Video ID</h3>
                        <div class="icon-edit">
                            <input type="text" name="youtube_featured_video" id="youtube_featured_video" placeholder="A valid YouTube video ID." value="<?php get_text($youtube_featured_video); ?>">
                        </div>
                    </div>
        
                    <div id="video-service-vimeo" class="video-service selection<?php if($video_service == 'vimeo') { ?> selected<?php } ?>">
                        <h3>Username</h3>
                        <div class="icon-edit">
                            <input type="text" name="vimeo_username" id="vimeo_username" placeholder="Your Vimeo username." value="<?php get_text($vimeo_username); ?>">
                        </div>
                        
                        <h3>Video Source</h3>
                        <div id="vimeo-display-selection">
                            <input type="radio" name="vimeo_display" id="vimeo_user" value="user" <?php if($vimeo_display == 'user') { ?>checked<?php } ?> />
                            <label class="button" for="vimeo_user">User</label>
                            <input type="radio" name="vimeo_display" id="vimeo_channel" value="channel" data-rel="vimeo-channel" <?php if($vimeo_display == 'channel') { ?>checked<?php } ?> />
                            <label class="button" for="vimeo_channel">Channel</label>
                            <input type="radio" name="vimeo_display" id="vimeo_album" value="album" data-rel="vimeo-album" <?php if($vimeo_display == 'album') { ?>checked<?php } ?> />
                            <label class="button" for="vimeo_album">Album</label>
                        </div>
                        
                        
                        <div id="vimeo-channel" class="vimeo-display selection<?php if($vimeo_display == 'channel') { ?> selected<?php } ?>">
                            <h3>Channel ID</h3>
                            <div class="icon-edit">
                                <input type="text" name="vimeo_channel" id="vimeo_channel" placeholder="A valid Vimeo channel ID." value="<?php get_text($vimeo_channel); ?>">
                            </div>
                        </div>
                        
                        
                        <div id="vimeo-album" class="vimeo-display selection<?php if($vimeo_display == 'album') { ?> selected<?php } ?>">
                            <h3>Album ID</h3>
                            <div class="icon-edit">
                                <input type="text" name="vimeo_album" id="vimeo_album" placeholder="A valid Vimeo album ID." value="<?php get_text($vimeo_album); ?>">
                            </div>
                        </div>
                        
                        <h3>Featured Video ID</h3>
                        <div class="icon-edit">
                            <input type="text" name="vimeo_featured_video" id="vimeo_featured_video" placeholder="A valid Vimeo video ID." value="<?php get_text($vimeo_featured_video); ?>">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row border">
                <div class="content">
                    <h2>Site Theme</h2>
                    <p>Choose an existing theme or install new themes. There’s a growing library of new themes that can be purchased on the <a href="http://cinemati.co/themes" target="_blank">Cinemati.co marketplace</a>.</p>
                    
                    <div class="row images">
                        <h3>Choose a Theme</h3>
                        
                        <?php {
                        
                            // The themes directory.
                            $themes_directory = './themes/';
                        
                            // Get all themes in the themes directory.
                            $available_themes = glob($themes_directory . '*');
                            
                            // Loop through the available themes.
                            foreach ($available_themes as $theme):
                        
                                // Generate template names.
                                $theme_dir_name = substr($theme, 9);
                        
                                // Template screenshots.
                                $theme_screenshot = '' . $themes_directory . '' . $theme_dir_name . '/screenshot.jpg';
                                
                                { ?>
                                <div class="frame themes">
                                    <input type="radio" name="site_theme" id="<?php echo($theme_dir_name); ?>" value="<?php echo($theme_dir_name); ?>" <?php if($site_theme == $theme_dir_name) { ?>checked<?php } ?> />
                                    <label class="theme" for="<?php echo($theme_dir_name); ?>"><img src="<?php echo($theme_screenshot); ?>" /></label>
                                    
                                    <?php if($site_theme !== $theme_dir_name) { ?>
                                    <input type="radio" name="remove_theme" id="remove_theme" value="<?php echo($theme_dir_name); ?>"> 
                                    <label class="icon-trash remove" for="remove_theme"></label>
                                    <?php } ?>
                                </div>
                                <?php }
                                
                            // End the theme loop.    
                            endforeach;
                        } ?>
                    </div>
                    
                    <h3>Install New Themes</h3>
                    <label class="button choose" for="theme_upload">Upload a Theme</label>
                    <input type="file" name="theme_upload" id="theme_upload">
                </div>
            </div>
            
            <div class="row border">
                <div class="content">
                    <h2>Video Gallery Options</h2>
                    <p>Your video gallery text &amp; options.</p>
                    
                    <h3>Gallery Title</h3>
                    <div class="icon-edit">
                        <input type="text" name="gallery_title" id="gallery_title" value="<?php get_text($gallery_title); ?>" placeholder="e.g. Featured &amp; Popular">
                    </div>
                    
                    <h3>Gallery Description</h3>
                    <div class="icon-edit">
                        <textarea name="gallery_description" id="gallery_description" placeholder="Check out our featured and popular videos."><?php get_text($gallery_description); ?></textarea>
                    </div>
                    
                    <h3>Videos Per Page</h3>
                    <div class="button-group">
                        <input type="radio" name="gallery_items_number" id="1" value="1" <?php if($gallery_items_number == '1') { ?>checked<?php } ?> />
                        <label class="button" for="1">1</label>
                        <input type="radio" name="gallery_items_number" id="2" value="2" <?php if($gallery_items_number == '2') { ?>checked<?php } ?> />
                        <label class="button" for="2">2</label>
                        <input type="radio" name="gallery_items_number" id="3" value="3" <?php if($gallery_items_number == '3') { ?>checked<?php } ?> />
                        <label class="button" for="3">3</label>
                        <input type="radio" name="gallery_items_number" id="4" value="4" <?php if($gallery_items_number == '4') { ?>checked<?php } ?> />
                        <label class="button" for="4">4</label>
                        <input type="radio" name="gallery_items_number" id="5" value="5" <?php if($gallery_items_number == '5') { ?>checked<?php } ?> />
                        <label class="button" for="5">5</label>
                        <input type="radio" name="gallery_items_number" id="6" value="6" <?php if($gallery_items_number == '6') { ?>checked<?php } ?> />
                        <label class="button" for="6">6</label>
                        <input type="radio" name="gallery_items_number" id="7" value="7" <?php if($gallery_items_number == '7') { ?>checked<?php } ?> />
                        <label class="button" for="7">7</label>
                        <input type="radio" name="gallery_items_number" id="8" value="8" <?php if($gallery_items_number == '8') { ?>checked<?php } ?> />
                        <label class="button" for="8">8</label>
                        <input type="radio" name="gallery_items_number" id="9" value="9" <?php if($gallery_items_number == '9') { ?>checked<?php } ?> />
                        <label class="button" for="9">9</label>
                        <input type="radio" name="gallery_items_number" id="10" value="10" <?php if($gallery_items_number == '10') { ?>checked<?php } ?> />
                        <label class="button" for="10">10</label>
                        <input type="radio" name="gallery_items_number" id="11" value="11" <?php if($gallery_items_number == '11') { ?>checked<?php } ?> />
                        <label class="button" for="11">11</label>
                        <input type="radio" name="gallery_items_number" id="12" value="12" <?php if($gallery_items_number == '12') { ?>checked<?php } ?> />
                        <label class="button" for="12">12</label>
                        <input type="radio" name="gallery_items_number" id="13" value="13" <?php if($gallery_items_number == '13') { ?>checked<?php } ?> />
                        <label class="button" for="13">13</label>
                        <input type="radio" name="gallery_items_number" id="14" value="14" <?php if($gallery_items_number == '14') { ?>checked<?php } ?> />
                        <label class="button" for="14">14</label>
                        <input type="radio" name="gallery_items_number" id="15" value="15" <?php if($gallery_items_number == '15') { ?>checked<?php } ?> />
                        <label class="button" for="15">15</label>
                        <input type="radio" name="gallery_items_number" id="16" value="16" <?php if($gallery_items_number == '16') { ?>checked<?php } ?> />
                        <label class="button" for="16">16</label>
                        <input type="radio" name="gallery_items_number" id="17" value="17" <?php if($gallery_items_number == '17') { ?>checked<?php } ?> />
                        <label class="button" for="17">17</label>
                        <input type="radio" name="gallery_items_number" id="18" value="18" <?php if($gallery_items_number == '18') { ?>checked<?php } ?> />
                        <label class="button" for="18">18</label>
                        <input type="radio" name="gallery_items_number" id="19" value="19" <?php if($gallery_items_number == '19') { ?>checked<?php } ?> />
                        <label class="button" for="19">19</label>
                        <input type="radio" name="gallery_items_number" id="20" value="20" <?php if($gallery_items_number == '20') { ?>checked<?php } ?> />
                        <label class="button" for="20">20</label>
                    </div>
                </div>
            </div>
            
            <div class="row border">
                <div class="content">
                    <h2>Footer Text</h2>
                    <p>Your footer text settings.</p>
                    
                    <h3>Footer Text</h3>
                    <div class="icon-edit">
                        <input type="text" name="footer_text" id="footer_text" value="<?php get_text($footer_text); ?>" placeholder="e.g. Copyright &copy; <?php echo date("Y") ?> Your Business">
                    </div>
                </div>
            </div>
            
            <div class="row border">
                <div class="content">
                    <h2>Your About Page</h2>
                    <p>Your about page settings.</p>
                    
                    <h3>About Page Title</h3>
                    <div class="icon-edit">
                        <input type="text" name="about_title" id="about_title" value="<?php get_text($about_title); ?>" placeholder="e.g. About">
                    </div>
                    
                    <h3>About Page Text</h3>
                    <div class="icon-edit">
                        <textarea name="about_text" id="about_text" rows="1" placeholder="This text is displayed on your “about” page. Write a little something about yourself."><?php get_text($about_text); ?></textarea>
                    </div>
                </div>
            </div>
            
            <div class="row border">
                <div class="content">
                    <h2>404 “Not Found” Page</h2>
                    <p>Your 404 “Not Found” page settings.</p>
                    
                    <h3>Not Found Page Title</h3>
                    <div class="icon-edit">
                        <input type="text" name="not_found_title" id="not_found_title" value="<?php get_text($not_found_title); ?>" placeholder="e.g. Not Found">
                    </div>
                    
                    <h3>Not Found Page Text</h3>
                    <div class="icon-edit">
                        <textarea name="not_found_text" id="not_found_text" rows="1" placeholder="Sorry, but what you're looking for isn't here."><?php get_text($not_found_text); ?></textarea>
                    </div>
                </div>
            </div>
            
            <div class="row border">
                <div class="content">
                    <h2>Profile Settings</h2>
                    <p>Your profile settings.</p>
                    
                    <h3>Your Name or Company Name</h3>
                    <div class="icon-edit">
                        <input type="text" name="profile_name" id="profile_name" value="<?php get_setting($profile_name); ?>" placeholder="e.g. Cinematico">
                    </div>
                    
                    <h3>Twitter Username</h3>
                    <div class="icon-edit">
                        <input type="text" name="profile_twitter" id="profile_twitter" value="<?php get_setting($profile_twitter); ?>" placeholder="e.g. TryCinematico">
                    </div>
                    
                    <h3>Facebook Username</h3>
                    <div class="icon-edit">
                        <input type="text" name="profile_facebook" id="profile_facebook" value="<?php get_setting($profile_facebook); ?>" placeholder="e.g. cinematico">
                    </div>
                    
                    <h3>Email</h3>
                    <div class="icon-edit">
                        <input type="email" name="profile_email" id="profile_email" value="<?php get_setting($profile_email); ?>" placeholder="e.g. public-contact@email.com">
                    </div>
                </div>
            </div>
            
            <div class="row border">
                <div class="content">
                    <h2>Google Analytics</h2>
                    <p>Your Google Analytics settings.</p>
                    
                    <h3>Analytics ID</h3>
                    <div class="icon-edit">
                        <input type="text" name="site_analytics" id="site_analytics" value="<?php get_setting($site_analytics); ?>" placeholder="e.g. AB-1234567-89">
                    </div>
                </div>
            </div>
            
            <div class="row border">
                <div class="content">
                    <h2>Your Account</h2>
                    <p>Change your account settings.</p>
                    
                    <h3>Username</h3>
                    <div class="icon-edit">
                        <input type="text" name="username" id="username" value="<?php get_setting($username); ?>" placeholder="username">
                    </div>
                    
                    <h3>Email</h3>
                    <div class="icon-edit">
                        <input type="email" name="email" id="email" value="<?php get_setting($email); ?>" placeholder="e.g. you@youremail.com">
                    </div>
                    
                    <h3>Password</h3>
                    <div class="icon-edit">
                        <input type="password" name="password" id="password" value="" placeholder="&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;">
                    </div>
                </div>
            </div>
            
            <button class="icon-check submit" type="submit" name="submit" value="submit"></button>
        </form>
        
        <div class="buttons">
            <a class="icon-out" href="?action=logout"></a>
        
        <?php } ?>
        
            <a class="icon-preview" href="<?php get_setting($site_url); ?>" target="_blank"></a>
        </div>
        
        <div class="row footer">
            <div class="content">
                <p><a href="http://cinemati.co" target="_blank">Powered by Cinematico - <?php get_cinematico_version(); ?></a></p>
            </div>
        </div>
    </div>
    
    <script src="http://code.jquery.com/jquery-2.0.3.min.js"></script>
    <script src="<?php echo($tools_url); ?>/assets/js/app.js"></script>
</body>
</html>