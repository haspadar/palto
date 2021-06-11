	    </td>
	    <td></td>
	</tr>
	<tr>
	    <td class="tdfooter"></td>
	    <td class="tdfooter"><a href="http://www.danannoncer.com" class="footer">My Site</a> - description | <a href="maito:ofby@mail.ru" class="footer">Contacts: ofby@mail.ru</a> | <!--LiveInternet counter--><script type="text/javascript">
		document.write("<a href='//www.liveinternet.ru/click' "+
		"target=_blank><img src='//counter.yadro.ru/hit?t44.1;r"+
		escape(document.referrer)+((typeof(screen)=="undefined")?"":
		";s"+screen.width+"*"+screen.height+"*"+(screen.colorDepth?
		screen.colorDepth:screen.pixelDepth))+";u"+escape(document.URL)+
		";h"+escape(document.title.substring(0,150))+";"+Math.random()+
		"' alt='' title='LiveInternet' "+
		"border='0' width='5' height='5'><\/a>")
		</script><!--/LiveInternet--></td>
		<td class="tdfooter"></td>
	</tr>
</table>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
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