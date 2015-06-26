<h3>キーワード検索</h3>

<?php
   get_search_form();
?>

<h3>カテゴリ検索</h3>

<?php
   $main_categories = get_categories(array(
      "parent" => 0,
      "hide_empty" => 0,
      "exclude" => 1 //'uncategorized'
   ));

   foreach ((array)$main_categories as $main_category) {
      $main_id = $main_category->term_id;
      $main_name = $main_category->name;
      $main_slug = $main_category->slug;
      echo "<div class='main_categories' onclick='switchDisplay($main_id);'>$main_name</div>";

      $sub_categories = get_categories(array(
         "parent" => $main_id
      ));

      echo "<div class='sub_categories' id=sub_category_$main_id >";

      foreach((array)$sub_categories as $sub_category){
         $sub_name = $sub_category->name;
         $sub_slug = $sub_category->slug;
         echo "<div><a href='". home_url() ."/archives/category/".$main_slug."/".$sub_slug ."'>" .$sub_name. "</a></div>";
      }

      echo "</div>";
   }
?>

<script>
   function switchDisplay(id){
      jQuery("#sub_category_" + id).toggle(800);
   }
</script>
