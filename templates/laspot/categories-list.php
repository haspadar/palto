<?php /** @var $this League\Plates\Template\Template */?>

<?php $this->layout('layout');?>

<?=\Palto\Counters::get('google') ?: \Palto\Counters::receive('adx')?>
<?php foreach (\Palto\Categories::getLiveCategories() as $level1Category) :?>
    <div class="span-d regions"><a href="<?=$level1Category->generateUrl($this->data['region'])?>"><strong> <?=$level1Category->getTitle()?></strong></a>
        <?php if ($level2Categories = $level1Category->getLiveChildren($this->data['region'])) :?>
            <ul>
                <?php foreach ($level2Categories as $level2Category) :?>
                    <li>
                        <ul>
                            <a href="<?=$level2Category->generateUrl($this->data['region'])?>">
                                <?=$level2Category->getTitle()?>
                            </a>
                            <?php if ($level3Categories = $level2Category->getLiveChildren($this->data['region'])) :?>
                                <ul>
                                <?php foreach ($level3Categories as $level3Category) :?>
                                    <li>
                                        <a href="<?=$level2Category->generateUrl()?>">
                                            <?=$level3Category->getTitle()?>
                                        </a>
                                    </li>
                                <?php endforeach;?>
                                </ul>
                            <?php endif;?>
                        </ul>

                    </li>
                <?php endforeach;?>
            </ul>

        <?php endif;?>
    </div>
<?php endforeach;