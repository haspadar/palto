<?php /** @var $this League\Plates\Template\Template */?>

<?php $this->layout('layout'); ?>
<table class="table">
    <thead>
        <th>Заголовок</th>
        <th>Категория</th>
        <th>Время</th>
    </thead>
    <?php foreach ($this->data['ads'] as $ad) :?>
        <?php $categoriesTitle = $ad->getCategoriesTitle();?>
        <tr class="<?php if ($ad->getCategory()->isUndefined()) :?>
                table-secondary
            <?php endif;?>"
        >
            <td>
                <a href="/karman/ad/<?=$ad->getId()?>?cache=0">
                    <?=$ad->getTitle();?>
                </a>
            </td>
            <td class="small text-muted">
                <?=$categoriesTitle?>
            </td>
            <td>
                <small class="text-muted text-decoration-none">
                    <?=$ad->getCreateTime()->format('d.m.Y H:i:s')?>
                </small>
            </td>
            <td>

                <!--<button class="btn btn-secondary btn-sm float-end move-ad"
                        type="button"
                        data-ad-id="<?/*=$ad->getId()*/?>"
                        data-category-id="<?/*=$ad->getCategory()->getId()*/?>"
                        data-category-parent-id="<?/*=$ad->getCategory()->getParentId()*/?>"
                >
                    Перенести
                </button>-->
            </td>
        </tr>
    <?php endforeach;?>
</table>

<br>
<?php $this->insert('partials/karman-pagination', [
    'url' => isset($this->data['category'])
        ? '/karman/category-ads/' . $this->data['category']->getId() . '/%s?cache=0'
        : '/karman/ads/%s?cache=0'
])?>

<?php $this->insert('partials/karman-move-ad-modal')?>
