<?php
$projectsDirectory = $argv[1] ?? '';
if (!$argv[1]) {
    exit('Укажите параметр – путь к проектам');
}

$commands = ['composer update'];
$projectPaths = getProjectPaths($projectsDirectory);
echo 'Projects: ' . implode(',', $projectPaths) . PHP_EOL;
foreach ($projectPaths as $project) {
    `cd $project`;
    foreach ($commands as $command) {
        `$command`;
    }
}

function getProjectPaths(string $path): array
{
    $projects = [];
    if ($handle = opendir($path)) {
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                $filePath = $path
                    . (substr($path, -1) != '/' ? '/' : '')
                    . $entry;
                if (is_dir($filePath)) {
                    $projects[] = $filePath;
                }
            }
        }

        closedir($handle);
    }

    return $projects;
}