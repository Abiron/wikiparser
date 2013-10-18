<?
include "simple_html_dom.php";

$wikiurl = 'http://ru.wikipedia.org';
$mobWikiTemplate = 'http://ru.wikipedia.org/w/index.php?printable=yes&title=';

$index=array();//Index of base...

$homedir=dirname(__FILE__).'/';

$urlMask='';




$url='http://ru.wikipedia.org/wiki/%D0%9A%D0%B0%D1%82%D0%B5%D0%B3%D0%BE%D1%80%D0%B8%D1%8F:%D0%9F%D1%80%D1%83%D0%B4%D1%8B';

set_time_limit(0);



function escapeName($string)
{
	$string = str_replace(array('/', ' ', ':', '.', '—'), '_', $string);
	$converter = array(
        'а' => 'a',   'б' => 'b',   'в' => 'v',
        'г' => 'g',   'д' => 'd',   'е' => 'e',
        'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
        'и' => 'i',   'й' => 'y',   'к' => 'k',
        'л' => 'l',   'м' => 'm',   'н' => 'n',
        'о' => 'o',   'п' => 'p',   'р' => 'r',
        'с' => 's',   'т' => 't',   'у' => 'u',
        'ф' => 'f',   'х' => 'h',   'ц' => 'c',
        'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
        'ь' => "'",  'ы' => 'y',   'ъ' => "'",
        'э' => 'e',   'ю' => 'yu',  'я' => 'ya',
 
        'А' => 'A',   'Б' => 'B',   'В' => 'V',
        'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
        'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
        'И' => 'I',   'Й' => 'Y',   'К' => 'K',
        'Л' => 'L',   'М' => 'M',   'Н' => 'N',
        'О' => 'O',   'П' => 'P',   'Р' => 'R',
        'С' => 'S',   'Т' => 'T',   'У' => 'U',
        'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
        'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
        'Ь' => "'",  'Ы' => 'Y',   'Ъ' => "'",
        'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
    );
    $string = strtr($string, $converter);
	return iconv('utf-8', 'cp1251', $string);
}




function getCategories($text)
{
	$arr = array();
	$html = str_get_html($text);
	if(sizeof($html->find('[id=mw-subcategories]')) > 0)
		foreach($html->find('[id=mw-subcategories]', 0)->find('.CategoryTreeLabel') as $element) 
			$arr[$element->href] = $element->plaintext;
	$html->clear();
	unset($html);
	return $arr;	 
}

function getPages($text)
{
	$html = str_get_html($text);
	if(sizeof($html->find('[id=mw-pages]', 0))>0)
		foreach($html->find('[id=mw-pages]', 0)->find('a') as $element) 
			$arr[$element->href] = $element->plaintext;

	
	$html->clear();
	unset($html);
	return $arr;	 
}

function getContent($suburl)
{
	global $mobWikiTemplate; 
	$suburl = str_replace('/wiki/', '', $suburl);
	$suburl = $mobWikiTemplate . $suburl;
	$html = str_get_html(getPage($suburl));
	$content = $html->find('.mw-body', 0)->outertext;
	//May be need to delete links...
	$html->clear();
	unset($html);
	return $content;	 
}

function getPage($url)
{
	//Simple page load function
	return file_get_contents($url);
}


function slowLoad($url)
{
	//Simple page load function two
	return file_get_contents($url);
}

function save($name, $content)
{
	global $homedir;
	file_put_contents($homedir.'data/articles/'.escapeName($name).'.html', $content);
}




function parseTree($url, $ind, array $indexArray)
{

	global $wikiurl;
	global $index;
	global $homedir;
	$text = getPage($url);
	$catigories = getCategories($text);
	
	$indexArray[$ind]=array();
	
	if(sizeof($catigories)>0)
	{
		foreach($catigories as $catUrl => $catName)
		{
			parseTree($wikiurl.$catUrl, $catName, &$indexArray[$ind]);
			
			
			
			
		
		}
	}
	
	$pages=getPages($text);
	if(sizeof($pages)>0)
	{
		foreach($pages as $pageUrl => $pageName)
		{
			save($pageName, getContent($pageUrl));
			$indexArray[$ind][]=escapeName($pageName).'	'.$pageName;
			


		}
	
	}
	
	

}

function loadTemplates()
{
	global $homedir;
	$templates['layout'] = file_get_contents($homedir.'data/layout.html');
	$templates['categoryList'] = file_get_contents($homedir.'data/category_list.html');
	$templates['articleList'] = file_get_contents($homedir.'data/article_list.html');
	$templates['article'] = file_get_contents($homedir.'data/article.html');
	return $templates;

}


function sarchSubtree(array $subtree, $place, array $bread) 
{ 
	global $breadTree;
	foreach ($subtree as $key => $value) 
	{ 
		if (is_array($value)) 
		{ 
			
			if($place == $key)
			{
				//$breadTree = $bread;
				$breadTree[] = $key;
				return $subtree[$place];
				
			}
			
			
			$newrr = sarchSubtree($value, $place, $bread);
			if(sizeof($newrr)>0) $breadTree[] = $key;
			if(sizeof($newrr)>0) return $newrr;
		}
	}
	return array();
} 

function modArticles(array $articles)
{
	global $urlMask;
	$templates=loadTemplates();
	foreach ($articles as &$article)
	{		
		$article = explode('	', $article);
		$article = str_replace(array('%BODY%', '%URL%'), array($article[1], $urlMask.$article[0]), $templates['articleList']);
	}
	return $articles; 

}

function modCategories(array $categories)
{
	
	global $urlMask;
	$templates=loadTemplates();
	foreach ($categories as &$cat)
	{		
		
		$cat = str_replace(array('%TITLE%', '%URL%'), array($cat, $urlMask.$cat), $templates['categoryList']);
	}
	return $categories; 
	

}


function showTree($index, $place)
{
	global $breadTree;
	
	
	
	$templates=loadTemplates();
	
	$subTree=sarchSubtree($index, $place, array());
	
	foreach ($subTree as $value) 
	{ 
		if (!is_array($value)) 
		{
			$articles[] = $value;
		
		}
		
	}
	if (isset($articles) && is_array($articles))
		modArticles(&$articles);
	
	foreach ($subTree as $key=>$value) 
	{ 
		if (is_array($value)) 
		{
			$categories[] = $key;
		
		}
		
	}
	if (isset($categories) && is_array($categories))
		modCategories(&$categories);
	
	$show = $templates['layout'];
	
	
	
	foreach($breadTree as &$piece)
	{
		$piece="<a href='$piece'>$piece</a>";
		//echo $piece;
	
	}
	$breadTree = array_reverse($breadTree);
	
	$show = str_replace('%ARTICLES%', @implode($categories), $show);
	$show = str_replace('%CATIGORIES%', @implode($articles), $show);
	$show = str_replace('%BREADCRUMBS%', @implode(' << ', $breadTree), $show);
	echo $show;

}



function showFile($file)
{
	
	global $homedir;
	$templates = loadTemplates();
	$file = file_get_contents($homedir."data/articles/$file.html");
	$file = str_replace('%BODY%',$file, $templates['article']);
	echo $file;

}

function sendPing($url)
{
	return (getPage($url) == md5('ping'));

}

// --------------------------Main start---------------------


		if(isset($argv) && sizeof($argv)>0)
			foreach($argv as $command)
			{
				$command = explode('=', $command);
				$_GET[$command[0]] = $command[1];
			}
		
		
		
		
		switch($_GET['action'])
		{
		
			case 'parse':
			{
				
				$url=$_GET['url'];
				parseTree($url, 'HOME', &$index);
				file_put_contents($homedir.'data/index.db', serialize($index)); //Save index file...
				echo 'parsed';
				break;
			}
			case 'unzip':
			{
				$zip = new ZipArchive;
				if($zip->open($homedir.'archive.zip') === true)
				{
					$zip->extractTo($homedir);
					$zip->close();
					echo 'Удачная распаковка';
				}
				else
				{
					echo 'Неудачная распаковка';
				
				}

				break;
			}
			case 'zip':
			{
				@unlink($homedir.'archive.zip');
				if(file_exists($homedir.'archive.zip'))
					die('Архив не создан');
				exec('zip -r archive.zip "data"');
				if(file_exists($homedir.'archive.zip'))
					echo 'Архив создан';
				else
					echo 'Архив не создан';
				break;
			}
			case 'templates':
			{
				file_put_contents($homedir.'data/layout.html', stripcslashes($_POST['layout']));
				file_put_contents($homedir.'data/category_list.html', stripcslashes($_POST['categoryList']));
				file_put_contents($homedir.'data/article_list.html', stripcslashes($_POST['articleList']));
				file_put_contents($homedir.'data/article.html', stripcslashes($_POST['article']));
				echo 'Шаблоны изменены';
				break;
			}
			case 'ping':
			{
				if(is_writable($homedir.'data/index.db') && is_writable($homedir.'data/articles')) echo md5('ping');
				break;
			}
			case 'sendPing':
			{
				if(!isset($_GET['url'])) die('Нет адреса');
				if(sendPing($_GET['url'].'?action=ping'))
					echo 'Тест пройден';
				else
					echo 'Тест провален';
				break;
			}
			case 'send':
			{
				if(!isset($_GET['url'])) die('Нет адреса');
				$suc=slowLoad($_GET['url'].'?action=recivePing&url='.$_GET['archiveUrl']);
				if($suc == 'true')
					echo 'Отправка удачна';
				else
					echo 'Отправка неудачна';
				
				
				break;
			}
			case 'recivePing':
			{
				if(!isset($_GET['url'])) die('fail');
				$arch = slowLoad($_GET['url']);
				file_put_contents('archive.zip', $arch);
				die('true');
				break;
			}
			default:
			{				
				$breadTree=array();
				$index = unserialize(file_get_contents($homedir.'data/index.db'));	
	
				if(!isset($_GET['place']) || $_GET['place'] == '') $_GET['place'] = 'HOME';
				
				if(!file_exists($homedir.'data/articles/'.trim($_GET['place']).'.html'))
					showTree($index, trim($_GET['place']));
				else
					showFile(trim($_GET['place']));
									
			
			
			}
		}
	

	




//---------------------------Main end----------------------
?>