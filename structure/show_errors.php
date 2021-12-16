<?php
if ($handle = opendir('logs')) {
    $errors = [];
    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {
            echo "$entry\n";
            parseErrors('logs/' . $entry, $errors);
            usort($errors, function ($previous, $next) {
                if ($previous['count'] < $next['count']) {
                    return 1;
                }

                if ($previous['count'] > $next['count']) {
                    return -1;
                }

                return 0;
            });
        }
    }

    closedir($handle);
    var_dump($errors);
}

function parseErrors($path, &$errors) {
    $handle = fopen($path, "r");
    if ($handle) {
        while (($line = fgets($handle)) !== false) {
            $isError = mb_strpos($line, '.ERROR: ') !== false;
            if ($isError) {
                $lineParts = explode('.ERROR: ', $line);
                $error = $lineParts[1];
                $isException = mb_strpos($error, '#0 ') !== false;
                if ($isException) {
                    $exceptionParts = explode(': ', $error);
                    $key = $exceptionParts[0];
                } else {
                    $key = $error;
                }

                if (!isset($errors[$key])) {
                    $errors[$key] = ['count' => 0, 'text' => ''];
                }

                $errors[$key]['count']++;
                $errors[$key]['text'] = $error;
            }
        }

        fclose($handle);

        return $errors;
    } else {
        throw new Exception('Can not open file');
    }
}