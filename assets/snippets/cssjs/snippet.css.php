<?php
if(!defined('MODX_BASE_PATH')) {die('What are you doing? Get out of here!');}

//Параметри
$files = isset($files)? $files: ''; // Список файлів (css, scss, less)
$minify = isset($minify)? $minify: '1'; //Стискати та мініфікувати файли
$folder = isset($folder)? $ folder : ''; // Папка для згенерованих стилів за промовчанням в корінь
$inhtml = isset($inhtml)? $inhtml : '0'; // Розмістити відразу в HTML тегах <style></style>
//$inline = isset($inline)? $inline : ''; // Інлайн код стилів
//$parse = isset($parse)? $parse : '0'; // Чи обробляти теги MODX
$filename = isset($filename)? $filename : 'styles'; // Ім'я файлу
$filepre = isset($filepre)? $filepre : ''; // Завантаження файлу, наприклад (media="print" onload="this.media='all'")
$link_rel = isset($link_rel)? $link_rel : 'stylesheet';// Атрибут посилання


//Обробляємо файли, перетворюємо less та scss
$filesArr = explode(',', str_replace('\n', '', $files));
foreach ($filesArr as $key => $value) {
	$file = MODX_BASE_PATH . trim($value);
	$fileinfo = pathinfo($file);
	$v[$key] =  filemtime($file);
	switch ($fileinfo['extension']) {
		case 'css':
		$filesForMin[$key] = $file;
		break;
		/*case 'less':
		require_once(MODX_BASE_PATH. "assets/snippets/cssjs/less.inc.php");
		$less = new lessc;
		$less->checkedCompile($file, $folder.$fileinfo['filename'].'.css');
		$filesForMin[$key] = $folder.$fileinfo['filename'].'.css';
		break;*/
	}
}
if ($minify == '1') {
	include_once(MODX_BASE_PATH. "assets/snippets/cssjs/class.magic-min.php");
	$minified = new Minifier();
	$min = $minified->merge( MODX_BASE_PATH.$folder.$filename.'.min.css', 'css', $filesForMin );
	if ($inhtml){
		return '<style>'. file_get_contents($modx->getConfig('base_path').$folder.'styles.min.css') .'</style>';
		}
	else return '<link rel="'.$link_rel.'" href="'.$modx->config['site_url'].$folder.$filename.'.min.css?v='.substr(md5(max($v)),0,3).'" '.$filepre.'>';
}else{
	$links = '';
	foreach ($filesArr as $key => $value) {
		if ($inhtml){
			$links .= '<style>'.file_get_contents($modx->getConfig('base_path').trim($value)).'</style>';
			}
		else $links .= '<link rel="stylesheet" href="'.$modx->config['site_url'].trim($value).'?v='.substr(md5($v[$key]),0,3).'" />';
	}
	return $links;
}
?>
