<?php /** @var $this League\Plates\Template\Template */ ?>
<?php use Palto\Ads;
use Palto\Categories; ?>
<?php $this->layout('layout'); ?>

<?php /** @var $ad \Palto\Ad */?>
<?php if ($ad->getCoordinates()) :?>
    <?php $this->push('styles') ?>
        <link rel="stylesheet" href="<?=$this->asset('/css/leaflet.css')?>">
    <?php $this->end() ?>
<?php endif;?>

<?=\Palto\Counters::get('google') ?: \Palto\Counters::receive('adx')?>
<?php if ($ad->getImages()) :?>
    <!-- Slideshow container -->
    <div class="slideshow-container">
        <!-- Full-width images with number and caption text -->
        <?php foreach ($ad->getImages() as $key => $image) :?>
            <div class="mySlides fade">
                <div class="numbertext"><?=$key + 1?> / <?=count($ad->getImages())?></div>
                <img src="<?=$image['big'] ?: $image['small']?>" style="width:100%" loading="lazy">
            </div>
        <?php endforeach;?>

        <!-- Next and previous buttons -->
        <a class="prev" onclick="plusSlides(-1)">❮</a>
        <a class="next" onclick="plusSlides(1)">❯</a>
    </div>
    <br>

    <!-- The dots/circles -->
    <div style="text-align:center;">
        <?php foreach ($ad->getImages() as $key => $image) :?>
            <span class="dot" onclick="currentSlide(<?=$key + 1?>)"></span>
        <?php endforeach;?>
    </div>

<?php endif;?>

    <br>
    <div class="youtube myvideo" data-url="<?=\Palto\Config::getDomainUrl() . '/youtube.php?query=' . urlencode($ad->getTitle())?>" style="text-align: center">
        <img src="/img/loading.gif" alt="loading">
    </div>
    <hr />
<?php if ($ad->getDetails()) :?>
    <ul class="details">
        <?php foreach ($ad->getDetails() as $field => $value) :?>
            <li>➡️ <?=$field?>: <?=$value?></li>
        <?php endforeach;?>
    </ul>
<?php endif;?>
    <div class="description"> <?=urldecode($ad->getText())?> </div>
    <?=\Palto\Counters::receive('adx') ?: \Palto\Counters::get('google')?>
<?php if ($ad->getPrice() > 0) :?>
    <div class="price">
        🏷 <?=$ad->getCurrency()?><?=number_format($ad->getPrice())?>
    </div>
<?php endif;?>

    <div><?=$ad->getSellerPostfix()?></div>
<?php if ($ad->getAddress()) :?>
    <div class="adress">📍Address: <?=$ad->getAddress()?></div>
<?php endif;?>

<?php if ($ad->getCoordinates()) :?>
    <div id="map"
         data-latitude="<?=$ad->getLatitude()?>"
         data-longitude="<?=$ad->getLongitute()?>"
         data-accuracy="15"
    ></div>
<?php endif;?>

<?php if ($ad->getRegion()):?>
    📍 <?=$this->translate('Регион')?>: <a href="<?=$ad->getRegion()->generateUrl()?>"><?=$ad->getRegion()->getTitle()?></a>
<?php endif;?>

<?php if (trim($ad->getSellerName())) :?>
    <div class="seller">💁‍♂️ <?=$ad->getSellerName()?></div>
<?php endif;?>

<?php if ($ad->getSellerPhone()) :?>
    <div class="phone">📞 <a class="show-phone phone" id="show-phone" data-phone="<?=$ad->getSellerPhone()?>" href="tel:<?=$ad->getSellerPhone()?>">
            <?php if ($ad->getSellerPhone()) :?>
                <?=$this->translate('Показать телефон')?>
            <?php else :?>
                <?=$this->translate('Нет телефона')?>
            <?php endif;?></a>
    </div>
<?php endif;?>

    <div class="reply"><a class="reply_link" href="<?=$ad->getUrl()?>" target="_blank" rel="nofollow">🤙 <?=$this->translate('Связаться')?></a></div>
    <div class="create_time">⏱ <?=$this->translate('Время публикации')?>: <?=$ad->getCreateTime()->format('d.m.Y')?> </div>
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
                        <td class="td_report"><input type="hidden" name="ad_id" value="<?=$ad->getId()?>"><label><?=$this->translate('Жалоба')?>:</label></td>
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
    <?=\Palto\Counters::get('google') ?: \Palto\Counters::receive('adx')?>
    <table class="serp">
        <?php foreach (Ads::getAds(
            $this->data['region'],
            $this->data['category'],
            5,
            0
        ) as $similarAd) :?>
            <?php if ($similarAd->getId() != $ad->getId()) :?>
                <?php $this->insert('partials/ad_in_list')?>
            <?php endif;?>
        <?php endforeach;?>
    </table>

<?php if ($ad->getCoordinates()) :?>
    <?php $this->push('scripts') ?>
        <script src="<?=$this->asset('/js/leaflet.js')?>"></script>
    <?php $this->end() ?>
<?php endif;