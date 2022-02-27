<?php
use Palto\Categories;
use Palto\Category;
use Palto\Debug;
?>
<?php /** @var $this League\Plates\Template\Template */?>

<?php $this->layout('layout'); ?>
    <form class="category">
        <div class="mb-3">
            <label class="form-label">Название</label>
            <input type="text" class="form-control" name="title" value="<?=$this->data['category']->getTitle()?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Url</label>
            <input type="text" class="form-control" name="url" value="<?=$this->data['category']->getUrl()?>">
        </div>
        <div class="mb-3">
            <button id="emoji-button" class="btn btn-outline-secondary" type="button"><?=$this->data['category']->getEmoji() ?: 'Emoji'?></button>
            <?php if ($this->data['category']->getEmoji()) :?>
                <a href="javascript:void(0);" class="small text-danger text-decoration-none remove-emoji" data-id="<?=$this->data['category']->getId()?>">Удалить</a>
            <?php endif;?>
        </div>

        <button type="submit" class="btn btn-primary" data-id="<?=$this->data['category']->getId()?>">Сохранить</button>
    </form>

<?php $categories = Categories::getLiveCategories($this->data['category']);?>
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
         * @var $this->data['category'] Category
         */
        ?>
        <?php foreach ($this->data['categories'] as $category) :?>
            <tr>
                <td>
                    <a href="/karman/categories/<?=$category->getId()?>" class="text-decoration-none">
                        <?=$category->getTitle()?>
                        <?=$category->getEmoji()?>
                    </a>
                </td>

            </tr>
        <?php endforeach;?>
        </tbody>
    </table>
<?php else :?>
    <div class="alert alert-secondary mt-3" role="alert">
        Подкатегорий нет
    </div>
<?php endif;
