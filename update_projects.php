<?php
$projectsDirectory = $argv[1] ?? '';
if (!isset($argv[1])) {
    exit('Укажите параметр – путь к проектам');
}

$commands = [
    'composer update',
    'ln -s /var/www/washingtonspot.org/parse_donor_ads.php ./',
//    'cp vendor/haspadar/palto/structure/layouts/registration.php layouts/'
];
$projectPaths = getProjectPaths($projectsDirectory);
echo 'Projects: ' . implode(',', $projectPaths) . PHP_EOL;
foreach ($projectPaths as $projectKey => $project) {
    foreach ($commands as $command) {
        $fullCommand = "cd $project && $command";
        echo 'RUN COMMAND FOR PROJECT '
            . ($projectKey + 1)
            . '/' . count($projectPaths)
            . ': '
            . $fullCommand
            . PHP_EOL;
        `cd $project && $command` . PHP_EOL;
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