<?php

use Palto\Categories;

/**
 * @var $this League\Plates\Template\Template
 */
?><!DOCTYPE html>
<html lang="<?= $this->translate('html_lang') ?>">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/img/favicon.ico">
    <link rel="stylesheet" href="<?= $this->asset(\Palto\Directory::getThemePublicDirectory() . '/css/style.css') ?>">
    <link rel="stylesheet" href="<?= $this->asset('/css/styles.css') ?>">
    <?php if (!isset($pager) || $pager->getPageNumber() == 1) : ?>
        <link rel="canonical" href="<?=\Palto\Url::getCurrentUrl()?>">
    <?php endif;?>

    <?php if (\Palto\Settings::isKarmanPanelEnabled()) : ?>
        <link rel="stylesheet" href="<?= $this->asset('/css/karman.css') ?>">
    <?php endif; ?>

    <?= $this->section('styles') ?>

    <title><?= $this->translate($this->data['title']) ?></title>
    <meta name="description" content="<?= $this->translate($this->data['description']) ?>">

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
    <div class="wrapper">
        <header class="header">
            <div class="container">
                <div class="header__content">
                        <?php if (file_exists(\Palto\Directory::getPublicDirectory() . '/img/logo.png')) : ?>
                            <a href="/" class="header__logo">
                                <img src="<?='/img/logo.png'?>" alt="<?= $this->translate('logo_alt') ?>" class="mylogo"/>
                            </a>
                        <?php endif; ?>

                    <nav class="header__menu">
                        <ul class="header__list">
                            <?php /** @var $popularLevel1Category \Palto\Category */ ?>
                            <?php foreach (Categories::getLiveCategories(null, $this->data['region'], \Palto\Config::get('HEADER_LAYOUT_MENU')) as $popularLevel1Category) : ?>
                                <li class="header__link">
                                    <a href="<?= $popularLevel1Category->generateUrl($this->data['region']) ?>" rel="nofollow">
                                        <?= $popularLevel1Category->getTitle() ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>

                        </ul>
                    </nav>
                    <a href="/registration" class="header__create"><?= $this->translate('???????????????? ????????????????????') ?></a>
                </div>
            </div>
        </header>
        <div class="background">
            <main class="main container">
                <div class="search">
                    <?php if ($counter = \Palto\Counters::get('google_search')) : ?>
                        <?= $counter ?>
                        <div class="gcse-search"></div>
                    <?php endif; ?>
                </div>
                <?php if ($this->data['breadcrumbs']) : ?>
                    <?= $this->insert('partials/breadcrumbs'); ?>
                <?php endif; ?>

                <?php if ($this->data['flash']) : ?>
                    <div class="alert"><?= $this->data['flash'] ?></div>
                <?php endif; ?>

                <?= $this->section('content') ?>
            </main>
            <footer class="footer">
                <div class="footer__content container">
                    <?= $this->translate('footer_text') ?> <?= \Palto\Counters::get('liveinternet') ?>
                </div>
            </footer>

            <div id="accept_notification">
                <div><?= $this->translate('cookie_text') ?></div>
                <button class="button cookie_accept"><?= $this->translate('????????????????') ?></button>
            </div>

            <script src="<?=$this->asset('/js/jquery.min.js')?>"></script>
            <script src="<?=$this->asset('/js/moderation.js')?>"></script>
            <script src="<?=$this->asset('/js/script.js')?>"></script>
            <script src="<?=$this->asset(\Palto\Directory::getThemePublicDirectory() . '/js/cookies.js')?>"></script>
            <?php if (\Palto\Settings::isKarmanPanelEnabled()) :?>
                <script src="<?=$this->asset('/js/karman-panel.js')?>"></script>
            <?php endif;?>

            <?= $this->section('scripts') ?>

            <?php if (\Palto\Settings::isKarmanPanelEnabled()) :?>
                <?= $this->insert('../karman/partials/karman-panel'); ?>
            <?php endif;?>

        </div>
    </div>
</body>
</html>