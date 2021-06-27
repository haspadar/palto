<?php
$projectsDirectory = $argv[1] ?? '';
if (!$argv[1]) {
    exit('Укажите параметр – путь к проектам');
}

$commands = ['composer update'];
foreach (getProjectPaths($projectsDirectory) as $project) {
    `cd $project`;
    foreach ($commands as $command) {
        `$command`;
    }
}

function getProjectPaths(string $path): array
{
    $projects = [];
    if ($handle = opendir('.')) {
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                $projects[] = $path
                    . (substr($path, -1) != '/' ? '/' : '')
                    . $entry;
            }
        }

        closedir($handle);
    }

    return $projects;
}