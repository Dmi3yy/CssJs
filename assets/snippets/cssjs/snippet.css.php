<?php
if(!defined('MODX_BASE_PATH')) {die('What are you doing? Get out of here!');}

// Parameters
$files = isset($files) ? $files : ''; // List of files (css, scss, less)
$minify = isset($minify) ? $minify : '1'; // Minify files
$folder = isset($folder) ? $folder : ''; // Folder for generated styles, default is the root
$inhtml = isset($inhtml) ? $inhtml : '0'; // Place directly in HTML tags <style></style>
$filename = isset($filename) ? $filename : 'styles'; // Filename
$filepre = isset($filepre) ? $filepre : ''; // Load file, e.g., (media="print" onload="this.media='all'")
$link_rel = isset($link_rel) ? $link_rel : 'stylesheet'; // Link attribute

// Process files, convert less and scss
$filesArr = explode(',', str_replace('\n', '', $files));
foreach ($filesArr as $key => $value) {
    $file = MODX_BASE_PATH . trim($value);
    $fileinfo = pathinfo($file);
    $v[$key] = filemtime($file);
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

// The $filename variable is passed in parameters, so use it to create the styles file name.
if ($minify == '1') {
    include_once(MODX_BASE_PATH. "assets/snippets/cssjs/class.magic-min.php");
    $minified = new Minifier();
    $min = $minified->merge(MODX_BASE_PATH.$folder.$filename.'.min.css', 'css', $filesForMin);
    if ($inhtml) {
        return '<style>'. file_get_contents($modx->getConfig('base_path').$folder.$filename.'.min.css') .'</style>';
    } else {
        return '<link rel="'.$link_rel.'" href="'.$modx->config['site_url'].$folder.$filename.'.min.css?v='.substr(md5(max($v)),0,3).'" '.$filepre.'>';
    }
} else {
    $links = '';
    foreach ($filesArr as $key => $value) {
        if ($inhtml) {
            $links .= '<style>'.file_get_contents($modx->getConfig('base_path').trim($value)).'</style>';
        } else {
            $links .= '<link rel="'.$link_rel.'" href="'.$modx->config['site_url'].trim($value).'?v='.substr(md5($v[$key]),0,3).'" '.$filepre.'>';
        }
    }
    return $links;
}
?>
