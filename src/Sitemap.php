<?php
namespace Palto;

class Sitemap
{
    const GENERATE_SCRIPT = 'generate_sitemap.php';
    private string $domainUrl;
    private string $path;
    private Palto $palto;

    public function __construct(string $domainUrl, string $path, Palto $palto)
    {
        $this->domainUrl = $domainUrl;
        $this->path = $path;
        $this->palto = $palto;
    }

    public function generate()
    {
        $executionTime = new ExecutionTime();
        $executionTime->start();
        $regions = $this->groupTrees(
            $this->palto->getRegions(0, 0, 0, 0, 'tree_id, level')
        );
        $categories = $this->groupTrees(
            $this->palto->getCategories(0, 0, 0, 0, 'tree_id, level')
        );
        foreach ($regions as $regionTree) {
            $regionTreeUrl = $regionTree[0]['url'];
            foreach ($categories as $categoryTree) {
                $categoryTreeUrl = $categoryTree[0]['url'];
                $this->generateDirectoryFiles(
                    '/' . $regionTreeUrl . '-' . $categoryTreeUrl,
                    $regionTree,
                    $categoryTree
                );
            }
        }

        $siteMapIndexUrl = $this->generateIndexes();
        $executionTime->end();
        $this->palto->getLogger()->info(
            'Generated sitemap ' . $siteMapIndexUrl . ' for ' . $executionTime->get()
        );
    }

    private function groupTrees(array $leaves): array
    {
        $trees = [];
        foreach ($leaves as $leaf) {
            $trees[$leaf['tree_id']][] = $leaf;
        }

        return $trees;
    }

    private function generateDirectoryFiles(string $regionTreePath, array $regions, array $categories)
    {
        $urls = [];
        foreach ($regions as $region) {
            $urls[] = $this->palto->generateRegionUrl($region);
            foreach ($categories as $category) {
                $urls[] = $this->palto->generateCategoryUrl($category, $region);
            }
        }

        $chunks = array_chunk($urls, $this->getMaxFileLinks());
        foreach ($chunks as $chunkKey => $chunk) {
            $this->saveUrls($this->path . $regionTreePath, $chunkKey + 1, $chunk);
        }
    }

    private function saveUrls(string $path, string $fileName, array $urls, bool $checkSize = true)
    {
        if (!file_exists($this->palto->getPublicDirectory() . $path)) {
            mkdir($this->palto->getPublicDirectory() . $path, 0777, true);
        }

        $xml = new \SimpleXMLElement(
            '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"/>'
        );
        foreach ($urls as $url) {
            $element = $xml->addChild('url');
            $element->addChild('loc', $this->domainUrl . $url);
            $element->addChild('lastmod', (new \DateTime())->format('Y-m-d'));
            $element->addChild('changefreq', 'daily');
        }

        $fullFilePath = $this->palto->getPublicDirectory() . $path . '/' . $fileName . '.xml';
        $xml->saveXML($fullFilePath);
        $this->palto->getLogger()->debug('Saved ' . count($urls) . ' urls to ' . $fullFilePath);
        if ($checkSize) {
            if ($this->getFileSizeInMb($fullFilePath) > $this->getMaxFileSizeMb()) {
                $chunksCount = ceil($this->getFileSizeInMb($fullFilePath) / $this->getMaxFileSizeMb());
                $chunks = array_chunk($urls, ceil(count($urls) / $chunksCount));
                foreach ($chunks as $chunkKey => $chunk) {
                    $this->saveUrls($path, $fileName . '-' . ($chunkKey + 1), $chunk);
                }

                $this->palto->getLogger()->debug('Split file ' . $fullFilePath . ' to ' . $chunksCount . ' files');
                unlink($fullFilePath);
                $this->palto->getLogger()->debug('Removed ' . $fullFilePath . ' file');
            }
        }
    }

    private function getFileSizeInMb(string $file): float
    {
        $filesize = filesize($file); // bytes

        return round($filesize / 1024 / 1024, 4); // megabytes with 1 digit
    }

    private function getDirectoryRecursiveFiles(string $directory, &$results = array()): array
    {
        $files = scandir($directory);
        foreach ($files as $value) {
            $path = realpath($directory . DIRECTORY_SEPARATOR . $value);
            if (!is_dir($path)) {
                $results[] = $path;
            } else if ($value != "." && $value != "..") {
                $this->getDirectoryRecursiveFiles($path, $results);
            }
        }

        return $results;
    }

    private function getDirectoryFiles(string $directory): array
    {
        $files = scandir($directory);
        $results = [];
        foreach ($files as $value) {
            $path = realpath($directory . DIRECTORY_SEPARATOR . $value);
            if (!is_dir($path)) {
                $results[] = $path;
            }
        }

        return $results;
    }

    private function generateIndexes(): string
    {
        $filesUrls = $this->getFilesUrls(
            $this->getDirectoryRecursiveFiles($this->palto->getPublicDirectory() . $this->path)
        );
        $fileNameParts = array_values(array_filter(explode('/', $this->path)));
        $fileName = $fileNameParts[count($fileNameParts) - 1];
        $this->saveUrls($this->path, $fileName, $filesUrls);
        $indexesUrls = $this->getFilesUrls(
            $this->getDirectoryFiles($this->palto->getPublicDirectory() . $this->path)
        );
        if (count($indexesUrls) > 1) {
            $this->saveUrls($this->path, $fileName, $indexesUrls, false);
        }

        $siteMapIndexUrl = $this->domainUrl . $this->path . '/' . $fileName . '.xml';

        return $siteMapIndexUrl;
    }

    private function getFilesUrls(array $paths): array
    {
        $filesUrls = [];
        foreach ($paths as $path) {
            $parts = explode($this->path, $path);
            $filesUrls[] = $this->path . $parts[1];
        }

        return $filesUrls;
    }

    private function getMaxFileLinks(): int
    {
        return intval($this->palto->getEnv()['SITEMAP_MAX_FILE_LINKS'] ?? 0) ?: 40000;
    }

    private function getMaxFileSizeMb(): float
    {
        return intval($this->palto->getEnv()['SITEMAP_MAX_FILE_SIZE_MB'] ?? 0) ?: 40;
    }
}