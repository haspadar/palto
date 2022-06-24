<?php

namespace Palto;

class Breadcrumbs
{
    public static function getUrls(?Region $region, ?Category $category): array
    {
        $defaultRegion = new Region([]);
        $urls = [[
            'title' => $defaultRegion->getTitle(),
            'url' => $defaultRegion->generateUrl(),
        ]];
        if ($region && $region->generateUrl() != $defaultRegion->generateUrl()) {
            foreach ($region->getParents() as $parentRegion) {
                $urls[] = [
                    'title' => $parentRegion->getTitle(),
                    'url' => $parentRegion->generateUrl($region)
                ];
            }

            $urls[] = [
                'title' => $region->getTitle(),
                'url' => $region->generateUrl()
            ];
        }

        if ($category) {
            foreach ($category->getParents() as $parentCategory) {
                $urls[] = [
                    'title' => $parentCategory->getTitle(),
                    'url' => $parentCategory->generateUrl($region)
                ];
            }

            $urls[] = [
                'title' => $category->getTitle(),
                'url' => $category->generateUrl($region)
            ];
        }

        return array_filter(
            $urls,
            fn($url) => $url != (new Url())->getPath()
        );
    }
}