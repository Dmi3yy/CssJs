<?php
if(!defined('MODX_BASE_PATH')) {die('What are you doing? Get out of here!');}

//параметры
$files = isset($files) ? $files : ''; // Список файлов (css, scss, less)
$minify = isset($minify) ? $minify : '1'; //сжымать и минифицировать файлы
$folder = isset($folder) ? $folder : ''; // папка для сгенерированных стилей по умолчанию в корень
//$inline = isset($inline) ? $inline : ''; // инлайн код стилей
//$parse = isset($parse) ? $parse : '0'; //обрабатывать ли теги MODX

//Обрабатываем файлы, преобразовываем less и scss
$filesArr = explode(',', str_replace('\n', '', $files));
foreach ($filesArr as $key => $value) {
	$file = MODX_BASE_PATH . trim($value);
	$v[$key] =  filemtime($file);
	$filesForMin[$key] = $file;  
}
if ($minify == '1') {
	include_once(MODX_BASE_PATH. "assets/snippets/cssjs/Minifier.php");
	$javascript = '';
	foreach($filesForMin as $file) {
			$javascript .= file_get_contents($file) . PHP_EOL;
	}
	$minified_javascript = \JShrink\Minifier::minify($javascript);
	file_put_contents(MODX_BASE_PATH.$folder.'scripts.min.js', $minified_javascript);
	return '<script src="'.$modx->config['site_url'].$folder.'scripts.min.js?v='.substr(md5(max($v)),0,3).'"></script>';
}else{
	$links = '';
	foreach ($filesArr as $key => $value) {
		$links .= '<script src="'.$modx->config['site_url'].trim($value).'?v='.substr(md5($v[$key]),0,3).'"></script>';	
	}	
	return $links;
}
?>
