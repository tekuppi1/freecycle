<form role="search" method="get" id="searchform_main" action="<?php echo home_url(); ?>">
    <div id="searchform_bar">
            <input type="text" placeholder="ほしい本を検索する" class="search" id="searchtext" name="s" id="s" value="<?php if(isset($_GET['s'])){ echo escape_html_special_chars($_GET['s']); } ?>"/>
        <div id="searchform_submit" class="searchform">
            <input class="search" type="submit" id="searchsubmit" />
        </div>
    </div>
<!-- 検索バー -->          
</form>
