<?php
/**
 * @var $this \Palto\Palto
 */
$this->partial('header.inc', [
    'title' => 'Title: ' . implode(
            ' - ',
            array_filter(array_merge(
                 array_column($this->getCurrentCategory()['parents'], 'title'),
                 [$this->getCurrentCategory()['title']],
                 [$this->getCurrentRegion()['title']]
             ))
        ),
    'description' => 'Category Description: '
        . implode(
            ' - ',
            array_filter(array_merge(
                 array_column($this->getCurrentCategory()['parents'], 'title'),
                 [$this->getCurrentCategory()['title']],
                 [$this->getCurrentRegion()['title']]
             ))
        ),
    'nextPageUrl' => $this->getNextPageUrl(),
    'previousPageUrl' => $this->getPreviousPageUrl(),
]);

//$sectionsTitle = array_filter([
//                                  $ad['title'] . ' køb i ' . $ad['city_title'],
//                                  $ad['sub_section_title'],
//                                  $ad['section_title']
//                              ]);
//Danannoncer::$title = implode(' - ', $sectionsTitle);
//Danannoncer::$description = str_replace(PHP_EOL, ' ', Danannoncer::getAdShortDescription($ad));

//$cityUrl = $city ? '/' . $city['url'] . '/' : '/' . Danannoncer::BR_CITY['url'] . '/';
//$category = Danannoncer::getCategoryById($ad['category_id']);
?>
<?php //require_once 'header.inc'?>
    <div id="col-mid">
        <div class="col-mid-inner">
            <div class="cmi-1">
                <div class="cmi-1-1">
                    <div class="block bread">
                        <div class="bl1">
                            <div class="b1" itemscope itemtype="http://schema.org/BreadcrumbList">
                                <?php foreach ($this->getAdBreadcrumbUrls() as $breadcrumbKey => $breadcrumbItem) : ?>
                                    <?php if ($breadcrumbKey) : ?>
                                        <span class="sep">»</span>
                                    <?php endif;?>

                                    <span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                                        <a itemprop="item" href="<?=$breadcrumbItem['url']?>" class="bread">
                                            <span itemprop="name"><?= $breadcrumbItem['title'] ?></span>
                                        </a>
                                        <meta itemprop="position" content="<?=$breadcrumbKey + 1?>"/>
                                    </span>
                                <?php endforeach;?>
                            </div>
                        </div>
                    </div>
                    <div class="main-ob">
                        <div class="block">
                            <div class="bl1">
                                <h1><?=$this->getCurrentAd()['title']?> <span style="color:#999"> in <?=$this->getCurrentRegion()['title']?></span></h1>
                                <?php if ($this->getCurrentAd()['images']) :?>
                                    <!-- Slideshow container -->
                                    <div class="slideshow-container">
                                        <!-- Full-width images with number and caption text -->
                                        <?php foreach ($this->getCurrentAd()['images'] as $key => $image) :?>
                                            <div class="mySlides fade">
                                                <div class="numbertext"><?=$key + 1?> / <?=count($this->getCurrentAd()['images'])?></div>
                                                <img src="<?=$image['big']?>" style="width:100%" loading="lazy">
                                            </div>
                                        <?php endforeach;?>

                                        <!-- Next and previous buttons -->
                                        <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
                                        <a class="next" onclick="plusSlides(1)">&#10095;</a>
                                    </div>
                                    <br>

                                    <!-- The dots/circles -->
                                    <div style="text-align:center;">
                                        <?php foreach ($this->getCurrentAd()['images'] as $key => $image) :?>
                                            <span class="dot" onclick="currentSlide(<?=$key + 1?>)"></span>
                                        <?php endforeach;?>
                                    </div>

                                <?php endif;?>
                                <br>
                                <div class="youtube myvideo" data-url="<?='/youtube.php?query=' . urlencode($this->getCurrentAd()['title'])?>"></div>

                                <?php if ($this->getCurrentAd()['details']) :?>
                                    <ul>
                                        <?php foreach ($this->getCurrentAd()['details'] as $field => $value) :?>
                                            <li><?=$field?>: <?=$value?></li>
                                        <?php endforeach;?>
                                    </ul>
                                <?php endif;?>
                            </div>
                        </div>
                        <div class="block">
                            <div class="bl1">
                                <div class="short-info">
                                    <div class="si1">
                                        <div class="price">🏷 <?=$this->getCurrentAd()['price']?> <span><?=$this->getCurrentAd()['currency']?></span> </div>
                                        <div class="si3">
                                            <div class="si3a">
                                                <div class="map"><?=$this->getCurrentAd()['address'] ?? ''?></div>
                                                <div class="param">⏱ Post time: <?=(new DateTime($this->getCurrentAd()['post_time']))->format('d.m.Y')?> </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="block">
                            <div class="bl1">
                                <div class="descr"> <?=urldecode($this->getCurrentAd()['text'])?> </div>
                                <div class="phone-block">
                                    <div class="pb1"></div>
                                    <div class="pb2">
                                        <p>
                                        <div class="seller">💁‍♂️ <?=$this->getCurrentAd()['seller_name']?></div>
                                        <div><?=$this->getCurrentAd()['seller_postfix'] ?? ''?></div>
                                        </p>
                                        <?php if ($this->getCurrentAd()['address']) :?>
                                            <div>
                                                📍Adresse: <?=$this->getCurrentAd()['address']?>
                                            </div>
                                        <?php endif;?>

                                        <div>📞 <a class="show-phone phone" id="show-phone" data-phone="<?=$this->getCurrentAd()['seller_phone']?>">
                                                <?php if ($this->getCurrentAd()['seller_phone']) :?>
                                                    Vis telefon
                                                <?php else :?>
                                                    Telefon ikke angivet
                                                <?php endif;?></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="block">
                            <div class="bl1">
                                <h2>Similar ads</h2>
                                <?php foreach ($this->getAds($this->getCurrentCategory()['id'], $this->getCurrentRegion()['id'], 5) as $similarAd) :?>
                                    <?php $this->partial('ad.inc', ['ad' => $similarAd])?>
                                <?php endforeach;?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->partial('footer.inc');