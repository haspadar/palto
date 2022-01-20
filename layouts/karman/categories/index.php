<?php

/**
 * @var $this \Palto\Layout\Karman
 */
$this->partial('header.inc', [
    'title' => 'Категории',
]);
?>

<?php $categories = \Palto\Categories::getWithAdsCategories();?>
<?php if ($categories) :?>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Название</th>
        </tr>
        </thead>
        <tbody>

        <?php
        /**
         * @var $category \Palto\Category
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
        Категорий нет
    </div>
<?php endif;?>

<?php $this->partial('footer.inc');