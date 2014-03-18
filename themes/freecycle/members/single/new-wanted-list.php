<form action="new_entry" method="post" enctype="multipart/form-data" id="new_wanted">

<!-- item name -->
<input type="text" name="keyword" id="keyword" placeholder="Amazonで検索" size="30">
<input type="button" name="btn_search" value="検索" onClick="onClickSearchWantedBook()"></br>
</br>
<div id="search_result">
</div>
<br/>
<hr>
<label for="manual">手動で登録</label></br>
<input class="btn btn-primary" type="button" value="登録" onClick="callOnNewWanted()" >
</form>
<br/>
<hr> <!-- 仕切り線 -->