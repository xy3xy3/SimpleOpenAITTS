<?php
include __DIR__ . '/system/inc.php';
//从php input 加载post
$_POST = json_decode(file_get_contents('php://input'), true);
$text = isset($_POST['text']) ? $_POST['text'] : '';
$model = isset($_POST['model']) ? $_POST['model'] : 'tts-1';
$voice = isset($_POST['voice']) ? $_POST['voice'] : 'alloy';
$format = isset($_POST['response_format']) ? $_POST['response_format'] : 'mp3';
//判断格式规范
if (!in_array($format, ['opus', 'mp3', 'aac', 'flac', 'wav', 'pcm'])) {
    json('格式错误', 1);
}
if ($text) {
    $result = $open_ai->speech([
        "model" => $model,
        "input" => $text,
        "voice" => $voice,
        "response_format" => $format
    ]);
    if (empty($result)) {
        json('转换失败', 1);
    }
    //判断如果result是json格式则
    if (json_decode($result)) {
        json($result, 1);
    }
    $dir = '/save/' . time().md5($text) . "." . $format;
    $saveDirectory = __DIR__ . $dir;
    if (!file_exists(dirname($saveDirectory))) {
        mkdir(dirname($saveDirectory), 0777, true);
    }
    file_put_contents($saveDirectory, $result);
    json('转换成功', 0, ['url' => ".".$dir]);
}
json('请输入文本', 1);
