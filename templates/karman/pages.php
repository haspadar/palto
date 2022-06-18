<?php /** @var $this League\Plates\Template\Template */?>

<?php $this->layout('layout');?>


<table class="table">
    <thead>
    <tr>
        <th>Страница</th>
        <th>Шаблон</th>
    </tr>
    </thead>
    <tbody>


    <?php
    /**
     * @var \Palto\Page $page
     */
    foreach ($this->data['pages'] as $page) :?>
        <?php $url = '/karman/pages/' . $page->getId() . '?cache=0';?>
        <tr>
                <td>
                    <?php if ($page->isEnabled()) :?>
                        <span class="badge rounded-circle bg-success bulb">&nbsp;</span>
                    <?php else :?>
                        <span class="badge rounded-circle bg-danger bulb">&nbsp;</span>
                    <?php endif;?>

                    <a href="<?=$url?>" class="text-decoration-none">
                        <?=$page->getComment()?>
                    </a>
                </td>
                <td>
                    <small class="text-muted">
                        <a href="/karman/pages/<?=$page->getId()?>?cache=0">
                            <span class="badge badge-secondary">
                                <?=$page->getTemplate()->getName()?>
                            </span>
                        </a>

                    </small>
                </td>
            </tr>

    <?php endforeach;?>
    </tbody>
</table>