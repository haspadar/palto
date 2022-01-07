<?php
/**
 * @var $this \Palto\Layout
 */
$this->partial('header.inc', [
    'title' => $this->generateHtmlTitle(),
    'description' => \Palto\Filter::shortText($this->getAd()->getText()),
    'css' => $this->getAd()->getCoordinates() ? [
        [
            'href' => 'https://unpkg.com/leaflet@1.7.1/dist/leaflet.css',
            'integrity' => 'sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==',
            'crossorigin' => ''
        ]
    ] : [],
]);
?>
    <div class="bread" itemscope itemtype="http://schema.org/BreadcrumbList">
        <?php $this->partial('breadcrumb.inc', ['breadcrumbUrls' => $this->getBreadcrumbUrls()]);?>
    </div>
    <h1><?=$this->getAd()->getTitle()?> <span style="color:#999"> in <?=$this->getAd()->getAddress() ? $this->getAd()->getAddress() . ', ' : ''?><?=$this->getRegion()->getTitle()?> from craigslist</span></h1>
<?php if ($this->getAd()->getImages()) :?>
    <!-- Slideshow container -->
    <div class="slideshow-container">
        <!-- Full-width images with number and caption text -->
        <?php foreach ($this->getAd()->getImages() as $key => $image) :?>
            <div class="mySlides fade">
                <div class="numbertext"><?=$key + 1?> / <?=count($this->getAd()->getImages())?></div>
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
        <?php foreach ($this->getAd()->getImages() as $key => $image) :?>
            <span class="dot" onclick="currentSlide(<?=$key + 1?>)"></span>
        <?php endforeach;?>
    </div>

<?php endif;?>

    <br>
    <div class="youtube myvideo" data-url="<?=\Palto\Config::getDomainUrl() . '/youtube.php?query=' . urlencode($this->getAd()->getTitle())?>" style="text-align: center">
        <img src="/img/loading.gif" alt="loading">
    </div>
    <hr />
<?php if ($this->getAd()->getDetails()) :?>
    <ul class="details">
        <?php foreach ($this->getAd()->getDetails() as $field => $value) :?>
            <li>➡️ <?=$field?>: <?=$value?></li>
        <?php endforeach;?>
    </ul>
<?php endif;?>
    <div class="description"> <?=urldecode($this->getAd()->getText())?> </div>
<?php if ($this->getAd()->getPrice() > 0) :?>
    <div class="price">
        🏷 <?=$this->getAd()->getCurrency()?><?=number_format($this->getAd()->getPrice())?>
    </div>
<?php endif;?>

    <div><?=$this->getAd()->getSellerPostfix()?></div>
<?php if ($this->getAd()->getAddress()) :?>
    <div class="adress">📍Address: <?=$this->getAd()->getAddress()?></div>
<?php endif;?>

<?php if ($this->getAd()->getCoordinates()) :?>
    <div id="map"
         data-latitude="<?=$this->getAd()->getLatitude()?>"
         data-longitude="<?=$this->getAd()->getLongitute()?>"
         data-accuracy="15"
    ></div>
<?php endif;?>

<?php if ($this->getAd()->getRegion()):?>
    Region: <a href="<?=$this->generateRegionUrl($this->getAd()->getRegion())?>"><?=$this->getAd()->getRegion()->getTitle()?></a>
<?php endif;?>

<?php if (trim($this->getAd()->getSellerName())) :?>
    <div class="seller">💁‍♂️ <?=$this->getAd()->getSellerName()?></div>
<?php endif;?>

<?php if ($this->getAd()->getSellerPhone()) :?>
    <div class="phone">📞 <a class="show-phone phone" id="show-phone" data-phone="<?=$this->getAd()->getSellerPhone()?>" href="tel:<?=$this->getAd()->getSellerPhone()?>">
            <?php if ($this->getAd()->getSellerPhone()) :?>
                Show Phone
            <?php else :?>
                No Phone
            <?php endif;?></a>
    </div>
<?php endif;?>

    <div class="reply"><a class="reply_link" href="<?=$this->getAd()->getUrl()?>" target="_blank" rel="nofollow">🤙 Reply</a></div>
    <div class="create_time">⏱ Post time: <?=$this->getAd()->getCreateTime()->format('d.m.Y')?> </div>
    <div class="report"><a class="report_link" href="javascript:void(0);" id="send-abuse">⚠️ Report this ad</a></div>
    <div id="send-abuse-modal" class="modal" data-url="<?=\Palto\Config::getDomainUrl()?>/send-feedback.php">
        <!-- Modal content -->
        <div class="modal-content">
            <div class="modal-header">
                <span class="close">&times;</span>
                <div>Report this ad</div>
            </div>
            <form class="form">
                <table class="tbl_report">
                    <tr>
                        <td class="td_report"><input type="hidden" name="page" value="http://<?=$_SERVER['HTTP_HOST']?><?=$_SERVER['REQUEST_URI']?>"><label>Email:</label></td>
                        <td><input type="email" name="email" required></td>
                    </tr>
                    <tr>
                        <td class="td_report"><input type="hidden" name="ad_id" value="<?=$this->getAd()->getId()?>"><label>Report:</label></td>
                        <td><textarea name="message" rows="3" width="200px"></textarea></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><input type="submit" value="Send" class="button"></td>
                    </tr>
                </table>
            </form>
            <p class="success" style="display: none">
                Your report has been sent.
            </p>
        </div>
    </div>
    <br />
    <h2>Similar ads</h2>
    <table class="serp">
        <?php foreach ($this->getSimilarAds() as $similarAd) :?>
            <?php if ($similarAd->getId() != $this->getAd()->getId()) :?>
                <?php $this->partial('ad_in_list.inc', ['ad' => $similarAd])?>
            <?php endif;?>
        <?php endforeach;?>
    </table>

<?php $this->partial('footer.inc', [
    'js' => $this->getAd()->getCoordinates() ? [
        [
            'src' => 'https://unpkg.com/leaflet@1.7.1/dist/leaflet.js',
            'integrity' => 'sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==',
            'crossorigin' => ''
        ]
    ] : []
]);