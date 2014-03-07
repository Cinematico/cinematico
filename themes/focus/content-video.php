<div id="background" class="row" style="background-image: url('<?php get_part($the_video_thumbnail); ?>');">
    <?php get_template_part('content-header'); ?>
    
    <div id="video" class="row">
        <div class="content">
            <div id="embed">
                <?php get_part($the_video_embed); ?>
            </div>
            
            <div id="description" class="row">
                <div class="left">
                    <h2><?php get_text($the_video_title); ?></h2>
                    <p><?php get_text($the_video_description); ?></p>
                </div>
                
                <div class="right">
                    <a class="icon-twitter button" href="https://twitter.com/intent/tweet?text=&quot;<?php get_part($the_video_title); ?>&quot;%20<?php get_part($the_video_permalink); ?>%20via%20&#64;<?php get_setting($profile_twitter); ?>" data-dnt="true" title="Share on Twitter"></a>
                    <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
                    <a class="icon-facebook button" href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2F<?php get_part($the_video_permalink); ?>" target="_blank" title="Share on Facebook"></a>
                    <a class="icon-google-plus button" title="Share on Google" href="https://plus.google.com/share?url=<?php get_part($the_video_permalink); ?>" onclick="javascript:window.open(this.href,'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"></a>
                </div>
            </div>
        </div>
    </div>
</div>