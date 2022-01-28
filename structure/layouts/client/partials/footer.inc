<?php
/**
 * @var $this \Palto\Layout\Client
 */

use Palto\Config;

?>
</td>
    <td class="tdright"></td>
	</tr>
	<tr>
	    <td class="tdfooter"></td>
	    <td class="tdfooter"><?=$this->translate('footer_text')?> <?=\Palto\Counters::get('liveinternet')?></td>
		<td class="tdfooter"></td>
	</tr>
</table>

<div id="cookie_notification">
    <div><?=$this->translate('cookie_text')?></div>
    <button class="button cookie_accept"><?=$this->translate('СОГЛАСЕН')?></button>
</div>

<script src="/js/jquery.min.js"></script>
<script src="/js/cookies.js"></script>
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