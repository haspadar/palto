<!DOCTYPE html>
<html lang="ru-RU">
<?php 
/**
 * @var $this \Palto\Palto
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
    <link rel="stylesheet" href="/css/styles.css?v=<?=time()?>">
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
<!--    <script data-ad-client="ca-pub-4251434934288140" async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>-->
</head>
<body>
    <table class="tbl_base">
        <tr class="trheader">
            <td class="fifty"></td>
            <td class="tdheader">
                <table class="top_tbl">
                    <tr>
                        <td>
                            <?php if (file_exists($this->getPublicDirectory() . '/images/logo.png')) :?>
                                <a href="/"><img src="/images/logo.png" alt="My Logo" class="mylogo"/></a>
                            <?php endif;?>
                            <div class="main_menu">
                                <table class="tbl_menu">
                                    <tr>
                                        <td>
                                            <?php foreach ($this->getCategories(0, 1, 5) as $popularLevel1Category) :?>
                                                <a class="menu" href="<?=$this->generateCategoryUrl($popularLevel1Category)?>" rel="nofollow"><?=$popularLevel1Category['title']?></a> <span class="whitem">|</span>
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
            <td></td>
            <td class="table_main">