<?php
use Palto\Categories;
use Palto\Category;
use Palto\Debug;
?>
<?php /** @var $this League\Plates\Template\Template */?>

<?php $this->layout('layout'); ?>

<?php $categories = Categories::getLiveCategories($this->data['category']);?>

<ol class="list-group list-group-numbered">
    <?php foreach ($this->data['ads'] as $ad) :?>
        <li class="list-group-item">
            <a href="<?=$ad->getUrl()?>" target="_blank">
                <?=$ad->getTitle();?>
            </a>
            <button class="btn btn-secondary btn-sm float-end move-not-found" type="button" data-bs-toggle="modal" data-bs-target="#moveNotFoundModal">Перенести</button>
        </li>

    <?php endforeach;?>

    <?php $count = \Palto\Ads::getCategoriesAdsCount([$this->data['category']->getId()]);?>
    <a href="#" class="list-group-item list-group-item-action disabled" tabindex="-1" aria-disabled="true">Всего <?=$count?> объявлений</a>
</ol>

<div class="modal fade" id="moveNotFoundModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <input type="hidden" id="parentCategoryId" value="<?=$this->data['category']->getParentId()?>">
    <input type="hidden" id="categoryId" value="<?=$this->data['category']->getId()?>">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Перенос объявления</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="categoryId" class="form-label">Категория</label>
                    <select class="form-select" aria-label="Default select example" id="categoryId">
                        <option selected>Dogs</option>
                        <option value="1">Cats</option>
                        <option value="2">Two</option>
                        <option value="3">Three</option>
                        <option value="0">Новая</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="newCategory" class="form-label">Новая категория</label>
                    <input type="text" class="form-control" id="newCategory" name="new_category">
                </div>

                <div class="mb-3">
                    <label for="subCategoryId" class="form-label">Подкатегория</label>
                    <select class="form-select" aria-label="Default select example" id="subCategoryId">
                        <option selected>Mastiff</option>
                        <option value="1">Shnautzer</option>
                        <option value="2">Two</option>
                        <option value="3">Three</option>
                        <option value="0">Новая</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="newSubcategory" class="form-label">Новая подкатегория</label>
                    <input type="text" class="form-control" id="newSubcategory" name="new_subcategory">
                </div>

                <div class="mb-3">
                    <label for="synonym" class="form-label">Синонимы (через запятую)</label>
                    <input type="text" class="form-control" id="synonym">
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                <button type="button" class="btn btn-primary">Сохранить</button>
            </div>
        </div>
    </div>
</div>
