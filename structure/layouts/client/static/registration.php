<?php

/**
 * @var $this \Palto\Layout\Client\Client\Client\Client\Client\Client\Client\Client\Client\Client\Client\Client\Client\Client\Client\Client\Client
 */
$this->partial('header.inc', [
    'title' => $this->translate('registration_title'),
    'description' => $this->translate('registration_description')
]);
?>

<h1><?=$this->translate('registration_h1')?></h1>
<form action="#">
    <input type="email" placeholder="email">
    <button><?=$this->translate('Зарегистрировать')?></button>
</form>

<?php $this->partial('footer.inc', []);