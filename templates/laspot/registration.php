<?php /** @var $this League\Plates\Template\Template */?>
<?php $this->layout('layout');?>

<h2><?=$this->translate('Авторизация')?></h2>
<?=\Palto\Counters::get('google')?>
<form action="#">
    <input type="email" placeholder="email" class="reg"><br/>
    <input type="password" placeholder="password" class="reg"><br/>
    <button class="button"><?=$this->translate('Войти')?></button>
    <div><a href="#"><?=$this->translate('Забыли пароль?')?></a></div>
</form>
<br/>
<p><?=$this->translate('или')?></p>
<h2><?=$this->translate('Регистрация')?></h2>
<form action="#">
    <input type="email" placeholder="email" class="reg"><br/>
    <button class="button"><?=$this->translate('Зарегистрировать')?></button>
</form>
<div style="height: 300px;"></div>