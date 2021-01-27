<?php
//アップロードを許可する拡張子
$cfg['ALLOW_EXTS'] = array("jpg", "jpeg", "gif", "png", "mp4");
$cfg_img['ALLOW_EXTS'] = array("jpg", "jpeg", "gif", "png");
$cfg_mv['ALLOW_EXTS'] = array("mp4");

//ファイル名から拡張子を取得する関数
function getExt($filename)
{
    return pathinfo($filename, PATHINFO_EXTENSION);
}

//アップロードされたファイル名の拡張子が許可されているか確認する関数
function checkExt($filename)
{
    global $cfg;
    $ext = strtolower(getExt($filename));
    return in_array($ext, $cfg['ALLOW_EXTS']);
}

//アップロードされたファイル名のimg拡張子が許可されているか確認する関数
function checkExt_img($filename)
{
    global $cfg_img;
    $ext = strtolower(getExt($filename));
    return in_array($ext, $cfg_img['ALLOW_EXTS']);
}

//アップロードされたファイル名のmovie拡張子が許可されているか確認する関数
function checkExt_mv($filename)
{
    global $cfg_mv;
    $ext = strtolower(getExt($filename));
    return in_array($ext, $cfg_mv['ALLOW_EXTS']);
}
?>