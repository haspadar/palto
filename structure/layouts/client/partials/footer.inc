<?php
/**
 * @var $this \Palto\Layout\Client
 */

use Palto\Config;

?>
</td>
	    <td></td>
	</tr>
	<tr>
	    <td class="tdfooter"></td>
	    <td class="tdfooter"><a href="<?=Config::getDomainUrl()?>" class="footer"><?=$this->translate('Частные бесплатные объявления в %s')?></a> - <?=$this->translate('Агрегатор всех местных досок объявлений')?> | <a href="maito:<?=Config::get('DEFAULT_REGION_TITLE')?>" class="footer"><?=$this->translate('Контакты')?>: <?=Config::get('DEFAULT_REGION_TITLE')?></a> | <?=\Palto\Counters::get('liveinternet')?></td>
		<td class="tdfooter"></td>
	</tr>
</table>

<div id="cookie_notification">
    <div><?=$this->translate('Текст про куки')?></div>
    <button class="button cookie_accept"><?=$this->translate('СОГЛАСЕН')?></button>
</div>

<script src="/js/jquery.min.js"></script>
<script src="/js/slider.js?v=<?=hash_file('md5', $this->getPublicDirectory() . '/js/slider.js')?>"></script>
<?php if ($this->getPartialVariable('js')) :?>
    <?php foreach ($this->getPartialVariable('js') as $js) :?>
        <?php foreach ($js as $jsAttribute => $jsValue) :?>
            <script <?=$jsAttribute?>="<?=$jsValue?>"></script>
        <?php endforeach;?>
    <?php endforeach;?>
<?php endif;?>
        
<script src="/js/moderation.js?v=<?=hash_file('md5', $this->getPublicDirectory() . '/js/moderation.js')?>"></script>
<script src="/js/script.js?v=<?=hash_file('md5', $this->getPublicDirectory() . '/js/script.js')?>"></script>
</body>
</html>