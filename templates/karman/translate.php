<?php /** @var $this League\Plates\Template\Template */?>

<?php $this->layout('layout');?>

<form class="translate">

    <dl class="row">
        <dt class="col-sm-3">Название</dt>
        <dd class="col-sm-9">
            <?=$this->data['translate']->getName()?>
        </dd>


        <dt class="col-sm-3">Значение</dt>
        <dd class="col-sm-9">
            <textarea name="value" cols="30" rows="5"><?=$this->data['translate']->getValue()?></textarea>
        </dd>
    </dl>

    <button type="submit" class="btn btn-primary" data-id="<?= $this->data['translate']->getId() ?>">Сохранить</button>
</form>
