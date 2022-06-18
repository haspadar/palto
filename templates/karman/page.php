<?php /** @var $this League\Plates\Template\Template */?>

<?php $this->layout('layout');?>

<form class="page">
    <div class="mb-3">
        <label class="form-label">Включена</label>
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch" name="is_enabled" id="page[<?=$this->data['page']['id']?>]" <?php if ($this->data['page']['is_enabled'] == 1) :?>checked<?php endif;?> value="1">
            <label class="form-check-label" for="page[<?=$this->data['page']['id']?>]"></label>
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label">Название</label>
        <input type="text" class="form-control" disabled value="<?=$this->data['page']['name']?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Описание</label>
        <input type="text" class="form-control" name="comment" value="<?=$this->data['page']['comment']?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Шаблон</label>
        <select class="form-select" aria-label="Template" name="template_id">
            <?php foreach ($this->data['templates'] as $template) :?>
                <option value="<?=$template['id']?>" <?php if ($this->data['page']['template_id'] == $template['id']) :?>selected<?php endif;?>>
                    <?=$template['name']?>
                </option>
            <?php endforeach;?>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">URL</label>
        <input type="text" class="form-control" name="url" value="<?=$this->data['page']['url']?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Функция</label>
        <select class="form-select" aria-label="Template" name="function">
            <?php foreach ($this->data['functions'] as $function) :?>
                <option value="<?=$function?>" <?php if ($this->data['page']['function'] == $function) :?>selected<?php endif;?>>
                    <?=$function?>
                </option>
            <?php endforeach;?>
        </select>
    </div>
    <button type="submit" class="btn btn-primary" data-id="<?= $this->data['page']['id'] ?>">Сохранить</button>
</form>
