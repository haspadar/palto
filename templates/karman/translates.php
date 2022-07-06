<?php /** @var $this League\Plates\Template\Template */?>

<?php $this->layout('layout');?>


<table class="table">
    <thead>
    <tr>
        <th>Перевод</th>
        <th>Значение</th>
    </tr>
    </thead>
    <tbody>

    <?php
    /**
     * @var \Palto\Translate $translate
     */
    foreach ($this->data['translates'] as $translate) :?>
        <tr>
                <td>
                    <?php $url = '/karman/translates/' . $translate->getId() . '?cache=0'; ?>
                    <a href="<?=$url?>">
                        <?=$translate->getName()?>
                    </a>
                </td>
                <td>
                    <?=htmlentities($translate->getValue())?>
                </td>
            </tr>

    <?php endforeach;?>
    </tbody>
</table>