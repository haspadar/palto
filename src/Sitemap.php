<?php
namespace Palto;

class Sitemap
{
    const GENERATE_SCRIPT = 'bin/generate_sitemap.php';
    const MAX_FILE_LINKS = 40000;
    const MAX_FILE_SIZE_MB = 40;
    private string $domainUrl;
    private string $path;

    public function __construct(string $domainUrl, string $path)
    {
        $this->domainUrl = $domainUrl;
        $this->path = $path;
    }

    public function generate()
    {
        $executionTime = new ExecutionTime();
        $executionTime->start();
        $groupedRegions = $this->groupTrees(
            array_map(fn ($region) => new Region($region),
                \Palto\Model\Regions::getDb()->query('SELECT * FROM regions ORDER BY tree_id, level')
            )
        );
        $groupedRegions[0][] = new Region([]);
        $groupedCategories = $this->groupTrees(
            array_map(fn ($category) => new Category($category),
                \Palto\Model\Categories::getDb()->query('SELECT * FROM categories WHERE id IN (SELECT DISTINCT category_level_1_id FROM ads) ORDER BY tree_id, level')
            )
        );
        $this->generateRegionsFiles('/regions', $groupedRegions);
        /**
         * @var Region[] $regions
         */
        foreach ($groupedRegions as $regions) {
            $regionWithLevel1 = $regions[0];
            /**
             * @var Category[] $categories
             */
            foreach ($groupedCategories as $categories) {
                $categoryWithLevel1 = $categories[0];
                $this->generateCategoriesFiles(
                    '/' . $regionWithLevel1->getUrl() . '-' . $categoryWithLevel1->getUrl(),
                    $regions,
                    $categories
                );
            }
        }

        $siteMapIndexUrl = $this->generateIndexes();
        $executionTime->end();
        Logger::info('Generated sitemap ' . $siteMapIndexUrl . ' for ' . $executionTime->get());
    }

    /**
     * @param Category[]|Region[] $leaves
     * @return array
     */
    private function groupTrees(array $leaves): array
    {
        $trees = [];
        foreach ($leaves as $leaf) {
            $trees[$leaf->getTreeId()][] = $leaf;
        }

        return $trees;
    }

    private function generateRegionsFiles(string $regionTreePath, array $groupedRegions)
    {
        $urls = [];
        foreach ($groupedRegions as $regionTree) {
            /**
             * @var $region Region
             */
            foreach ($regionTree as $region) {
                $urls[] = $region->generateUrl();
            }
        }

        $chunks = array_chunk($urls, $this->getMaxFileLinks());
        foreach ($chunks as $chunkKey => $chunk) {
            $this->saveUrls($this->path . $regionTreePath, $chunkKey + 1, $chunk);
        }
    }

    /**
     * @param string $regionTreePath
     * @param Region[] $regions
     * @param Category[] $categories
     * @return void
     */
    private function generateCategoriesFiles(string $regionTreePath, array $regions, array $categories)
    {
        $urls = [];
        foreach ($regions as $region) {
            foreach ($categories as $category) {
                if ($this->hasAds($category, $region)) {
                    $urls[] = $category->generateUrl($region);
                } else {
                    Logger::debug('Ignored category ' . $category->getId() . ' with region ' . $region->getId() . ': ads not found');
                    exit;
                }
            }
        }

        $chunks = array_chunk($urls, $this->getMaxFileLinks());
        foreach ($chunks as $chunkKey => $chunk) {
            $this->saveUrls($this->path . $regionTreePath, $chunkKey + 1, $chunk);
        }
    }

    private function hasAds(Category $category, ?Region $region): bool
    {
        $query = "SELECT id FROM categories_regions_with_ads WHERE category_id={$category->getId()}"
            . ($region && $region->getId() ? " AND region_id={$region->getId()}" : '');

        return (bool)\Palto\Model\Ads::getDb()->queryFirstField($query);
    }

    private function saveUrls(string $path, string $fileName, array $urls, bool $checkSize = true)
    {
        if (!file_exists(Directory::getPublicDirectory() . $path)) {
            mkdir(Directory::getPublicDirectory() . $path, 0777, true);
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

        $fullFilePath = Directory::getPublicDirectory() . $path . '/' . $fileName . '.xml';
        $xml->saveXML($fullFilePath);
        Logger::debug('Saved ' . count($urls) . ' urls to ' . $fullFilePath);
        if ($checkSize) {
            if ($this->getFileSizeInMb($fullFilePath) > $this->getMaxFileSizeMb()) {
                $chunksCount = ceil($this->getFileSizeInMb($fullFilePath) / $this->getMaxFileSizeMb());
                $chunks = array_chunk($urls, ceil(count($urls) / $chunksCount));
                foreach ($chunks as $chunkKey => $chunk) {
                    $this->saveUrls($path, $fileName . '-' . ($chunkKey + 1), $chunk);
                }

                Logger::debug('Split file ' . $fullFilePath . ' to ' . $chunksCount . ' files');
                unlink($fullFilePath);
                Logger::debug('Removed ' . $fullFilePath . ' file');
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
        $fileNameParts = array_values(array_filter(explode('/', $this->path)));
        $fileName = $fileNameParts[count($fileNameParts) - 1];
        $filesUrls = $this->getFilesUrls(
            $this->getDirectoryRecursiveFiles(Directory::getPublicDirectory() . $this->path),
            ['/' . $fileName . '.xml']
        );

        $this->saveUrls($this->path, $fileName, $filesUrls);
        $indexesUrls = $this->getFilesUrls(
            $this->getDirectoryFiles(Directory::getPublicDirectory() . $this->path),
            ['/' . $fileName . '.xml']
        );
        if (count($indexesUrls) > 1) {
            $this->saveUrls($this->path, $fileName, $indexesUrls, false);
        }

        $siteMapIndexUrl = $this->domainUrl . $this->path . '/' . $fileName . '.xml';

        return $siteMapIndexUrl;
    }

    private function getFilesUrls(array $paths, array $excludes = []): array
    {
        $filesUrls = [];
        foreach ($paths as $path) {
            $parts = explode($this->path, $path);
            if (!in_array($parts[1], $excludes)) {
                $filesUrls[] = $this->path . $parts[1];
            }
        }

        return $filesUrls;
    }

    private function getMaxFileLinks(): int
    {
        return self::MAX_FILE_LINKS;
    }

    private function getMaxFileSizeMb(): float
    {
        return self::MAX_FILE_SIZE_MB;
    }
}