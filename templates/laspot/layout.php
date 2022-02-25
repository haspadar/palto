<?php

use Palto\Categories;

/**
 * @var $this League\Plates\Template\Template
 */
?>
<!DOCTYPE html>
<html lang="<?= $this->translate('html_lang') ?>">
<head>
    <meta charset="utf-8">
    <title><?= $this->translate($this->data['title']) ?></title>
    <meta name="description" content="<?= $this->translate($this->data['description']) ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/img/favicon.ico">
    <link rel="stylesheet" href="<?=$this->asset('/laspot-theme/css/styles.css')?>">
    <link rel="stylesheet" href="<?=$this->asset('/css/styles.css')?>">
    <?= $this->section('styles') ?>

    <?php if (isset($pager) && $pager instanceof \Palto\Pager) : ?>
        <?php /** @var $pager \Palto\Pager */ ?>
        <?php if ($pager->getPreviousPageUrl()) : ?>
            <meta name="robots" content="noindex, follow"/>
            <link rel="prev" href="<?= $pager->getPreviousPageUrl() ?>">
        <?php endif; ?>

        <?php if ($pager->getNextPageUrl()) : ?>
            <link rel="next" href="<?= $pager->getNextPageUrl() ?>">
        <?php endif; ?>
    <?php endif; ?>

    <?= \Palto\Counters::get('google_header') ?>
</head>
<body>
    <div class="js-vars">
        <input type="hidden" name="domain" value="<?=\Palto\Directory::getProjectName()?>">
    </div>
    <table class="tbl_base">
        <tr class="trheader">
            <td class="fifty"></td>
            <td class="tdheader">
                <table class="top_tbl">
                    <tr>
                        <td>
                            <?php if (file_exists(\Palto\Directory::getPublicDirectory() . '/img/logo.png')) : ?>
                                <a href="/"><img src="/img/logo.png" alt="<?= $this->translate('logo_alt') ?>"
                                                 class="mylogo"/></a>
                            <?php endif; ?>
                            <div class="main_menu">
                                <table class="tbl_menu">
                                    <tr>
                                        <td>
                                            <?php /** @var $popularLevel1Category \Palto\Category */ ?>
                                            <?php foreach (Categories::getLiveCategories(null, $this->data['region'], \Palto\Config::get('HEADER_LAYOUT_MENU')) as $popularLevel1Category) : ?>
                                                <a class="menu"
                                                   href="<?= $popularLevel1Category->generateUrl($this->data['region']) ?>"
                                                   rel="nofollow"><?= $popularLevel1Category->getTitle() ?></a> <span
                                                        class="whitem">|</span>
                                            <?php endforeach; ?>
                                        </td>
                                        <td align="righ">
                                            <strong>
                                                ✍🏻<a href="/registration"><?= $this->translate('Добавить объявление') ?></a>
                                            </strong>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
            <td class="fifty"></td>
        </tr>
        <tr>
            <td class="tdleft"></td>
            <td class="table_main">
                <?php if ($counter = \Palto\Counters::get('google_search')) : ?>
                    <?= $counter ?>
                    <div class="gcse-search"></div>
                <?php endif; ?>

                <?php if ($this->data['breadcrumbs']) : ?>
                    <?= $this->insert('partials/breadcrumbs'); ?>
                <?php endif; ?>

                <?php if ($this->data['h1']) :?>
                    <h1><?= $this->data['h1'] ?></h1>
                <?php endif;?>

                <?php if ($this->data['flash']) : ?>
                    <div class="alert"><?= $this->data['flash'] ?></div>
                <?php endif; ?>

                <?= $this->section('content') ?>

            </td>
            <td class="tdright"></td>
        </tr>
        <tr>
            <td class="tdfooter"></td>
            <td class="tdfooter"><?= $this->translate('footer_text') ?> <?= \Palto\Counters::get('liveinternet') ?></td>
            <td class="tdfooter"></td>
        </tr>
    </table>

    <div id="cookie_notification">
        <div><?= $this->translate('cookie_text') ?></div>
        <button class="button cookie_accept"><?= $this->translate('СОГЛАСЕН') ?></button>
    </div>

    <script src="<?=$this->asset('/js/jquery.min.js')?>"></script>
    <script src="<?=$this->asset('/js/cookies.js')?>"></script>
    <script src="<?=$this->asset('/js/slider.js')?>"></script>
    <script src="<?=$this->asset('/js/moderation.js')?>"></script>
    <script src="<?=$this->asset('/js/script.js')?>"></script>
    <?= $this->section('scripts') ?>
</body>
</html>