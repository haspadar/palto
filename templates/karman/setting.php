<?php /** @var $this League\Plates\Template\Template */?>

<?php $this->layout('layout');?>

<form class="setting">

    <dl class="row">
        <dt class="col-sm-3">Название</dt>
        <dd class="col-sm-9">
            <?=$this->data['setting']['name']?>
        </dd>

        <dt class="col-sm-3">Группа</dt>
        <dd class="col-sm-9">
            <span class="badge badge-secondary">
                <?=$this->data['setting']['group']?>
            </span>
        </dd>


        <dt class="col-sm-3">Описание</dt>
        <dd class="col-sm-9">
            <?=$this->data['setting']['comment']?>
        </dd>

        <dt class="col-sm-3">Значение</dt>
        <dd class="col-sm-9">
            <?php if ($this->data['setting']['type'] == 'theme'):?>
                <select class="form-select-sm" aria-label="Theme" name="value">
                    <?php foreach ($this->data['themes'] as $theme) :?>
                        <option value="<?=$theme?>" <?php if ($theme == $this->data['setting']['value']) :?>selected<?php endif;?>>
                            <?=$theme?>
                        </option>
                    <?php endforeach;?>
                </select>
            <?php elseif (in_array($this->data['setting']['type'], ['input', 'bool'])):?>
                <input type="text" name="value" value="<?=$this->data['setting']['value']?>">
            <?php else: ?>
                <textarea name="value" cols="30" rows="5"><?=$this->data['setting']['value']?></textarea>
            <?php endif;?>
        </dd>
    </dl>

    <button type="submit" class="btn btn-primary" data-id="<?= $this->data['setting']['id'] ?>">Сохранить</button>
</form>
