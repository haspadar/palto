<?php
/**
 * @var $this \Palto\Palto
 */
$paginationUrls = $this->getPartialVariable('paginationUrls');
?>
<?php if ($paginationUrls) : ?>
    <div>
        <table class="pager">
            <tr>
                <td>
                    <?php var_dump($paginationUrls)?>
                    <?php foreach ($paginationUrls as $page) :?>
                        <?php if ($page['url']) :?>
                            <a href="<?=$page['url']?>"><?=$page['title']?></a>
                        <?php elseif (is_numeric($page['title'])) :?>
                            <a class="active"><?=$page['title']?></a>
                        <?php else :?>
                            <a><?=$page['title']?></a>
                        <?php endif;?>
                    <?php endforeach;?>

                    <div class="c"></div>
                </td>
            </tr>
        </table>
    </div>
<?php endif; ?>