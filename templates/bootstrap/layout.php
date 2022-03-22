<?php

use Palto\Categories;

/**
 * @var $this League\Plates\Template\Template
 */
?><!doctype html>
<html lang="<?= $this->translate('html_lang') ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="<?= $this->translate($this->data['description']) ?>">
    <link rel="icon" type="image/png" href="/img/favicon.ico">
    <link rel="stylesheet" href="<?=$this->asset(\Palto\Directory::getThemePublicDirectory() . '/css/styles.css')?>">
    <link rel="stylesheet" href="<?=$this->asset('/css/styles.css')?>">
    <?php if (\Palto\Auth::isLogged()) :?>
        <link rel="stylesheet" href="<?=$this->asset('/css/karman.css')?>">
    <?php endif;?>

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

    <link href="/bootstrap/css/bootstrap.css" rel="stylesheet">
<!--    <link href="/bootstrap-icons-1.8.1/font/bootstrap-icons.css" rel="stylesheet">-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">



    <!-- Custom styles for this template -->
    <link rel="stylesheet" href="<?=$this->asset(\Palto\Directory::getThemePublicDirectory() . '/css/styles.css')?>">
    <link rel="stylesheet" href="<?=$this->asset('/css/styles.css')?>">

    <title><?= $this->translate($this->data['title']) ?></title>
</head>

<body>
<div class="js-vars">
    <input type="hidden" name="domain" value="<?=\Palto\Directory::getProjectName()?>">
</div>
<nav class="navbar navbar-expand-sm navbar-light bg-light">
    <div class="container">
        <div class="collapse navbar-collapse " id="navbarSupportedContent">
            <?php if (file_exists(\Palto\Directory::getPublicDirectory() . '/img/logo.png')) : ?>
                <a class="navbar-brand" href="/"><img src="/img/logo.png" alt="<?= $this->translate('logo_alt') ?>"
                                                      width="198"
                                                      onerror="this.src='/img/no-photo.png'"
                                                      class="logo"/></a>
            <?php endif; ?>

            <div class="mx-auto">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
            <ul class="navbar-nav me-auto order-0">
                <?php /** @var $popularLevel1Category \Palto\Category */ ?>
                <?php foreach (\Palto\Categories::getLiveCategories(null, $this->data['region'], \Palto\Config::get('HEADER_LAYOUT_MENU')) as $popularLevel1Category) : ?>
                    <li class="nav-item px-3">
                        <a class="nav-link" href="<?= $popularLevel1Category->generateUrl($this->data['region']) ?>">
                            <?= $popularLevel1Category->getTitle() ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <form class="d-flex ms-auto order-5">
                <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-pencil-square"></i>
                    <?=$this->translate('Добавить объявление')?></button>
            </form>
        </div>
    </div>
</nav>
<div class="container">
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

    <div id="cookie_notification">
        <div><?= $this->translate('cookie_text') ?></div>
        <button class="button cookie_accept"><?= $this->translate('СОГЛАСЕН') ?></button>
    </div>
    <footer class="pt-4 my-md-5 pt-md-5 border-top">
        <div class="row">
            <?= $this->translate('footer_text') ?> <?= \Palto\Counters::get('liveinternet') ?>
        </div>
    </footer>
</div>


<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="/bootstrap/js/bootstrap.js"></script>
<script src="<?=$this->asset('/js/jquery.min.js')?>"></script>
<script src="<?=$this->asset('/js/cookies.js')?>"></script>
<script src="<?=$this->asset('/js/slider.js')?>"></script>
<script src="<?=$this->asset('/js/moderation.js')?>"></script>
<script src="<?=$this->asset('/js/script.js')?>"></script>
<script src="<?=$this->asset('/js/karman-panel.js')?>"></script>
<?= $this->section('scripts') ?>
<?= $this->insert('../karman/partials/karman-panel'); ?>
</body>
</html>
