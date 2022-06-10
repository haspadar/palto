<?php /** @var $this League\Plates\Template\Template */ ?>
<?php use Palto\Ads;
use Palto\Categories; ?>
<?php $this->layout('layout'); ?>

<?php if ($this->data['h1']) :?>
    <div class="card-headline headline">
        <h1><?= $this->data['h1'] ?></h1>
    </div>
<?php endif;?>

<?php /** @var $ad \Palto\Ad */ ?>
<?php if ($ad->getCoordinates()) : ?>
    <?php $this->push('styles') ?>
        <link rel="stylesheet" href="<?= $this->asset('/css/swiper-bundle.min.css')?>">
        <link rel="stylesheet" href="<?= $this->asset('/css/leaflet.css') ?>">
    <?php $this->end() ?>
<?php endif; ?>

<?= \Palto\Counters::get('google') ?: \Palto\Counters::receive('adx') ?>
<?php if ($ad->getImages()) : ?>
    <div class="slider">
        <div class="image-slider swiper-container">
            <div class="image-slider__wrapper swiper-wrapper">
                <?php foreach ($ad->getImages() as $image) : ?>
                    <div class="image-slider__slide swiper-slide">
                        <div class="image-slider__image">
                            <img src="<?=$image['big'] ?: $image['small']?>" alt="photo" loading="lazy">
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
<?php endif;?>

<?php if (\Palto\Config::get('YOUTUBE_URL') == 1) : ?>
    <div class="youtube__content youtube"
         data-url="<?= \Palto\Config::getDomainUrl() . '/youtube.php?query=' . urlencode($ad->getTitle()) ?>"
    >
        <img src="<?=\Palto\Directory::getThemePublicDirectory()?>/img/loading.gif" alt="loading">
    </div>
<?php endif; ?>

<div class="info">
    <div class="info__content">
        <?php if ($ad->getDetails()) : ?>
            <div class="info__feature">
                <?php foreach ($ad->getDetails() as $field => $value) : ?>
                    <span class="info__feature-item"><?= $field ?>: <?= $value ?></span>
                <?php endforeach;?>
            </div>
        <?php endif;?>

        <p class="info__description">
            <?= urldecode($ad->getText()) ?>
            <?= \Palto\Counters::receive('adx') ?: \Palto\Counters::get('google') ?>
        </p>
        <?php if ($ad->getPrice() > 0) : ?>
            <span class="info__price"><?= $ad->getCurrency() ?><?= number_format($ad->getPrice()) ?></span>
        <?php endif;?>

        <div class="info__block"><?= $ad->getSellerPostfix() ?></div>

        <?php if ($ad->getAddress()) : ?>
            <div class="info__block">
                <span>Address: <?= $ad->getAddress() ?></span>
            </div>
        <?php endif;?>

        <?php if ($ad->getCoordinates()) : ?>
            <div class="info__cart" id="map"
                 data-latitude="<?= $ad->getLatitude() ?>"
                 data-longitude="<?= $ad->getLongitute() ?>"
                 data-accuracy="15"
            ></div>
        <?php endif;?>

        <?php if ($ad->getRegion()): ?>
            <div class="info__block">
                <span><?= $this->translate('Регион') ?>: </span>
                <a href="<?= $ad->getRegion()->generateUrl() ?>">
                    <?= $ad->getRegion()->getTitle() ?>
                </a>
            </div>
        <?php endif;?>

        <?php if (trim($ad->getSellerName())) : ?>
            <div class="info__block"> <?= $ad->getSellerName() ?></div>
        <?php endif; ?>

        <?php if ($ad->getSellerPhone()) : ?>
            <div class="info__block"> <a class="show-phone phone" id="show-phone" data-phone="<?= $ad->getSellerPhone() ?>"
                                     href="tel:<?= $ad->getSellerPhone() ?>">
                    <?php if ($ad->getSellerPhone()) : ?>
                        <?= $this->translate('Показать телефон') ?>
                    <?php else : ?>
                        <?= $this->translate('Нет телефона') ?>
                    <?php endif; ?></a>
            </div>
        <?php endif; ?>

        <?php if (\Palto\Config::get('DONOR_URL') == 1) :?>
            <a class="info__reply"
               href="<?= $ad->getUrl() ?>"
               target="_blank"
               rel="nofollow">🤙 <?= $this->translate('Связаться') ?></a>
        <?php else :?>
            <a class="info__reply"
               href="/registration"
               rel="nofollow">🤙 <?= $this->translate('Связаться') ?></a>
        <?php endif;?>

        <span class="info__time"><?= $this->translate('Время публикации') ?>: <?= $ad->getCreateTime()->format('d.m.Y') ?></span>
        <a href="javascript:void(0);" class="info__report" id="send-abuse">
            <?= $this->translate('Пожаловаться на объявление') ?>
        </a>
    </div>
</div>

<div class="new-ads">
    <div class="new-ads__content">
        <div class="card-headline headline">
            <h2>Similar Ads</h2>
        </div>
        <div class="new-ads__items">
            <?= \Palto\Counters::get('google') ?: \Palto\Counters::receive('adx') ?>
            <?php foreach (Ads::getAds(
                $this->data['region'],
                $this->data['category'],
                5,
                0,
                $this->data['ad']->getId()
            ) as $similarAd) : ?>
                <?php if ($similarAd->getId() != $ad->getId()) : ?>
                    <?php $this->insert('partials/ad_in_list', ['ad' => $similarAd]) ?>
                <?php endif; ?>
            <?php endforeach; ?>
       </div>
   </div>
</div>

<div id="send-abuse-modal" class="modal" data-url="<?= \Palto\Config::getDomainUrl() ?>/send-feedback.php">
    <!-- Modal content -->
    <div class="modal-content">
        <div class="modal-header">
            <span class="close">&times;</span>
            <div><?= $this->translate('Пожаловаться на объявление') ?></div>
        </div>

        <form class="form">
            <table class="tbl_report">
                <tr>
                    <td class="td_report"><input type="hidden" name="page"
                                                 value="http://<?= $_SERVER['HTTP_HOST'] ?><?= $_SERVER['REQUEST_URI'] ?>"><label>Email:</label>
                    </td>
                    <td><input type="email" name="email" required></td>
                </tr>
                <tr>
                    <td class="td_report"><input type="hidden" name="ad_id"
                                                 value="<?= $ad->getId() ?>"><label><?= $this->translate('Жалоба') ?>
                            :</label></td>
                    <td><textarea name="message" rows="3"></textarea></td>
                </tr>
                <tr>
                    <td></td>
                    <td><input type="submit" value="Send" class="button"></td>
                </tr>
            </table>
        </form>

        <p class="success" style="display: none">
            <?= $this->translate('Ваша жалоба успешно отправлена.') ?>
        </p>
    </div>
</div>

<?php if ($ad->getCoordinates()) : ?>
    <?php $this->push('scripts') ?>
    <script src="<?= $this->asset('/js/leaflet.js') ?>"></script>
    <script src="<?= $this->asset('/js/swiper-bundle.min.js')?>"></script>
    <script src="<?=\Palto\Directory::getThemePublicDirectory()?>/js/js.js"></script>
    <?php $this->end() ?>
<?php endif;