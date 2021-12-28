<?php

/**
 * @var $this \Palto\Palto
 */

use Palto\Search;

$this->partial('header.inc', [
    'title' => 'Wynik wyszukiwania',
    'description' => 'Wynik wyszukiwania',
]);
?>
<h1>Wynik wyszukiwania</h1>
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-4251434934288140"
     crossorigin="anonymous"></script>
<!-- apola_adaptive -->
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-4251434934288140"
     data-ad-slot="5199190551"
     data-ad-format="auto"
     data-full-width-responsive="true"></ins>
<script>
     (adsbygoogle = window.adsbygoogle || []).push({});
</script>
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