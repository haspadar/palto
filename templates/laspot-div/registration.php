<?php /** @var $this League\Plates\Template\Template */?>
<?php $this->layout('layout');?>

<h2><?=$this->translate('Авторизация')?></h2>
<?=\Palto\Counters::get('google') ?: \Palto\Counters::receive('adx')?>

<div class="registration">
    <form action="#" class="registration__log">
        <p class="registration__title">Log in</p>
        <input type="text" placeholder="email">
        <input type="text" placeholder="password">
        <button type="submit"><?=$this->translate('Войти')?></button>
        <a href="#"><?=$this->translate('Забыли пароль?')?></a>
    </form>
    <span><?=$this->translate('или')?></span>
    <form action="#" class="registration__create">
        <p class="registration__title"><?=$this->translate('Регистрация')?></p>
        <input type="text" placeholder="email">
        <button type="submit"><?=$this->translate('Зарегистрировать')?></button>
    </form>
</div>
