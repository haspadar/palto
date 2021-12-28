	    </td>
	    <td></td>
	</tr>
	<tr>
	    <td class="tdfooter"></td>
	    <td class="tdfooter"><a href="https://www.apola.net" class="footer">Ogłoszenia drobne w Polsce</a> - Tablica ogłoszeń lokalnych | <a href="https://www.apola.net/regions" class="footer">Miasta</a> | <a href="https://www.apola.net/categories" class="footer">Kategorie</a> | <a href="maito:adspoter@gmail.com" class="footer">Łączność: adspoter@gmail.com</a> | <!--LiveInternet counter--><a href="https://www.liveinternet.ru/click"
target="_blank"><img id="licnt42CF" width="6" height="6" style="border:0" 
title="LiveInternet"
src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAEALAAAAAABAAEAAAIBTAA7"
alt=""/></a><script>(function(d,s){d.getElementById("licnt42CF").src=
"https://counter.yadro.ru/hit?t44.1;r"+escape(d.referrer)+
((typeof(s)=="undefined")?"":";s"+s.width+"*"+s.height+"*"+
(s.colorDepth?s.colorDepth:s.pixelDepth))+";u"+escape(d.URL)+
";h"+escape(d.title.substring(0,150))+";"+Math.random()})
(document,screen)</script><!--/LiveInternet--></td>
		<td class="tdfooter"></td>
	</tr>
</table>
<div id="cookie_notification">
        <div>Ta strona wykorzystuje pliki cookie do personalizacji treści i reklam, udostępniania funkcji mediów społecznościowych i analizy naszego ruchu. Udostępniamy również informacje o Twoim korzystaniu z naszej witryny naszym partnerom społecznościowym, reklamowym i analitycznym, którzy mogą łączyć je z innymi informacjami, które im przekazałeś lub które zebrali podczas korzystania z ich usług.</div>
        <button class="button cookie_accept">ZAAKCEPTOWAĆ</button>
</div>
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
<script src="/css/cookies.js"></script>
</body>
</html>