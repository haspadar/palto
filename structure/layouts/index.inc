<?php

use Palto\Palto;

$palto = Palto::getInstance();
$palto->setTitle('Page Example Title');
$palto->setDescription('Page Example Description');
?>
<?php require_once 'header.inc'?>
    <br/>
    <!--3rows-->
    <?php foreach ($palto->getCategories(0, 1) as $level1Category) :?>
        <div class="span-d">
            <p>
                <a href="<?=$palto->generateCategoryUrl($level1Category)?>">
                    <?php if ($level1Category['icon_url']) :?>
                        <img src="<?=$level1Category['icon_url']?>"
                             title="<?=$level1Category['icon_text']?>"
                             class="icm" />
                    <?php endif?>
                    <strong> <?=$level1Category['title']?></strong>
                </a>
            </p>
            <?php if ($level2Categories = $palto->getCategories($level1Category['id'])) :?>
                <ul>
                    <?php foreach ($level2Categories as  $level2Category) :?>
                        <li><a href="<?=$palto->generateCategoryUrl($level2Category)?>">
                                <?=$level2Category['title']?>
                            </a>
                        </li>
                    <?php endforeach;?>
                </ul>
            <?php endif;?>
        </div>
    <?php endforeach;?>

<?php require_once 'footer.inc';