	    </td>
	    <td></td>
	</tr>
	<tr>
	    <td class="tdfooter"></td>
	    <td class="tdfooter"><a href="http://www.laspot.org" class="footer">Classified Ads in Los Angeles</a> - Aggregator of all local classifieds boards | <a href="maito:adspoter@gmail.com" class="footer">Contacts: adspoter@gmail.com</a> | <!--LiveInternet counter--><a href="https://www.liveinternet.ru/click"
target="_blank"><img id="licnt4AF3" width="5" height="5" style="border:0" 
title="LiveInternet"
src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAEALAAAAAABAAEAAAIBTAA7"
alt=""/></a><script>(function(d,s){d.getElementById("licnt4AF3").src=
"https://counter.yadro.ru/hit?t44.1;r"+escape(d.referrer)+
((typeof(s)=="undefined")?"":";s"+s.width+"*"+s.height+"*"+
(s.colorDepth?s.colorDepth:s.pixelDepth))+";u"+escape(d.URL)+
";h"+escape(d.title.substring(0,150))+";"+Math.random()})
(document,screen)</script><!--/LiveInternet--></td>
		<td class="tdfooter"></td>
	</tr>
</table>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="/js/slider.js?t=<?=time()?>"></script>
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