<?php
function json($msg, $code = 1, $extra = [])
{
    $data = ['code' => $code, 'msg' => $msg];
    //合并extra和data
    $data = array_merge($data, $extra);
    echo json_encode($data);
    exit();
}
function deleteDirectory($dir)
{
    if (!file_exists($dir)) {
        return true;
    }

    if (!is_dir($dir)) {
        return unlink($dir);
    }

    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }

        if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
            return false;
        }
    }
}
function loadEnvVariables($filePath)
{
    $variables = array();

    if (file_exists($filePath)) {
        $file = fopen($filePath, 'r');

        while (($line = fgets($file)) !== false) {
            $line = trim($line);

            // 跳过以#开头的注释行和空行
            if (empty($line) || $line[0] === '#') {
                continue;
            }

            // 解析变量名和值
            $parts = explode('=', $line, 2);
            $name = trim($parts[0]);
            $value = isset($parts[1]) ? trim($parts[1]) : '';

            // 移除变量值中的引号
            if (preg_match('/^"(.+)"$/', $value, $matches) === 1) {
                $value = $matches[1];
            }

            $variables[$name] = $value;
        }

        fclose($file);
    }

    return $variables;
}
