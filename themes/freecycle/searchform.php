<div class="header_form">
<div id="search-23" class="widget widget_search">
         <form role="search" method="get" id="searchform_main" action="<?php echo home_url(); ?>">
               <div id="searchform_text">
                       <input type="text" id="searchtext" name="s" id="s" value="<?php if(isset($_GET['s'])){ echo $_GET['s']; } ?>"/>
                 </div>
                  <div id="searchform_pulldown">
                     <select name="seachform_itemstatus">
                        <option value="givemeable">ください可</option>
                        <option value="all">すべて</option>
                     </select>
                  </div>
                  <div id="searchform_submit">
                     <input type="submit" id="searchsubmit" value="検索" />
                  </div>
               </div>
         </form>
</div><!-- 検索バー -->
