<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    
    <!-- Page Meta -->
    <title><?php get_text($page_title); ?></title>
    <?php get_meta($page_meta); ?>
    
    <!-- Tame the Viewport -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Google Fonts -->
    <link href="http://fonts.googleapis.com/css?family=Lato:300,400,700,400italic,700italic" rel="stylesheet" type="text/css">
    <link href="http://fonts.googleapis.com/css?family=Satisfy" rel="stylesheet" type="text/css">
    
    <!-- Theme Styles -->
    <link rel="stylesheet" href="<?php get_setting($theme_url); ?>/assets/css/normalize.css">
    <link rel="stylesheet" href="<?php get_setting($theme_url); ?>/assets/css/style.css">
    
    <!-- Favicon -->
    <?php if (is_image('favicon')) { ?>
    <link href="<?php get_setting($site_url); ?>/uploads/favicon.png" rel="shortcut icon">
    <?php } else { ?>
    <link href="<?php get_setting($assets_url); ?>/images/favicon.png" rel="shortcut icon">
    <?php } ?>
    
    <?php get_includes('header'); ?>
</head>

<body class="<?php get_body_class($video_service); ?>">
    <nav id="menu">
        <div class="section">
            <a class="icon-close close" href="#menu"></a>
            
            <h3><?php get_text($site_title); ?></h3>
            <p><?php get_text($site_description); ?></p>
        </div>
        
        <div class="section">
            <ul class="filter">
                <li class="<?php if (is_page('home')) { echo('current'); } ?>"><a href="<?php get_setting($site_url); ?>/">Home</a></li>
                <li class="<?php if (is_page('about')) { echo('current'); } ?>"><a href="<?php get_setting($site_url); ?>/about">About</a></li>
            </ul>
        </div>
        
        <div class="section">
            <ul>
                <?php if (is_setting($profile_facebook)) { ?>
                <li><a class="icon-facebook" href="http://facebook.com/<?php get_setting($profile_facebook); ?>">Facebook</a></li>
                <?php } ?>
                <?php if (is_setting($profile_twitter)) { ?>
                <li><a class="icon-twitter" href="http://twitter.com/<?php get_setting($profile_twitter); ?>">Twitter</a></li>
                <?php } ?>
            </ul>
        </div>
    </nav>
    
    <?php if (is_page('home') || is_page('single')) {
        
        // The Video Gallery Template
        get_template_part('content-gallery');
        
    } elseif (is_page('about')) {
    
        // The About Page Template
        get_template_part('content-about');
        
    } else {
        
        // The 404 Page Template
        get_template_part('content-404');
        
    } ?>
    
    <?php get_includes('footer'); ?>
    
    <script src="<?php get_setting($theme_url); ?>/assets/js/app.js"></script>
    
    <?php get_analytics(); ?>
</body>
</html>