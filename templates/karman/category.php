<?php

use Palto\Categories;
use Palto\Category;
use Palto\Debug;

?>
<?php /** @var $this League\Plates\Template\Template */ ?>

<?php $this->layout('layout'); ?>
<form class="category">
    <div class="mb-3">
        <label class="form-label">Название</label>
        <input type="text" class="form-control" name="title" value="<?= $this->data['category']->getTitle() ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Url</label>
        <input type="text" class="form-control" name="url" value="<?= $this->data['category']->getUrl() ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Синонимы (через запятую)</label>
        <input type="text" class="form-control" name="synonyms"
               value="<?= $this->data['category']->getGroupedSynonyms() ?>">
    </div>
    <div class="mb-3">
        <button id="emoji-button" class="btn btn-outline-secondary"
                type="button"><?= $this->data['category']->getEmoji() ?: 'Emoji' ?></button>
        <?php if ($this->data['category']->getEmoji()) : ?>
            <a href="javascript:void(0);" class="small text-danger text-decoration-none remove-emoji"
               data-id="<?= $this->data['category']->getId() ?>">Удалить</a>
        <?php endif; ?>
    </div>

    <br>
    <div class="d-flex justify-content-between">
        <button type="submit" class="btn btn-primary" data-id="<?= $this->data['category']->getId() ?>">Сохранить</button>
        <button type="button"
                class="btn btn-danger remove-category bi-text-right"
                data-ads-count="<?=$this->data['ads_counts'][$this->data['category']->getId()] ?? 0?>"
                data-categories-count="<?=count($this->data['categories'])?>"
                data-id="<?= $this->data['category']->getId() ?>"
        >Удалить</button>
    </div>

</form>

<?php if ($this->data['categories']) : ?>
    <br>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Подкатегория</th>
            <th>Объявления</th>
            <th>Синонимы</th>
        </tr>
        </thead>
        <tbody>

        <?php foreach ($this->data['categories'] as $category) : ?>
            <tr>
                <td>
                    <a href="/karman/categories/<?= $category->getId() ?>?cache=0" class="text-decoration-none">
                        <?= $category->getTitle() ?>
                        <?= $category->getEmoji() ?>
                    </a>
                </td>
                <td>
                    <a href="/karman/category-ads/<?=$category->getId()?>?cache=0">
                        <span class="badge bg-secondary "
                              data-bs-toggle="tooltip"
                              title="Количество объявлений"
                              data-bs-placement="right"
                        >
                            <?= $this->data['ads_counts'][$category->getId()] ?? 0 ?>
                        </span>
                    </a>
                </td>
                <td>
                    <?= $category->getGroupedSynonyms() ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

<?php else : ?>
    <div class="alert alert-secondary mt-3" role="alert">
        Подкатегорий нет
    </div>
<?php endif;?>

<div class="modal fade" id="removeCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Удаление категории</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <input type="hidden" id="categoryId" name="category_id">
                </form>
                <div class="alert alert-danger" role="alert">
                    A simple danger alert—check it out!
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Нет</button>
                <button type="button" class="btn btn-danger remove">Удалить</button>
            </div>
        </div>
    </div>
</div>

