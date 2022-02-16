<?php

/**
 * @var $this \Palto\Layout\Client
 */
$this->partial('header.inc', [
    'title' => $this->translate('registration_title'),
    'description' => $this->translate('registration_description')
]);
?>
<h2><?=$this->translate('Авторизация')?></h2>
<?=\Palto\Counters::get('google') ?: \Palto\Counters::get('adx')?>
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

<?php $this->partial('footer.inc', []);