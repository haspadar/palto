<?php use Palto\Panel;
use Palto\Plural;

if (Panel::isPanelEnabled()) :?>
    <div class="karman-panel">
        <div class="karman-item">
            <div class="label">Сайт</div>
            <input class="apple-switch site-checkbox" type="checkbox" <?php if (Panel::isSiteEnabled()) :?>checked<?php endif;?>>
        </div>

        <div class="karman-item">
            <div class="label">Кэш</div>
            <input class="apple-switch cache-checkbox"  type="checkbox" <?php if (Panel::isCacheEnabled()) :?>checked<?php endif;?>>
        </div>

        <div class="karman-item d-sm-none">
            <a href="/karman/complaints?cache=0">
                <?php $complaintsCount = Panel::getComplaintsCount()?>
                <?php if ($complaintsCount) :?>
                    <span class="badge badge-warning">
                    <?=$complaintsCount?> <?= Plural::get($complaintsCount, 'жалоба', 'жалобы', 'жалоб')?>
                </span>
                <?php else :?>
                    <span class="badge badge-secondary">
                    жалоб нет
                </span>
                <?php endif;?>
            </a>
        </div>

        <div class="karman-item d-sm-none">
            <a href="/karman/status?cache=0">
                <?php $percent = Panel::getBusySpace()?>
                <span class="badge <?php if ($percent > 90) :?>badge-danger<?php elseif ($percent > 75) :?>badge-warning<?php else :?>badge-secondary<?php endif;?>">
                место: <?=$percent?>%
            </span>
            </a>
        </div>

        <div class="karman-hide">
            <a href="<?= Panel::getHideUrl()?>" >Спрятать</a>
        </div>
    </div>
<?php endif;?>