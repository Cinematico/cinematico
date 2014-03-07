<?php get_video('all', 'content-video'); ?>

<div id="gallery" class="row">
    <div id="content" class="row">
        <header class="content">
            <h3><?php get_text($gallery_title); ?></h3>
            <p><?php get_text($gallery_description); ?></p>
        </header>
        
        <div id="thumbs" class="content grid">
            <?php get_videos('gallery', 'content-gallery-item'); ?>
        </div>
    </div>
    
    <footer id="footer" class="row">
        <div class="content">
            <div class="left">
                <span><?php get_text($footer_text); ?></span><br>
                <a class="cinematico" href="http://cinemati.co" target="_blank">Powered by Cinematico</a>
            </div>
            
            <div class="right">
                <div id="pagination"><?php get_pagination('page-previous', 'page-next'); ?></div>
            </div>
        </div>
    </footer>
</div>