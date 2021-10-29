<?php

/**
 * @var $this \Palto\Palto
 */

use Palto\Search;

$query = $this->filterString($_GET['query'] ?? '');
$offset = ($this->getPageNumber() - 1) * $this->getAdsLimit();
$found = Search::find($query, $this->getEnv()['DB_NAME'], $offset, $this->getAdsLimit());
$adsIds = Search::getIds($found);
$hasNextPage = Search::getCount($found) > $offset + count($adsIds);
$this->initPager($hasNextPage);
$this->partial('header.inc', [
    'title' => $this->getSearchQuery() . ' in ' . $this->getCurrentRegion()['title'] . ': search results',
    'description' => $this->getSearchQuery() . ' in ' . $this->getCurrentRegion()['title'] . ': search results',
    'nextPageUrl' => $this->getNextPageUrl(),
    'previousPageUrl' => $this->getPreviousPageUrl(),
]);
?>

    <h1><?=$this->getSearchQuery()?> in <?=$this->getCurrentRegion()['title']?></h1>
    <table class="serp">
        <?php if ($adsIds) :?>
            <?php foreach ($this->getAdsByIds($adsIds) as $ad) :?>
                <?php $this->partial('ad_in_list.inc', ['ad' => $ad])?>
            <?php endforeach;?>
        <?php else :?>
            Not found
        <?php endif;?>

    </table>

<?php $this->initPager(Search::getCount($found) > $offset + count($adsIds))?>
<?php $this->partial('pager.inc', [
    'pageNumber' => $this->getPageNumber(),
    'nextPageUrl' => $this->getNextPageUrl(),
    'previousPageUrl' => $this->getPreviousPageUrl(),
])?>

<?php $this->partial('footer.inc', []);