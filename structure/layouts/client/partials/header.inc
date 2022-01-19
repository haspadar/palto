<!DOCTYPE html>
<html lang="<?=$this->translate('html_lang')?>">
<?php 
/**
 * @var $this \Palto\Layout\Client\Client
 */
$title = $this->getPartialVariable('title');
$description = $this->getPartialVariable('description');
?>
<head>
    <meta charset="utf-8">
    <title><?=$this->getPartialVariable('title')?></title>
    <meta name="description" content="<?=$this->getPartialVariable('description')?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/favicon.ico?1">
    <link rel="stylesheet" href="/css/styles.css?v=<?=hash_file('md5', $this->getPublicDirectory() . '/css/styles.css')?>">
    <?php if ($this->getPartialVariable('css')) :?>
        <?php foreach ($this->getPartialVariable('css') as $css) :?>
            <?php foreach ($css as $cssAttribute => $cssValue) :?>
                <link rel="stylesheet" <?=$cssAttribute?>="<?=$cssValue?>">
            <?php endforeach;?>
        <?php endforeach;?>
    <?php endif;?>

    <?php if ($this->getPartialVariable('previousPageUrl')) :?>
        <meta name="robots" content="noindex, follow" />
        <link rel="prev" href="<?=$this->getPartialVariable('previousPageUrl')?>">
    <?php endif;?>

    <?php if ($this->getPartialVariable('nextPageUrl')) :?>
        <link rel="next" href="<?=$this->getPartialVariable('nextPageUrl')?>">
    <?php endif;?>

    <?php if ($this->getPartialVariable('css')) :?>
        <?php foreach ($this->getPartialVariable('css') as $css) :?>
            <?php foreach ($css as $cssAttribute => $cssValue) :?>
                <link rel="stylesheet" <?=$cssAttribute?>="<?=$cssValue?>">
            <?php endforeach;?>
        <?php endforeach;?>
    <?php endif;?>
</head>
<body>
    <table class="tbl_base">
        <tr class="trheader">
            <td class="fifty"></td>
            <td class="tdheader">
                <table class="top_tbl">
                    <tr>
                        <td>
                            <?php if (file_exists($this->getPublicDirectory() . '/img/logo.png')) :?>
                                <a href="/"><img src="/img/logo.png" alt="<?=sprintf($this->translate('Частные бесплатные объявления в %s'), $this->getRegion()->getTitle())?>" class="mylogo"/></a>
                            <?php endif;?>
                            <div class="main_menu">
                                <table class="tbl_menu">
                                    <tr>
                                        <td>
                                            <?php foreach ($this->getWithAdsCategories( null, 5) as $popularLevel1Category) :?>
                                                <a class="menu" href="<?=$this->generateCategoryUrl($popularLevel1Category)?>" rel="nofollow"><?=$popularLevel1Category->getTitle()?></a> <span class="whitem">|</span>
                                            <?php endforeach;?>

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
                <form action="/search" method="get" class="search">
                    <input type="search" name="query" value="<?=$this->getSearchQuery()?>" placeholder="Find what yo need..." class="input" />
                    <input type="submit" name="" value="" class="submit" />
                </form>