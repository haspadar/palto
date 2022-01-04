<?php

/**
 * @var $this \Palto\Layout
 */
$this->partial('header.inc', [
    'title' => 'Registration',
    'description' => 'registration',
]);
?>

<h1>Registration</h1>
<form action="#">
    <input type="email" placeholder="email">
    <button>Зарегистрировать</button>
</form>

<?php $this->partial('footer.inc', []);