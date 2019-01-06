<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/templates/_functions.php');

//Configuration:
$PageName = 'openEHR JSON Schemas';
$format = 'JSON Schema';
$filePattern = '*.json';
$regex_components = '/BASE|RM|AM|LANG|PROC|OET|QUERY/';
$regex_releases = '/latest|Release-\d+\.\d+(.\d+)?/';
$specs_href = 'https://specifications.openehr.org/releases';


//Code to scan for the right files
$relativePath = str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__) . '/';
$requestUri = trim(str_replace($relativePath, '', $_SERVER['REQUEST_URI']), '/');
$parts = explode('/', $requestUri, 4);
//... validate target
if (!empty($parts[0]) && preg_match('/\w+/', $parts[0]) && is_dir(__DIR__ . '/'. $parts[0])) {
    $targetDir = __DIR__ . '/'. $parts[0];
} else {
    $targetDir = __DIR__ . '/components';
}
//... validate component
if (!empty($parts[1]) && preg_match($regex_components, $parts[1]) && is_dir($targetDir . '/'. $parts[1])) {
    $targetDir = $targetDir . '/'. $parts[1];
    $componentInfo = "{$parts[1]} component";
} else {
    $componentInfo = '';
}
//... validate release
if (!empty($parts[2]) && preg_match($regex_releases, $parts[2]) && is_dir($targetDir . '/'. $parts[2])) {
    $targetDir = $targetDir . '/'. $parts[2];
    $releaseInfo = "from the {$parts[2]} release";
} else {
    $releaseInfo = '';
}
//... add href to infos
if ($componentInfo && $releaseInfo) {
    $componentInfo = "<a href=\"$specs_href/$parts[1]/$parts[2]\">$componentInfo</a>";
    $releaseInfo = "<a href=\"$specs_href/$parts[1]/$parts[2]\">$releaseInfo</a>";
}
//... collect all candidate files
function collectFileNames($pattern, $startdir = '', $flags = 0){
    $files = glob($startdir.$pattern, $flags) ?: array();
    foreach (glob($startdir.'*', GLOB_ONLYDIR|GLOB_NOSORT|GLOB_MARK) as $dir){
        $files = array_merge($files, collectFileNames($pattern, $dir, $flags));
    }
    return $files;
}
$files = collectFileNames($filePattern, $targetDir);

//debug info
/*
var_dump(
    $_SERVER['REQUEST_URI'], $_SERVER['DOCUMENT_ROOT'], __DIR__,
    $requestUri, $relativePath,
    $parts,
    $targetDir,
    $componentInfo, $releaseInfo,
    $files
);
*/

require_once($_SERVER['DOCUMENT_ROOT'].'/templates/_header.php');
?>

<div id="Content">
    <!-- - - - - - - - - - - - - - - - - - - - - - - Content starts here - - - - - - - - - - - - - - - - - - - - - - - - - -->

    <h1><?php echo "$PageName";?></h1>

    <p>The following files are the openEHR <?php echo $componentInfo; ?> models <?php echo $releaseInfo; ?> expressed in <?php echo $format; ?> format.</p>
    <ul>
        <?php
        foreach ($files as $file) {
            $file = str_replace($targetDir . '/', '', $file);
            echo "<li><a href=\"{$file}\">{$file}</a></li>\n";
        }
        ?>
    </ul>

    <!-- - - - - - - - - - - - - - - - - - - - - - - Content ends here - - - - - - - - - - - - - - - - - - - - - - - - - -->
</div>
<?php require_once($_SERVER['DOCUMENT_ROOT'].'/templates/_footer.php');?>
