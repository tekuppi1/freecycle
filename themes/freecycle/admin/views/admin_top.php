<?php
	//get_catgories_list();
  admin_search_form();
	echo '<hr>';
	admin_item_list();
?>

<script>
   function switchDisplay(id){
      jQuery("#sub_category_" + id).toggle(800);
   }
</script>




