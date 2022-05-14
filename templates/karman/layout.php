<!doctype html>
<html lang="ru">
<?php
/**
 * @var $this League\Plates\Template\Template
 */
?>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $this->data['title'] ?></title>
    <link rel="stylesheet" href="<?= $this->asset('/bootstrap/css/bootstrap.css') ?>">
    <link rel="stylesheet" href="<?=$this->asset('/css/karman.css')?>">
    <meta name="theme-color" content="#7952b3">
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
            <div class="navbar-brand col-md-3 col-lg-2 me-0 px-3"><img src="/coat-with-pockets.png" alt="Karman" height="30"> Karman</div>
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link " aria-current="page" href="/karman/status?cache=0">
                            <h7 class="<?php if ($this->data['url']->getPath() == '/karman/status') : ?>fw-bold<?php endif; ?>">
                                Приборы
                            </h7>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/karman/complaints?cache=0">
                            <h7 class="<?php if ($this->data['url']->getPath() == '/karman/complaints') : ?>fw-bold<?php endif; ?>">
                                Жалобы
                                <?php if ($this->data['actual_complaints_count']) : ?>
                                    <span class="badge bg-warning"><?= $this->data['actual_complaints_count'] ?></span>
                                <?php endif; ?>
                            </h7>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/karman/categories?cache=0">
                            <h7 class="<?php if (in_array($this->data['url']->getPath(), ['/karman/categories', '/karman/category-ads'])) : ?>fw-bold<?php endif; ?>">
                                Категории
                            </h7>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/karman/ads?cache=0">
                            <h7 class="<?php if (in_array($this->data['url']->getPath(), ['/karman/ads', '/karman/ad'])) : ?>fw-bold<?php endif; ?>">
                                Объявления
                            </h7>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="/karman/info-logs-directories?cache=0">
                            <h7 class="<?php if ($this->data['url']->isStartsAt(['/karman/info-logs-directories', '/karman/info-logs'])) : ?>fw-bold<?php endif; ?>">
                                Логи
                            </h7>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="/karman/error-logs-directories?cache=0">
                            <h7 class="<?php if ($this->data['url']->isStartsAt(['/karman/error-logs-directories', '/karman/error-logs'])) : ?>fw-bold<?php endif; ?>">
                                Ошибки
                            </h7>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <?php if ($this->data['breadcrumbs']) : ?>
                <nav aria-label="breadcrumb" class="pt-3 pb-1 mb-1">
                    <ol class="breadcrumb">
                        <?php foreach ($this->data['breadcrumbs'] as $breadcrumbUrl) : ?>
                            <?php if ($breadcrumbUrl['url'] ?? '') : ?>
                                <li class="breadcrumb-item"><a
                                            href="<?= $breadcrumbUrl['url'] ?>"><?= $breadcrumbUrl['title'] ?></a></li>
                            <?php else : ?>
                                <li class="breadcrumb-item active"
                                    aria-current="page"><?= $breadcrumbUrl['title'] ?></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ol>
                </nav>

            <?php endif; ?>

            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
                <h1><?= $this->data['title'] ?></h1>
            </div>

            <?php if ($this->data['flash']) : ?>
                <?php $decoded = json_decode($this->data['flash'], JSON_OBJECT_AS_ARRAY) ?>
                <div class="alert alert-<?= $decoded['type'] ?> alert-dismissible mt-2 fade show" role="alert">
                    <?= $decoded['message'] ?>
                </div>
            <?php endif; ?>

            <?= $this->section('content') ?>
        </main>
    </div>
</div>

<script src="<?=$this->asset('/js/jquery.min.js')?>"></script>
<script src="<?=$this->asset('/bootstrap/js/bootstrap.js')?>"></script>
<script src="<?=$this->asset('/bootstrap/js/bootstrap.bundle.js')?>"></script>
<script src="<?=$this->asset('/clipboard/dist/clipboard.min.js')?>"></script>
<script src="<?=$this->asset('/js/emoji.min.js')?>"></script>
<script src="<?=$this->asset('/js/karman.js')?>"></script>

</body>
<script></script>
</html>
