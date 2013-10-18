<?
$homedir=dirname(__FILE__).'/';
$templates['layout'] = file_get_contents($homedir.'data/layout.html');
$templates['categoryList'] = file_get_contents($homedir.'data/category_list.html');
$templates['articleList'] = file_get_contents($homedir.'data/article_list.html');
$templates['article'] = file_get_contents($homedir.'data/article.html');
?>
<form method=POST action='index.php?action=templates'> 
layout:<br><textarea name="layout" cols=64 rows=6>
<? echo $templates['layout']; ?>
</textarea><br>
categoryList:<br><textarea name="categoryList" cols=64 rows=6>
<? echo $templates['categoryList']; ?>
</textarea><br>
articleList:<br><textarea name="articleList" cols=64 rows=6>
<? echo $templates['articleList']; ?>
</textarea><br>
article:<br><textarea name="article" cols=64 rows=6>
<? echo $templates['article']; ?>
</textarea><br>
<input type=submit>
</form>