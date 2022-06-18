<?php /** @var $this League\Plates\Template\Template */?>

<?php $this->layout('layout');?>

<form class="setting">

    <dl class="row">
        <dt class="col-sm-3">Название</dt>
        <dd class="col-sm-9">
            <?=$this->data['setting']['name']?>
        </dd>

        <dt class="col-sm-3">Группа</dt>
        <dd class="col-sm-9 text-muted"><?=$this->data['setting']['group']?></dd>


        <dt class="col-sm-3">Описание</dt>
        <dd class="col-sm-9">
            <?=$this->data['setting']['comment']?>
        </dd>

        <dt class="col-sm-3">Значение</dt>
        <dd class="col-sm-9">
            <?php if ($this->data['setting']['type'] == 'bool') :?>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="setting[<?=$this->data['setting']['id']?>]" <?php if ($this->data['setting']['value'] == 1) :?>checked<?php endif;?>>
                    <label class="form-check-label" for="setting[<?=$this->data['setting']['id']?>]">Да</label>
                </div>
            <?php elseif ($this->data['setting']['type'] == 'theme'):?>
                <select name="value">
                    <?php foreach ($this->data['themes'] as $theme) :?>
                        <option value="<?=$theme?>">
                            <?=$theme?>
                        </option>
                    <?php endforeach;?>
                </select>
            <?php elseif ($this->data['setting']['type'] == 'input'):?>
                <input type="text" name="value" value="<?=$this->data['setting']['value']?>">
            <?php else: ?>
                <textarea name="value" cols="30" rows="5"><?=$this->data['setting']['value']?></textarea>
            <?php endif;?>
        </dd>
    </dl>

    <button type="submit" class="btn btn-primary" data-id="<?= $this->data['setting']['id'] ?>">Сохранить</button>
</form>
