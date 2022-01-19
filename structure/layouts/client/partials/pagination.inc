<?php
/**
 * @var $this \Palto\Layout\Client\Client\Client\Client\Client\Client\Client\Client\Client\Client\Client\Client\Client\Client\Client\Client\Client
 */
$paginationUrls = $this->getPartialVariable('paginationUrls');
?>
<?php if ($paginationUrls) : ?>
    <div class="pager">| 
        <?php foreach ($paginationUrls as $page) :?>
            <?php if ($page['url']) :?><a href="<?=$page['url']?>"><?=$page['title']?></a> | 
            <?php elseif (is_numeric($page['title'])) :?><a class="active_page"><?=$page['title']?></a> | 
            <?php else :?><a><?=$page['title']?></a> | 
            <?php endif;?>
        <?php endforeach;?>
    </div>
<?php endif; ?>