<?php /** @var $this League\Plates\Template\Template */?>

<?php $this->layout('layout');?>

<?php
/**
 * @var \Palto\Page $page
 */
$page = $this->data['page'];
?>
<form class="page">
    <div class="mb-3">
        <label class="form-label">Включена</label>
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch" name="is_enabled" id="page[<?=$page->getId()?>]" <?php if ($page->isEnabled()) :?>checked<?php endif;?> value="1">
            <label class="form-check-label" for="page[<?=$page->getId()?>]"></label>
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label">Название</label>
        <input type="text" class="form-control" disabled value="<?=$page->getName()?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Описание</label>
        <input type="text" class="form-control" name="comment" value="<?=$page->getComment()?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Шаблон</label>
        <select class="form-select" aria-label="Template" name="template_id">
            <?php
            /**
             * @var \Palto\Template $template
             */
            foreach ($this->data['templates'] as $template) :?>
                <option value="<?=$template->getId()?>" <?php if ($page->getTemplateId() == $template->getId()) :?>selected<?php endif;?>>
                    <?=$template->getName()?>
                </option>
            <?php endforeach;?>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">URL</label>
        <input type="text" class="form-control" name="url" value="<?=$page->getUrl()?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Функция</label>
        <select class="form-select" aria-label="Template" name="function">
            <?php foreach ($this->data['functions'] as $function) :?>
                <option value="<?=$function?>" <?php if ($page->getFunction() == $function) :?>selected<?php endif;?>>
                    <?=$function?>
                </option>
            <?php endforeach;?>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Приоритет</label>
        <input type="text" class="form-control" name="priority" value="<?=$page->getPriority()?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Тэг "title"</label>
        <input type="text" class="form-control" name="title" value="<?=htmlentities($page->getTitle())?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Тэг "description"</label>
        <textarea class="form-control" rows="3" name="description"><?=htmlentities($page->getDescription())?></textarea>
    </div>
    <div class="mb-3">
        <label class="form-label">Тэг "h1"</label>
        <input type="text" class="form-control" name="h1" value="<?=htmlentities($page->getH1())?>">
    </div>


    <button type="submit" class="btn btn-primary" data-id="<?= $page->getId() ?>">Сохранить</button>
</form>
