<div id="background" class="row" <?php if (is_image('background')) { ?>style="background-image: url('<?php get_setting($site_url); ?>/uploads/background.jpg');"<?php } ?>>
    <div class="row">
        <?php get_template_part('content-header'); ?>
    </div>
    
    <div id="page" class="row">
        <div class="content">
            <h2><?php get_text($about_title); ?></h2>
            <p><?php get_text($about_text); ?></p>
            <?php if (is_setting($profile_email)) { ?>
            <a class="button" href="mailto:<?php get_setting($profile_email); ?>">Contact</a>
            <?php } ?>
        </div>
    </div>
</div>