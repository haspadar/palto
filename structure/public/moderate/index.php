<?php

use Palto\Moderation;

require_once '../../vendor/autoload.php';
\Palto\Auth::check();
?>
<html>
<head>
    <title>Модерация анкет</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" type="text/css" href="/css/style.css?v=<?=time()?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="Refresh" content="600" />
</head>
<body>
    <table width="100%" cellspacing="0" cellpadding="0" style="font:'Arial Black'; font-size:12px">
        <thead>
            <tr>
                <td height="30" bgcolor="#CCCCCC" style="padding-left:10px">Почта</td>
                <td bgcolor="#CCCCCC">Сообщение</td>
                <td bgcolor="#CCCCCC">Страница</td>
                <td bgcolor="#CCCCCC">Url</td>
                <td bgcolor="#CCCCCC">Аватар</td>
                <td bgcolor="#CCCCCC">IP</td>
                <td bgcolor="#CCCCCC"></td>
            </tr>
        </thead>
        <tbody>
            <?php $actualComplaints = Moderation::getActualComplaints();
            foreach ($actualComplaints as $actualComplaint) :?>
                <tr>
                    <td height="30" style="border-bottom:#CCC solid 1px; padding-left:10px">
                        <?=$actualComplaint['email']?>
                    </td>
                    <td style="border-bottom:#CCC solid 1px">
                        <?=$actualComplaint['message']?>
                    </td>
                    <td style="border-bottom:#CCC solid 1px">
                        <?=(new DateTime($actualComplaint['create_time']))->format('d.m.Y H:i:s')?>
                    </td>
                    <td style="border-bottom:#CCC solid 1px">
                        <a href="<?=$actualComplaint['domain']?><?=$actualComplaint['page']?>" target="_blank"><?=$actualComplaint['domain']?><?=$actualComplaint['page']?></a>
                    </td>
                    <td style="border-bottom:#CCC solid 1px">
                        <?php if ($actualComplaint['avatar'] ?? '') :?>
                            <a href="<?=$actualComplaint['avatar']?>" target="_blank">IMG</a>
                        <?php endif;?>
                    </td>
                    <td style="border-bottom:#CCC solid 1px">
                        <?=$actualComplaint['ip']?>
                    </td>
                    <td style="border-bottom:#CCC solid 1px">
                        <a href="javascript:void(0);" data-id="<?=$actualComplaint['id']?>" class="ignore-profile" data-url="<?=\Palto\Config::getDomainUrl()?>/moderate/ignore.php">
                            Игнорировать жалобу
                        </a>

                        <a href="javascript:void(0);" data-id="<?=$actualComplaint['id']?>" class="remove-profile" data-url="<?=\Palto\Config::getDomainUrl()?>/moderate/remove.php">
                            Удалить анкету
                        </a>
                    </td>
                </tr>
            <?php endforeach;?>
        </tbody>
    </table>
    <?php if ($actualComplaints) :?>
        <a href="javascript:void(0);"
           data-id="<?=implode(',', array_column($actualComplaints, 'id'))?>"
           class="ignore-profile"
           data-url="<?=\Palto\Config::getDomainUrl()?>/moderate/ignore.php"
        >
            <br/><br/><div style="padding-left:10px">Игнорировать все жалобы</div>
        </a>

        <a href="javascript:void(0);"
           data-id="<?=implode(',', array_column($actualComplaints, 'id'))?>"
           class="remove-profile"
        >
            <br/><br/><div style="padding-left:10px">Удалить все анкеты</div>
        </a>
    <?php endif;?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script type="text/javascript" src="/js/moderation.js?v=<?=time()?>"></script>
</body>
</html>