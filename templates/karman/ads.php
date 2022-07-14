<?php /** @var $this League\Plates\Template\Template */?>

<?php $this->layout('layout'); ?>
<!--<div class="btn-group btn-group-sm" role="group" aria-label="Basic outlined example">-->
<!--    <button type="button" class="btn btn-outline-primary active">Все</button>-->
<!--    <button type="button" class="btn btn-outline-primary">Сегодня</button>-->
<!--    <button type="button" class="btn btn-outline-primary">Вчера</button>-->
<!--</div>-->

<table class="table">
    <thead>
        <th>Заголовок</th>
        <th>Категория</th>
        <th>Время</th>
        <th></th>
    </thead>
    <tbody>
    <?php foreach ($this->data['ads'] as $ad) :?>
        <?php $categoriesTitle = $ad->getCategoryPath();?>
        <tr class="<?php if ($ad->getCategory()?->isUndefined()) :?>
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
                <small class="text-muted text-decoration-none" title="<?=$ad->getCreateTime()->format('d.m.Y H:i:s')?>">
                    <?php if ($ad->getCreateTime()->format('d.m.Y') == (new DateTime())->format('d.m.Y')) :?>
                        сегодня в <?=$ad->getCreateTime()->format('H:i:s')?>
                    <?php elseif ($ad->getCreateTime()->format('d.m.Y') == (new DateTime())->modify('1 DAY')->format('d.m.Y')) :?>
                        вчера в <?=$ad->getCreateTime()->format('H:i:s')?>
                    <?php elseif ($ad->getCreateTime()->format('d.m.Y') == (new DateTime())->modify('2 DAY')->format('d.m.Y')) :?>
                        позавчера в <?=$ad->getCreateTime()->format('H:i:s')?>
                    <?php else : ?>
                        <?=$ad->getCreateTime()->format('d.m.Y H:i:s')?>
                    <?php endif; ?>

                </small>
            </td>
            <td>

                <button class="btn btn-secondary btn-sm float-end move-ad"
                        type="button"
                        data-ad-id="<?=$ad->getId()?>"
                        data-category-id="<?=$ad->getCategory()?->getId()?>"
                        data-category-parent-id="<?=$ad->getCategory()?->getParentId()?>"
                >
                    Перенести
                </button>
            </td>
        </tr>
    <?php endforeach;?>
    </tbody>
</table>

<br>
<?php $this->insert('partials/karman-pagination', [
    'url' => isset($this->data['category'])
        ? '/karman/category-ads/' . $this->data['category']->getId() . '/%s?cache=0'
        : '/karman/ads/%s?cache=0'
])?>

<?php $this->insert('partials/karman-move-ad-modal')?>
