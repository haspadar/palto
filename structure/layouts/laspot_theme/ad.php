<?php
/**
 * @var $this \Palto\Layout\Client
 */
$this->partial('header.inc', [
    'title' => $this->translate('ad_title'),
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
    <?php $this->partial('breadcrumb.inc', ['breadcrumbUrls' => $this->getBreadcrumbUrls()]);?>
    <h1><?=$this->translate('ad_h1')?></h1>
<?=\Palto\Counters::get('google') ?: \Palto\Counters::receive('adx')?>
<?php if ($this->getAd()->getImages()) :?>
    <!-- Slideshow container -->
    <div class="slideshow-container">
        <!-- Full-width images with number and caption text -->
        <?php foreach ($this->getAd()->getImages() as $key => $image) :?>
            <div class="mySlides fade">
                <div class="numbertext"><?=$key + 1?> / <?=count($this->getAd()->getImages())?></div>
                <img src="<?=$image['small']?>" style="width:100%" loading="lazy">
            </div>
        <?php endforeach;?>

        <!-- Next and previous buttons -->
        <a class="prev" onclick="plusSlides(-1)">❮</a>
        <a class="next" onclick="plusSlides(1)">❯</a>
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
<?=\Palto\Counters::get('adx') ?: \Palto\Counters::get('google')?>
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
    📍 <?=$this->translate('Регион')?>: <a href="<?=$this->generateRegionUrl($this->getAd()->getRegion())?>"><?=$this->getAd()->getRegion()->getTitle()?></a>
<?php endif;?>

<?php if (trim($this->getAd()->getSellerName())) :?>
    <div class="seller">💁‍♂️ <?=$this->getAd()->getSellerName()?></div>
<?php endif;?>

<?php if ($this->getAd()->getSellerPhone()) :?>
    <div class="phone">📞 <a class="show-phone phone" id="show-phone" data-phone="<?=$this->getAd()->getSellerPhone()?>" href="tel:<?=$this->getAd()->getSellerPhone()?>">
            <?php if ($this->getAd()->getSellerPhone()) :?>
                <?=$this->translate('Показать телефон')?>
            <?php else :?>
                <?=$this->translate('Нет телефона')?>
            <?php endif;?></a>
    </div>
<?php endif;?>

    <div class="reply"><a class="reply_link" href="<?=$this->getAd()->getUrl()?>" target="_blank" rel="nofollow">🤙 <?=$this->translate('Связаться')?></a></div>
    <div class="create_time">⏱ <?=$this->translate('Время публикации')?>: <?=$this->getAd()->getCreateTime()->format('d.m.Y')?> </div>
    <div class="report"><a class="report_link" href="javascript:void(0);" id="send-abuse">⚠️ <?=$this->translate('Пожаловаться на объявление')?></a></div>
    <div id="send-abuse-modal" class="modal" data-url="<?=\Palto\Config::getDomainUrl()?>/send-feedback.php">
        <!-- Modal content -->
        <div class="modal-content">
            <div class="modal-header">
                <span class="close">&times;</span>
                <div><?=$this->translate('Пожаловаться на объявление')?></div>
            </div>
            <form class="form">
                <table class="tbl_report">
                    <tr>
                        <td class="td_report"><input type="hidden" name="page" value="http://<?=$_SERVER['HTTP_HOST']?><?=$_SERVER['REQUEST_URI']?>"><label>Email:</label></td>
                        <td><input type="email" name="email" required></td>
                    </tr>
                    <tr>
                        <td class="td_report"><input type="hidden" name="ad_id" value="<?=$this->getAd()->getId()?>"><label><?=$this->translate('Жалоба')?>:</label></td>
                        <td><textarea name="message" rows="3" width="200px"></textarea></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><input type="submit" value="Send" class="button"></td>
                    </tr>
                </table>
            </form>
            <p class="success" style="display: none">
                <?=$this->translate('Ваша жалоба успешно отправлена.')?>
            </p>
        </div>
    </div>
    <br />
    <h2><?=$this->translate('Похожие объявления')?></h2>
    <?=\Palto\Counters::get('google') ?: \Palto\Counters::get('adx')?>
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