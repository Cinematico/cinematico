<div id="<?php get_part($the_video_id); ?>" class="block <?php get_part($the_video_status); ?>">
    <div class="screenshot">
        <a href="<?php get_part($the_video_permalink); ?>">
            <img src="<?php get_part($the_video_thumbnail); ?>" />
        </a>
    </div>
    
    <h2><?php limit_text($the_video_title, '25'); ?></h2>
    <p><?php limit_text($the_video_description, '70'); ?></p>
</div>