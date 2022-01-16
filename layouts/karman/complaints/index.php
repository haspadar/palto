<?php

/**
 * @var $this \Palto\Layout\Karman
 */
$this->partial('header.inc', [
    'title' => 'Жалобы',
]);
?>

<table class="table table-striped">
    <thead>
    <tr>
        <th>Сообщение</th>
        <th>Дата</th>
    </tr>
    </thead>
    <tbody>

    <?php $actualComplaints = \Palto\Complaints::getActualComplaints()?>
    <?php foreach ($actualComplaints as $actualComplaint) :?>
        <tr>
            <td>
                <a href="/karman/complaints?id=<?=$actualComplaint['id']?>" class="text-decoration-none">
                    <?php $message = $actualComplaint['message']?>
                    <?=\Palto\Filter::shortText($message, 50)?>
                </a>
            </td>
            <td>
                <?php $createTime = new DateTime($actualComplaint['create_time'])?>
                <?=$createTime->format('d')?> <?=\Palto\Time::russianMonth($createTime->format('m'))?>
                <small class="text-muted"><?=$createTime->format('H:i')?></small>
            </td>

        </tr>
    <?php endforeach;?>
    </tbody>
</table>


<button type="button" class="btn btn-warning ignore-all-complaints" data-ids="<?=implode(',', array_column($actualComplaints, 'id'))?>">Игнорировать все жалобы</button>
<button type="button" class="btn btn-danger remove-all-ads" data-ids="<?=implode(',', array_column($actualComplaints, 'id'))?>">Удалить все анкеты</button>

<?php $this->partial('footer.inc');