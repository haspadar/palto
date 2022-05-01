<?php /** @var $this League\Plates\Template\Template */?>

<?php $this->layout('layout');?>

<?php if ($this->data['categories'] ?? []) :?>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Название</th>
            <th>Синонимы</th>
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
                    <a href="<?=$this->data['category_url']?>/<?=$category->getId()?>" class="text-decoration-none">
                        <?=$category->getTitle()?>
                        <?=$category->getEmoji()?>

                        <span class="badge bg-secondary" data-bs-toggle="tooltip" title="Количество объявлений" data-bs-placement="right">
                            <?=$this->data['ads_counts'][$category->getId()]?>
                        </span>
                    </a>
                </td>
                <td>
                    <?=implode(', ', $this->data['synonyms'][$category->getId()] ?? [])?>
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