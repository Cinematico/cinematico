<header id="header" class="row">
    <div class="content">
        <div class="left">
            <?php if (is_image('logo')) { ?>
            <a href="<?php get_setting($site_url); ?>"><img src="<?php get_setting($site_url); ?>/uploads/logo.png" /></a>
            <?php } else { ?>
            <h1><a href="<?php get_setting($site_url); ?>"><?php get_text($site_title); ?></a></h1>
            <p><?php get_text($site_description); ?></p>
            <?php } ?>
        </div>
    
        <div class="right">
            <a class="icon-menu open" href="#menu"></a>
        </div>
    </div>
</header>