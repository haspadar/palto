<!doctype html>
<html lang="ru">
<?php
/**
 * @var $this \Palto\Layout\Karman
 */
?>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?=$this->getPartialVariable('title')?></title>

    <!-- Bootstrap core CSS -->
    <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Favicons -->
    <meta name="theme-color" content="#7952b3">

    <!-- Custom styles for this template -->
<!--    <link href="dashboard.css" rel="stylesheet">-->
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
            <div class="navbar-brand col-md-3 col-lg-2 me-0 px-3">Karman</div>
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link " aria-current="page" href="/karman/status">
                            <h7 class="<?php if ($this->getUrl() == '/karman/status') :?>fw-bold<?php endif;?>">Статус</h7>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/karman/complaints">
                            <h7 class="<?php if ($this->getUrl() == '/karman/complaints') :?>fw-bold<?php endif;?>">Жалобы
                                <?php if ($count = \Palto\Complaints::getActualComplaintsCount()) :?>
                                    <span class="badge bg-warning"><?=$count?></span>
                                <?php endif;?>
                            </h7>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/karman/categories">
                            <h7 class="<?php if ($this->getUrl() == '/karman/categories') :?>fw-bold<?php endif;?>">Категории</h7>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <?php if ($breadcrumbUrls = $this->getPartialVariable('breadcrumbUrls')) :?>
                <nav aria-label="breadcrumb" class="pt-3 pb-1 mb-1">
                    <ol class="breadcrumb">
                        <?php foreach ($breadcrumbUrls as $breadcrumbUrl) :?>
                            <?php if ($breadcrumbUrl['url'] ?? '') :?>
                                <li class="breadcrumb-item"><a href="<?=$breadcrumbUrl['url']?>"><?=$breadcrumbUrl['title']?></a></li>
                            <?php else :?>
                                <li class="breadcrumb-item active" aria-current="page"><?=$breadcrumbUrl['title']?></li>
                            <?php endif;?>
                        <?php endforeach;?>
                    </ol>
                </nav>

            <?php endif;?>

            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
                <h1 class="h2"><?=$this->getPartialVariable('title')?></h1>
            </div>

            <?php if ($flashMessage = \Palto\Flash::get()) :?>
                <?php $decoded = json_decode($flashMessage, JSON_OBJECT_AS_ARRAY)?>
                <div class="alert alert-<?=$decoded['type']?> alert-dismissible mt-2 fade show" role="alert">
                    <?=$decoded['message']?>
                </div>
            <?php endif;?>