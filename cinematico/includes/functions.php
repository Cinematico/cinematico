<?php

/*-----------------------------------------------------------------------------------*/
/* Get Single Video Function
/*-----------------------------------------------------------------------------------*/

function get_video($type, $template) {
    
    // Get YouTube video meta.
    if (VIDEO_SERVICE == 'youtube') {
        
        // The YouTube API.
        $the_video_meta = json_decode(file_get_contents('http://gdata.youtube.com/feeds/api/videos/' . THE_VIDEO_ID . '?v=2&alt=jsonc'));
        
        // The video meta.
        $the_video_thumbnail = $the_video_meta->data->thumbnail->hqDefault;
        $the_video_title = $the_video_meta->data->title;
        $the_video_description = $the_video_meta->data->description;
        $the_video_embed = '<iframe src="//www.youtube.com/embed/' . THE_VIDEO_ID . '?rel=0" frameborder="0" allowfullscreen></iframe>';
        
    // Get Vimeo video meta.
    } elseif (VIDEO_SERVICE == 'vimeo') {
    
        // The Vimeo API.
        $the_video_meta = json_decode(file_get_contents('http://vimeo.com/api/v2/video/' . THE_VIDEO_ID . '.json'));
        
        // The video meta.
        $the_video_thumbnail = $the_video_meta[0]->thumbnail_large;
        $the_video_title = $the_video_meta[0]->title;
        $the_video_description = $the_video_meta[0]->description;
        $the_video_embed = '<iframe src="//player.vimeo.com/video/' . THE_VIDEO_ID . '?title=0&amp;byline=0&amp;portrait=0" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
    }
    
    // Get the video permalink.
    $the_video_permalink = SITE_URL . '/' . THE_VIDEO_ID;
    
    // Is this video currently playing?
    if (THE_VIDEO_ID == THE_VIDEO_ID) {
        $the_video_status = 'current';
    } else {
        $the_video_status = 'queued';
    }
    
    if ($type == 'all') {
        
        // Get the posts file.
        include(BASE_DIR . 'themes/' . SITE_THEME . '/' . $template . '.php');
    }
    
    if ($type == 'title') {
        return $the_video_title;
    }
    
    if ($type == 'description') {
        return $the_video_description;
    }
    
    if ($type == 'thumbnail') {
        echo $the_video_thumbnail;
    }
}

/*-----------------------------------------------------------------------------------*/
/* Get All Videos Function
/*-----------------------------------------------------------------------------------*/

function get_videos($type, $template) {
    
    // Variable setup for pagination.
    if (isset($_GET['page'])) {
    $page_number = PAGE_NUMBER;
    }
    $page_increment = GALLERY_ITEMS_NUMBER;
    
    // If we're using YouTube.
    if (VIDEO_SERVICE == 'youtube') {
        
        // Variable setup for YouTube pagination.
        if (!empty($page_number)) {
            $start_index = $page_increment*$page_number+'1';
        } else {
            $start_index = '1';
        }
        
        // Get the YouTube API.
        if (YOUTUBE_DISPLAY == 'user') {
            $api = file_get_contents('http://gdata.youtube.com/feeds/api/users/' . YOUTUBE_USERNAME . '/uploads?v=2&alt=jsonc&start-index=' . $start_index . '&max-results=' . $page_increment);
        } elseif (YOUTUBE_DISPLAY == 'channel') {
            $api = file_get_contents('http://gdata.youtube.com/feeds/api/channels/' . YOUTUBE_CHANNEL . '/?v=2&alt=jsonc&start-index=' . $start_index . '&max-results=' . $page_increment);
        } elseif (YOUTUBE_DISPLAY == 'playlist') {
            $api = file_get_contents('http://gdata.youtube.com/feeds/api/playlists/' . YOUTUBE_PLAYLIST . '/?v=2&alt=jsonc&start-index=' . $start_index . '&max-results=' . $page_increment);
        }
        $the_video_meta = json_decode($api);
        $the_video_meta = $the_video_meta->data->items;
    
    // If we're using Vimeo.    
    } elseif (VIDEO_SERVICE == 'vimeo') {
    
        // Variable setup for Vimeo pagination.
        if (!empty($page_number)) {
            $start_index = $page_number+'1';
        } else {
            $start_index = '1';
        }
    
        // Get the Vimeo API.
        if (VIMEO_DISPLAY == 'user') {
            $api = file_get_contents('http://cinemati.co/api/vimeo-api.php?user_id=' . VIMEO_USERNAME . '&page=' . $start_index . '&per_page=' . $page_increment);
        } elseif (VIMEO_DISPLAY == 'channel') {
            $api = file_get_contents('http://cinemati.co/api/vimeo-api.php?channel_id=' . VIMEO_CHANNEL . '&user_id=' . VIMEO_USERNAME . '&page=' . $start_index . '&per_page=' . $page_increment);
        } elseif (VIMEO_DISPLAY == 'album') {
            $api = file_get_contents('http://cinemati.co/api/vimeo-api.php?album_id=' . VIMEO_ALBUM . '&user_id=' . VIMEO_USERNAME . '&page=' . $start_index . '&per_page=' . $page_increment);
        }
        $the_video_meta = json_decode($api);
    }
    
    if ($type == 'gallery') {
    
        // Begin the videos loop.
        foreach ($the_video_meta as $key => $row):
            
            // Get YouTube video meta.
            if (VIDEO_SERVICE == 'youtube') {
                
                // Username videos meta.
                if (YOUTUBE_DISPLAY == 'user') {
                    $the_video_id = $row->id;
                    $the_video_thumbnail = $row->thumbnail->hqDefault;
                    $the_video_title = $row->title;
                    $the_video_description = $row->description;
                    
                // Channel or playlist videos meta.    
                } elseif (YOUTUBE_DISPLAY == 'channel' || YOUTUBE_DISPLAY == 'playlist') {
                    $the_video_id = $row->video->id;
                    $the_video_thumbnail = $row->video->thumbnail->hqDefault;
                    $the_video_title = $row->video->title;
                    $the_video_description = $row->video->description;
                }
                
            // Get Vimeo video meta.
            } elseif (VIDEO_SERVICE == 'vimeo') {
                $the_video_id = $row->id;
                $the_video_thumbnail = $row->thumbnail;
                $the_video_title = $row->title;
                $the_video_description = $row->description;
            }
            
            // Get the video permalink.
            $the_video_permalink = SITE_URL . '/' . $the_video_id;
            
            // Is this video currently playing?
            if ($the_video_id == THE_VIDEO_ID) {
                $the_video_status = 'current';
            } else {
                $the_video_status = 'queued';
            }
            
            // Get the posts file.
            include(BASE_DIR . 'themes/' . SITE_THEME . '/' . $template . '.php');
        
        // End the videos loop.
        endforeach; 
    }
    
    if ($type == 'count') {
        $item_count = count($the_video_meta);
        return $item_count;
    }
}

/*-----------------------------------------------------------------------------------*/
/* Pagination Function
/*-----------------------------------------------------------------------------------*/

function get_pagination($page_previous_class, $page_next_class) {
    
    // Variable setup for pagination.
    if (isset($_GET['page'])) {
    $page_number = PAGE_NUMBER;
    } else {
        $page_number = '';
    }
    $page_increment = GALLERY_ITEMS_NUMBER;
    $item_count = get_videos('count', '');
    
    if ($page_number == '') {
    
        $previous_page = '1';
        echo '<a class="' . $page_previous_class . '" href="?page=' . $previous_page . '"></a>';
        
    } elseif ($page_number == '1') {
    
        $previous_page = $page_number+'1';
        
        echo '<a class="' . $page_previous_class . '" href="?page=' . $previous_page . '"></a>';
        echo '<a class="' . $page_next_class . '" href="' . SITE_URL . '"></a>';
        
    } else {
        
        $next_page = $page_number-'1';
        $previous_page = $page_number+'1';
        
        // Only display a previous posts link if the item count = the page increment.
        if ($item_count == $page_increment) {
            echo '<a class="' . $page_previous_class . '" href="?page=' . $previous_page . '"></a>';
        }
        
        echo '<a class="' . $page_next_class . '" href="?page=' . $next_page . '"></a>';
        
    }
}

/*-----------------------------------------------------------------------------------*/
/* Text & Settings Functions
/*-----------------------------------------------------------------------------------*/

function limit_text($text, $limit) {
    $text = strip_tags($text);
    
    if (strlen($text) > $limit) {
        $textCut = substr($text, 0, $limit);
        $text = substr($textCut, 0, strrpos($textCut, ' ')).' ...'; 
    }
    echo $text;
}

function get_text($text) {
    echo (stripslashes($text));
}

function get_setting($setting) {
    echo ($setting);
}

function get_meta($meta) {
    echo ($meta);
}

function get_part($part) {
    echo ($part);
}

function get_body_class() {
    
    // Home page classes.
    if (is_page('home')) {
    
        // Simple "home" class.
        echo (VIDEO_SERVICE . ' home');
        
    // Single page classes.    
    } elseif (is_page('single')) {
    
        // Simple "single" class.
        echo (VIDEO_SERVICE . ' single');
        
    // Page page classes.    
    } else {
    
        // 
        if (is_image('background')) {
            echo ('page background');
        } else {
            echo ('page');            
        }
    }
}

/*-----------------------------------------------------------------------------------*/
/* Conditional Functions
/*-----------------------------------------------------------------------------------*/

function is_image($name) {
    
    if ($name == 'logo' || $name == 'favicon') {
        $format = 'png';
    } elseif ($name == 'background') {
        $format = 'jpg';
    }
    
    // If there's an image, this is true.
    if (file_exists('./uploads/' . $name . '.' . $format)) {
        return true;
    }
}

function is_setting($setting) {
    
    // If a setting is set.
    if ($setting) {
        return true;
    }
}

function is_page($page) {
    
    // Get the current page.    
    $currentpage  = @( $_SERVER['HTTPS'] != 'on' ) ? 'http://'.$_SERVER['SERVER_NAME'] : 'https://'.$_SERVER['SERVER_NAME'];
    
    if (isset($_GET['page'])) {
        $page_number = $_GET['page'];
        $currentpage .= str_replace(array('?page='.$page_number), '', $_SERVER['REQUEST_URI']);
    } else {
        $currentpage .= $_SERVER['REQUEST_URI'];
    }
    
    // Get the current video ID.
    $the_current_video_id = end((explode('/', $currentpage)));
    
    if ($page == 'home') {
        
        // The home page URL.
        $home_page = SITE_URL . '/';
        
        // If is home return true.
        if ($home_page == $currentpage) {
            return true;
        }
	
    } elseif ($page == 'single') {
        
        // YouTube Video ID Check
        if (VIDEO_SERVICE == 'youtube') {
    	    
            $headers = get_headers('http://gdata.youtube.com/feeds/api/videos/' . $the_current_video_id);	    
            if (strpos($headers[0], '200')) {
                return true;
            }
        
        // Vimeo Video ID Check
        } elseif (VIDEO_SERVICE == 'vimeo') {
            
            $headers = get_headers('http://vimeo.com/api/v2/video/' . $the_current_video_id . '.json');	    
            if (strpos($headers[0], '200')) {
                return true;
            }
        }
	    
    } elseif ($page == 'about') {
    
        // The about page URL.
        $about_page = SITE_URL . '/about';
        
        // If is home return true.
        if ($about_page == $currentpage) {
            return true;
        }
    }
}

/*-----------------------------------------------------------------------------------*/
/* Theme Specific Functions
/*-----------------------------------------------------------------------------------*/

function get_template_part($part) {
    extract($GLOBALS);
    
    // Include template part.
    $template_part = $base_dir . 'themes/' . $site_theme . '/' . $part . '.php';
    include($template_part);
}

function get_includes($location) { 
    extract($GLOBALS);
    
    // Default includes for the footer.
    if ($location == 'footer') { ?>
    <script src="http://code.jquery.com/jquery-2.0.3.min.js"></script>
    <script type="text/javascript">
        $(function(){
            $(document).on('click', '#pagination a',function(e) {
                e.preventDefault();
                var page_url=$(this).prop('href');
                
                $('#gallery').append('<div id="loading" class="animated flash"></div>');
                $('#gallery').load(page_url + ' #gallery');
                $('html, body').animate({
                    scrollTop: $("#gallery").offset().top
                }, 400);
                
            });
        });
    </script>
    <?php }
    
    // For addons.
    action::run($location);
}

function get_analytics() {
    
    // Site analytics.
    if (SITE_ANALYTICS !== '') {

    ?>
    <script type="text/javascript">
        var _gaq = _gaq || [];
        _gaq.push(['_setAccount', '<?php echo(SITE_ANALYTICS); ?>']);
        _gaq.push(['_trackPageview']);
        
        (function() {
            var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
        })();
    </script>
    <?php 
    }
}

function get_cinematico_version() {
    $content = file(BASE_DIR . 'ABOUT.md');
    $version = str_replace(array("\n", '- '), '', $content[1]);
    echo($version);   
}