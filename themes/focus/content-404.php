<div id="background" class="row" <?php if (is_image('background')) { ?>style="background-image: url('<?php get_setting($site_url); ?>/uploads/background.jpg');"<?php } ?>>
    <div class="row">
        <?php get_template_part('content-header'); ?>
    </div>
    
    <div id="page" class="row">
        <div class="content">
            <h2><?php get_text($not_found_title); ?></h2>
            <p><?php get_text($not_found_text); ?></p>
            <a class="button" href="<?php get_setting($site_url); ?>">Home</a>
        </div>
    </div>
</div>