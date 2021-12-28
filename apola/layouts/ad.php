<?php
/**
 * @var $this \Palto\Palto
 */
$this->partial('header.inc', [
    'title' => $this->getCurrentAd()['title']
        . ': '
        . implode(
            ' - ',
            array_filter(array_merge(
                $this->getCurrentCategory()['titles'],
                [$this->getCurrentAd()['address']],
                ['Ogłoszenia w ' . $this->getCurrentRegion()['title']],
            ))
        ),
    'description' => $this->generateShortText($this->getCurrentAd()['text']),
    'nextPageUrl' => $this->getNextPageUrl(),
    'previousPageUrl' => $this->getPreviousPageUrl(),
    'css' => $this->getCurrentAd()['coordinates'] ? [
        [
            'href' => 'https://unpkg.com/leaflet@1.7.1/dist/leaflet.css',
            'integrity' => 'sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==',
            'crossorigin' => ''
        ]
    ] : [],
]);
?>
<div class="bread" itemscope itemtype="http://schema.org/BreadcrumbList">
    <?php $this->partial('breadcrumb.inc', ['breadcrumbUrls' => $this->getAdBreadcrumbUrls()]);?>
</div>
<h1><?=$this->getCurrentAd()['title']?> <span style="color:#999"> in <?=$this->getCurrentAd()['address'] ? $this->getCurrentAd()['address'] . ', ' : ''?><?=$this->getCurrentRegion()['title']?> z olx</span></h1>
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
<?php if ($this->getCurrentAd()['images']) :?>
    <!-- Slideshow container -->
    <div class="slideshow-container">
        <!-- Full-width images with number and caption text -->
        <?php foreach ($this->getCurrentAd()['images'] as $key => $image) :?>
            <div class="mySlides fade">
                <div class="numbertext"><?=$key + 1?> / <?=count($this->getCurrentAd()['images'])?></div>
                <img src="<?=$image['small']?>;s=640x640" style="width:100%" loading="lazy">
            </div>
        <?php endforeach;?>

        <!-- Next and previous buttons -->
        <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
        <a class="next" onclick="plusSlides(1)">&#10095;</a>
    </div>
    <br>

    <!-- The dots/circles -->
    <div style="text-align:center;">
        <?php foreach ($this->getCurrentAd()['images'] as $key => $image) :?>
            <span class="dot" onclick="currentSlide(<?=$key + 1?>)"></span>
        <?php endforeach;?>
    </div>

<?php endif;?>

<br>
<div class="youtube myvideo" data-url="<?=$this->getDomainUrl() . '/youtube.php?query=' . urlencode($this->getCurrentAd()['title'])?>" style="text-align: center">
    <img src="/img/loading.gif" alt="loading">
</div>
<hr />
<?php if ($this->getCurrentAd()['details']) :?>
    <ul class="details">
        <?php foreach ($this->getCurrentAd()['details'] as $field => $value) :?>
            <li>➡️ <?=$field?>: <?=$value?></li>
        <?php endforeach;?>
    </ul>
<?php endif;?>
<div class="description"> <?=urldecode($this->getCurrentAd()['text'])?> </div>
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
<?php if ($this->getCurrentAd()['price'] > 0) :?>
    <div class="price">
        🏷 <?=number_format($this->getCurrentAd()['price'])?> <?=$this->getCurrentAd()['currency']?></span>
    </div>
<?php endif;?>

<div><?=$this->getCurrentAd()['seller_postfix'] ?? ''?></div>
<?php if ($this->getCurrentAd()['address']) :?>
    <div class="adress">📍Address: <?=$this->getCurrentAd()['address']?></div>
<?php endif;?>

<?php if ($this->getCurrentAd()['coordinates']) :?>
    <div id="map"
         data-latitude="<?=$this->getLatitude()?>"
         data-longitude="<?=$this->getLongitute()?>"
         data-accuracy="15"
    ></div>
<?php endif;?>
📍Region: <a href="<?=$this->generateRegionUrl($this->getCurrentAd()['region'])?>"><?=$this->getCurrentAd()['region']['title']?></a>
<?php if (trim($this->getCurrentAd()['seller_name'])) :?>
    <div class="seller">💁‍♂️ <?=$this->getCurrentAd()['seller_name']?></div>
<?php endif;?>

<?php if ($this->getCurrentAd()['seller_phone']) :?>
    <div class="phone">📞 <a class="show-phone phone" id="show-phone" data-phone="<?=$this->getCurrentAd()['seller_phone']?>" href="tel:<?=$this->getCurrentAd()['seller_phone']?>">
        <?php if ($this->getCurrentAd()['seller_phone']) :?>
            Show Phone
        <?php else :?>
            No Phone
        <?php endif;?></a>
    </div>
<?php endif;?>

<div class="reply"><a class="reply_link" href="<?=$this->getCurrentAd()['url']?>" target="_blank" rel="nofollow">🤙 Odpowiedź</a></div>
<div class="post_time">⏱ Post time: <?=(new DateTime($this->getCurrentAd()['post_time']))->format('d.m.Y')?> </div>
<div class="report"><a class="report_link" href="javascript:void(0);" id="send-abuse">⚠️ Zgłoś tę reklamę</a></div>
<div id="send-abuse-modal" class="modal" data-url="<?=$this->getDomainUrl()?>/send-feedback.php">
    <!-- Modal content -->
    <div class="modal-content">
        <div class="modal-header">
            <span class="close">&times;</span>
            <div>Zgłoś tę reklamę</div>
        </div>
        <form class="form">
            <table class="tbl_report">
                <tr>
                    <td class="td_report"><input type="hidden" name="page" value="http://<?=$_SERVER['HTTP_HOST']?><?=$_SERVER['REQUEST_URI']?>"><label>Email:</label></td>
                    <td><input type="email" name="email" required></td>
                </tr>
                <tr>
                    <td class="td_report"><input type="hidden" name="ad_id" value="<?=$this->getCurrentAd()['id']?>"><label>Raport:</label></td>
                    <td><textarea name="message" rows="3" width="200px"></textarea></td>
                </tr>
                <tr>
                    <td></td>
                    <td><input type="submit" value="Send" class="button"></td>
                </tr>
            </table>
        </form>
        <p class="success" style="display: none">
            Twój raport został wysłany.
        </p>
    </div>
</div>
<br />
<h2>Podobne reklamy</h2>
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
<table class="serp">
<?php foreach ($this->getAds($this->getCurrentCategory()['id'], $this->getCurrentRegion()['id'], 6) as $similarAd) :?>
    <?php if ($similarAd['id'] != $this->getCurrentAd()['id']) :?>
        <?php $this->partial('ad_in_list.inc', ['ad' => $similarAd])?>
    <?php endif;?>
<?php endforeach;?>
</table>

<?php $this->partial('footer.inc', [
    'js' => $this->getCurrentAd()['coordinates'] ? [
        [
            'src' => 'https://unpkg.com/leaflet@1.7.1/dist/leaflet.js',
            'integrity' => 'sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==',
            'crossorigin' => ''
        ]
    ] : []
]);