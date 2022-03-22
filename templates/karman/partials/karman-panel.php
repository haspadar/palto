<?php if (\Palto\Auth::isLogged()) :?>
    <div class="karman-panel">
        <div class="karman-item">
            <div class="label">Сайт</div>
            <input class="apple-switch site-checkbox" type="checkbox" <?php if (\Palto\Status::isSiteEnabled()) :?>checked<?php endif;?>>
        </div>

        <div class="karman-item">
            <div class="label">Кэш</div>
            <input class="apple-switch cache-checkbox"  type="checkbox" <?php if (\Palto\Status::isCacheEnabled()) :?>checked<?php endif;?>>
        </div>

        <div class="karman-item link">
            <a href="/karman/complaints?cache=0">
                <?php $complaintsCount = \Palto\Complaints::getActualComplaintsCount()?>
                <?php if ($complaintsCount) :?>
                    <span class="badge badge-warning">
                        <?=$complaintsCount?> <?=\Palto\Plural::get($complaintsCount, 'жалоба', 'жалобы', 'жалоб')?>
                    </span>
                <?php else :?>
                    <span class="badge badge-secondary">
                        жалоб нет
                    </span>
                <?php endif;?>
            </a>
        </div>

        <div class="karman-item link">
            <a href="/karman/status?cache=0">
                <?php $percent = \Palto\Status::getDirectoryUsePercent('/')?>
                <span class="badge <?php if ($percent > 90) :?>badge-danger<?php elseif ($percent > 75) :?>badge-warning<?php else :?>badge-secondary<?php endif;?>">
                    место: <?=$percent?>%
                </span>
            </a>
        </div>

    </div>
<?php endif;?>