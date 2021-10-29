<?php

/**
 * @var $this \Palto\Palto
 */

use Palto\Search;

$query = $this->filterString($_GET['query'] ?? '');
$offset = ($this->getPageNumber() - 1) * $this->getAdsLimit();
$found = Search::find($query, $offset, $this->getAdsLimit());
$adsIds = Search::getIds($found);
$hasNextPage = Search::getCount($found) > $offset + count($adsIds);
$this->initPager($hasNextPage);

$this->partial('header.inc', [
    'title' => 'Search result',
    'description' => 'Search result',
    'nextPageUrl' => $this->getNextPageUrl(),
    'previousPageUrl' => $this->getPreviousPageUrl(),
]);
?>

    <h1>Search result</h1>
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