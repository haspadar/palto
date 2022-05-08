<?php
use Palto\Categories;
use Palto\Category;
use Palto\Debug;
?>
<?php /** @var $this League\Plates\Template\Template */?>

<?php $this->layout('layout'); ?>

<ol class="list-group list-group-numbered">
    <?php foreach ($this->data['ads'] as $ad) :?>
        <li class="list-group-item">
            <a href="<?=$ad->getUrl()?>" target="_blank">
                <?=$ad->getTitle();?>
            </a>
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

    <a href="#" class="list-group-item list-group-item-action disabled" tabindex="-1" aria-disabled="true">Всего <?=$this->data['ads_count']?> объявлений</a>
</ol>

<div class="modal fade" id="moveUndefinedModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Перенос объявления</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <input type="hidden" id="adId" name="ad_id">
                    <input type="hidden" id="adCategoryId">
                    <input type="hidden" id="adCategoryParentId">

                    <div class="mb-3">
                        <label for="categoryLevel1" class="form-label">Категория</label>
                        <a href="javascript:void(0);" class="add-category-level-1 small">Добавить</a>
                        <select class="form-select" id="categoryLevel1" name="category_level_1">
                            <?php foreach ($this->data['categories_level_1'] ?? [] as $categoryLevel1) :?>
                                <option value="<?=$categoryLevel1['id']?>" <?php if (in_array($categoryLevel1['id'], [$this->data['category']->getId(), $this->data['category']->getParentId()])) :?>selected<?php endif;?>>
                                    <?=$categoryLevel1['title']?>
                                </option>
                            <?php endforeach;?>
                        </select>
                    </div>

                    <div class="mb-3 d-none">
                        <label for="newCategoryLevel1" class="form-label">Новая категория</label>
                        <input type="text" class="form-control" id="newCategoryLevel1" name="new_category_level_1">
                    </div>

                    <div class="mb-3">
                        <label for="categoryLevel2" class="form-label">Подкатегория</label>
                        <a href="javascript:void(0);" class="add-category-level-2 small">Добавить</a>
                        <select class="form-select" id="categoryLevel2" name="category_level_2">
                            <?php if ($this->data['category']->getLevel() == 2) :?>
                                <?php foreach ($this->data['categories_level_2'] ?? [] as $categoryLevel2) :?>
                                    <?php if ($categoryLevel2['parent_id'] == $this->data['category']->getParentId()) :?>
                                        <option value="<?=$categoryLevel2['id']?>" <?php if ($categoryLevel2['id'] == $this->data['category']->getId()) :?>selected<?php endif;?>>
                                            <?=$categoryLevel2['title']?>
                                        </option>
                                    <?php endif;?>
                                <?php endforeach;?>
                            <?php endif;?>
                        </select>
                    </div>

                    <div class="mb-3 d-none">
                        <label for="newCategoryLevel2" class="form-label">Новая подкатегория</label>
                        <input type="text" class="form-control" id="newCategoryLevel2" name="new_category_level_2">
                    </div>

                    <div class="mb-3">
                        <label for="synonyms" class="form-label">Синонимы (через запятую)</label>
                        <input type="text" class="form-control" id="synonyms" name="synonyms">
                    </div>
                </form>
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Warning:">
                        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                    </svg>
                    <div></div>
                </div>
            </div>
            <div class="modal-footer">

                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                <button type="button" class="btn btn-primary save" >Сохранить</button>
            </div>
        </div>
    </div>
</div>
