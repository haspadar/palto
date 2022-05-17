<?php /** @var $this League\Plates\Template\Template */?>

<?php $this->layout('layout'); ?>
<ol class="list-group list-group">
    <?php foreach ($this->data['ads'] as $ad) :?>
        <li class="list-group-item">
            <a href="/karman/ad/<?=$ad->getId()?>?cache=0">
                <?=$ad->getTitle();?>
            </a>
            <small class="text-muted text-decoration-none">
                <?=$ad->getCreateTime()->format('d.m.Y H:i:s')?>
            </small>
            <button class="btn btn-secondary btn-sm float-end move-ad"
                    type="button"
                    data-ad-id="<?=$ad->getId()?>"
                    data-category-id="<?=$ad->getCategory()->getId()?>"
                    data-category-parent-id="<?=$ad->getCategory()->getParentId()?>"
            >
                Перенести
            </button>
        </li>

    <?php endforeach;?>
</ol>

<br>
<?php $this->insert('partials/karman-pagination', [
    'url' => isset($this->data['category'])
        ? '/karman/category-ads/' . $this->data['category']->getId() . '/%s?cache=0'
        : '/karman/ads/%s?cache=0'
])?>

<?php $this->insert('partials/karman-move-ad-modal')?>
