<?php

/**
 * @var $this \Palto\Layout\Karman
 */

use Palto\Categories;
use Palto\Category;
use Palto\Debug;

$category = Categories::getById($this->getId());
$parents = $category->getParents();
$parentsUrls = array_map(fn(Category $parent) => [
    'title' => $parent->getTitle(),
    'url' => '/karman/categories?id=' . $parent->getId()
], $parents);
//Debug::dump($parentsUrls);
$this->partial('header.inc', [
    'title' => 'Категория "' . $category->getTitle() . '"',
    'breadcrumbUrls' => array_merge([[
        'title' => 'Категории',
        'url' => '/karman/categories?cache=0'
    ]], $parentsUrls, [[
        'title' => 'Категория "' . $category->getTitle() . '"',
    ]])
]);
?>
<form class="category">
    <div class="mb-3">
        <label class="form-label">Название</label>
        <input type="text" class="form-control" name="title" value="<?=$category->getTitle()?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Url</label>
        <input type="text" class="form-control" name="url" value="<?=$category->getUrl()?>">
    </div>
    <div class="mb-3">
        <button id="emoji-button" class="btn btn-outline-secondary" type="button"><?=$category->getEmoji() ?: 'Emoji'?></button>
        <?php if ($category->getEmoji()) :?>
            <a href="javascript:void(0);" class="small text-danger text-decoration-none remove-emoji">Удалить</a>
        <?php endif;?>
    </div>

    <button type="submit" class="btn btn-primary">Сохранить</button>
</form>

<?php $categories = Categories::getLiveCategories($category);?>
<?php if ($categories) :?>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Подкатегории</th>
        </tr>
        </thead>
        <tbody>

        <?php
        /**
         * @var $category Category
         */
        ?>
        <?php foreach ($categories as $category) :?>
            <tr>
                <td>
                    <a href="/karman/categories?id=<?=$category->getId()?>" class="text-decoration-none">
                        <?=$category->getTitle()?>
                        <?=$category->getEmoji()?>
                    </a>
                </td>

            </tr>
        <?php endforeach;?>
        </tbody>
    </table>
<?php else :?>
    <div class="alert alert-light" role="alert">
        Подкатегорий нет
    </div>
<?php endif;?>

<?php $this->partial('footer.inc');