<?php

/**
 * @var $this \Palto\Palto
 */

use Palto\Search;

$this->partial('header.inc', [
    'title' => 'Search result',
    'description' => 'Search result',
]);
?>
<h1>Search result</h1>
<?php $query = $this->filterString($_GET['query'] ?? '');?>
<?php $found = Search::find($query, 0, $this->getAdsLimit());?>
<?php $adsIds = Search::getIds($found);?>

<table class="serp">
    <?php foreach ($this->getAdsByIds($adsIds) as $ad) :?>
        <?php $this->partial('ad_in_list.inc', ['ad' => $ad])?>
    <?php endforeach;?>
</table>
<?php $this->initPager($this->hasNextPage(Search::getCount($found)));?>
<?php $this->partial('pager.inc', [
    'pageNumber' => $this->getPageNumber(),
    'nextPageUrl' => $this->getNextPageUrl(),
    'previousPageUrl' => $this->getPreviousPageUrl(),
])?>

<?php $this->partial('footer.inc', []);