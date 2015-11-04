<div class="header_form">
<div id="search-23" class="widget widget_search">
         <form role="search" method="get" id="searchform_main" action="<?php echo home_url(); ?>">
             <!-- <div id="searchform_pulldown"> -->
                    <!--  <select  title="次の中から検索" name="seachform_itemstatus" class="itemstatus">
                        <option value="givemeable"　select="selected">ください可</option>
                        <option value="all">すべて</option>
                     </select> -->

                  <!-- <input type="hidden" value="givemeable" name="seachform_itemstatus"> -->
             <!-- </div> -->
        <div id="searchform_bar">
            <div id="searchform_text"　class="searchform">
                <input type="text" placeholder="ほしい本を検索する" class="search" id="searchtext" name="s" id="s" value="<?php if(isset($_GET['s'])){ echo escape_html_special_chars($_GET['s']); } ?>"/>
            </div>
            <div id="searchform_submit" class="searchform">
                <input class="search" type="submit" id="searchsubmit" />
            </div>
        </div>
<!-- 検索バー -->          
        </form>
</div>
</div>

            
               