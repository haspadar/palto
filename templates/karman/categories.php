<?php /** @var $this League\Plates\Template\Template */?>

<?php $this->layout('layout');?>

<?php if ($this->data['categories'] ?? []) :?>
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
        Категорий нет
    </div>
<?php endif;