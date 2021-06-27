<?php
$path = '..';
$commands = ['composer update'];

foreach (getProjects($path) as $project) {
    `cd $path/$project`;
    foreach ($commands as $command) {
        `$command`;
    }
}

function getProjects(string $path): array
{
    $projects = [];
    if ($handle = opendir('.')) {
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                $projects[] = $entry;
            }
        }

        closedir($handle);
    }

    return $projects;
}