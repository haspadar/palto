<?php /** @var $this League\Plates\Template\Template */?>

<?php $this->layout('layout');?>

<?php if ($this->data['complaints']) :?>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Сообщение</th>
            <th>Объявление</th>
            <th>Дата</th>
            <th>

            </th>
        </tr>
        </thead>
        <tbody>

        <?php foreach ($this->data['complaints'] as $actualComplaint) :?>
            <tr>
                <td>
                    <a href="/karman/complaints/<?=$actualComplaint['id']?>" class="text-decoration-none">
                        <?php $message = $actualComplaint['message']?>
                        <?=\Palto\Filter::shortText($message, 50)?>
                    </a>
                </td>
                <td>
                    <?php $ad = Ads::getById($actualComplaint['ad_id']);?>
                    <a href="<?=$ad->generateUrl()?>" target="_blank" class="text-decoration-none">
                        <?=\Palto\Filter::shortText($ad->getTitle(), 100)?>
                    </a>
                </td>
                <td>
                    <?php $createTime = new DateTime($actualComplaint['create_time'])?>
                    <?=$createTime->format('d')?> <?=\Palto\Russian::month($createTime->format('m'))?>
                    <small class="text-muted"><?=$createTime->format('H:i')?></small>
                </td>
                <td>
                    <a href="javascript:void(0);" class="text-danger remove-ad" data-id="<?=$actualComplaint['id']?>">Удалить</a><br>
                    <a href="javascript:void(0);" class="text-muted ignore-complaint" data-id="<?=$actualComplaint['id']?>">Игнорировать</a>
                </td>
            </tr>
        <?php endforeach;?>
        </tbody>
    </table>
    <button type="button" class="btn btn-warning ignore-all-complaints" data-ids="<?=implode(',', array_column($this->data['complaints'], 'id'))?>">Игнорировать все жалобы</button>
    <button type="button" class="btn btn-danger remove-all-ads" data-ids="<?=implode(',', array_column($this->data['complaints'], 'id'))?>">Удалить все анкеты</button>
<?php else :?>
    <div class="alert alert-light" role="alert">
        Жалоб нет
    </div>
<?php endif;