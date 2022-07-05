<?php /** @var $this League\Plates\Template\Template */?>

<?php $this->layout('layout');?>


<table class="table">
    <thead>
    <tr>
        <th>Шаблон</th>
        <th>Страницы</th>
    </tr>
    </thead>
    <tbody>

    <?php
    /**
     * @var \Palto\Template $template
     */
    foreach ($this->data['templates'] as $template) :?>
        <tr>
                <td>
                    <?=$template->getName()?>
                </td>
                <td>
                    <small>
                        <?php
                        /**
                         * @var \Palto\Page $page
                         */
                        foreach ($this->data['pages'] as $page) :?>
                            <?php if ($page->getTemplateId() == $template->getId()) :?>
                                <a href="/karman/pages/<?=$page->getId()?>?cache=0">
                                    <span class="badge badge-<?=($page->isEnabled() ? 'success' : 'secondary')?>">
                                        <?=$page->getName()?>
                                    </span>
                                </a>
                            <?php endif;?> 
                        <?php endforeach;?>
                        
                    </small>
                </td>
            </tr>

    <?php endforeach;?>
    </tbody>
</table>