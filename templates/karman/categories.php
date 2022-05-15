<?php /** @var $this League\Plates\Template\Template */?>

<?php $this->layout('layout');?>

<?php if ($this->data['categories'] ?? []) :?>
    <?php $this->insert('partials/karman-categories', ['categories' => $this->data['categories']])?>

    <br>
    <?php if ($this->data['undefined_categories']) :?>
        <h3>Undefined</h3>
        <?php $this->insert('partials/karman-categories', ['categories' => $this->data['undefined_categories']])?>

    <?php endif;?>
<?php else :?>
    <div class="alert alert-secondary mt-3" role="alert">
        Категорий нет
    </div>
<?php endif;