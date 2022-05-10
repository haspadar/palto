<?php /** @var $this League\Plates\Template\Template */?>

<?php $this->layout('layout');?>

<?php if ($this->data['categories'] ?? []) :?>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Название</th>
            <th>Объявления</th>
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
                    <a href="/karman/categories/<?=$category->getId()?>?cache=0" class="text-decoration-none">
                        <?=$category->getTitle()?>
                        <?=$category->getEmoji()?>
                    </a>
                </td>
                <td>
                    <a href="/karman/ads/<?=$category->getId()?>?cache=0">
                        <span class="badge bg-primary" data-bs-toggle="tooltip" title="Количество объявлений" data-bs-placement="right">
                            <?=$this->data['ads_counts'][$category->getId()] ?? 0?>
                        </span>
                    </a>
                </td>
                <td>
                    <?=$category->getGroupedSynonyms()?>
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