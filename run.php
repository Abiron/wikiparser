<form method=GET action='index.php'> 
Начать парсинг:</br>
<input type="text" name="url" value="http://ru.wikipedia.org/wiki/%D0%9A%D0%B0%D1%82%D0%B5%D0%B3%D0%BE%D1%80%D0%B8%D1%8F:%D0%9F%D1%80%D1%83%D0%B4%D1%8B">
<input type="hidden" name="action" value="parse">
</br>
<input type=submit>
</form>
<hr>
<a href='templates.php'>Изменить шаблоны</a>
</br><hr>
<form method=GET action='index.php'> 
Пинг:</br>
<input type="text" name="url" value="url">
<input type="hidden" name="action" value="sendPing">
</br>
<input type=submit>
</form>
</br><hr>
<a href='index.php?action=zip'>Zip</a>
</br><hr>
<a href='index.php?action=unzip'>UnZip</a>
</br><hr>
<form method=GET action='index.php'> 
Отослать архив:</br>
<input type="text" name="url" value="url"></br>
<input type="text" name="archiveUrl" value="archive url">
<input type="hidden" name="action" value="send">
</br>
<input type=submit>
</form>
</br><hr>
<a href='wiki/'>Index url</a>
</br><hr>