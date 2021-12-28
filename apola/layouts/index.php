<?php

/**
 * @var $this \Palto\Palto
 */
$this->partial('header.inc', [
    'title' => 'Prywatne darmowe ogłoszenia w Polsce z olx i innych forów dyskusyjnych',
    'description' => 'Agregator wszystkich darmowych ogłoszeń w Polsce',
]);
?>
    <h1>Ogłoszenia drobne w Polsce</h1>
    <div class="span-d regions">📍<a href="/swietokrzyskie"><strong> Świętokrzyskie</strong></div>
    <div class="span-d regions">📍<a href="/dolnoslaskie"><strong> Dolnośląskie</strong></a></div>
    <div class="span-d regions">📍<a href="/wielkopolskie"><strong> Wielkopolskie</strong></a></div>
    <div class="span-d regions">📍<a href="/krakow"><strong> Kraków</strong></a></div>
    <div class="span-d regions">📍<a href="/lodz"><strong> Łódź</strong></a></div>
    <div class="span-d regions">📍<a href="/poznan"><strong> Poznań</strong></a></div>
    <div class="span-d regions">📍<a href="/mazowieckie"><strong> Mazowieckie</strong></a></div>
    <div class="span-d regions">📍<a href="/slaskie"><strong> Śląskie</strong></a></div>
    <div class="span-d regions">📍<a href="/wroclaw"><strong> Wrocław</strong></a></div>
    <div class="span-d regions">📍<a href="/gdansk"><strong> Gdańsk</strong></a></div>
    <div class="span-d regions">📍<a href="/podlaskie"><strong> Podlaskie</strong></a></div>
    <div class="span-d regions">📍<a href="/opolskie"><strong> Opolskie</strong></a></div>
    <div class="span-d regions">📍<a href="/malopolskie"><strong> Małopolskie</strong></a></div>
    <div class="span-d regions">📍<a href="/pomorskie"><strong> Pomorskie</strong></a></div>
    <div class="span-d regions">📍<a href="/lubelskie"><strong> Lubelskie</strong></a></div>
    <div class="span-d regions">📍<a href="/zachodniopomorskie"><strong> Zachodniopomorskie</strong></a></div>
    <div class="span-d regions">📍<a href="/regions"><strong> Inne miasta</strong></a></div>
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-4251434934288140"
     crossorigin="anonymous"></script>
<!-- apola_adaptive -->
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-4251434934288140"
     data-ad-slot="5199190551"
     data-ad-format="auto"
     data-full-width-responsive="true"></ins>
<script>
     (adsbygoogle = window.adsbygoogle || []).push({});
</script>
    <br style="clear: both">
    <br style="clear: both">
    <h2>🗂 Kategorie</h2>
    <?php foreach ($this->getWithAdsCategories(0, 1) as $level1Category) :?>
        <div class="span-d">
            <p><a href="<?=$this->generateCategoryUrl($level1Category)?>">
                    <?php if ($level1Category['icon_url']) :?>
                        <img src="<?=$level1Category['icon_url']?>" title="<?=$level1Category['icon_text']?>" class="icm" />
                    <?php endif?>
                    <strong> <?=$level1Category['title']?></strong>
                </a>
            </p>
            <?php if ($level2Categories = $this->getWithAdsCategories($level1Category['id'])) :?>
                <ul>
                    <?php foreach ($level2Categories as  $level2Category) :?>
                        <li><a href="<?=$this->generateCategoryUrl($level2Category)?>"><?=$level2Category['title']?></a></li>
                    <?php endforeach;?>
                </ul>
            <?php endif;?>
        </div>
    <?php endforeach;?>
            <br style="clear: both">
            <br style="clear: both">
        <h2 style="color: #d91b39;">🔥 Gorące reklamy 🔥</h2>
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-4251434934288140"
     crossorigin="anonymous"></script>
<!-- apola_adaptive -->
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-4251434934288140"
     data-ad-slot="5199190551"
     data-ad-format="auto"
     data-full-width-responsive="true"></ins>
<script>
     (adsbygoogle = window.adsbygoogle || []).push({});
</script>
        <table class="serp">
            <tr>
    <td class="serp_img">
                    <a href="/leg-tarnowski/nieruchomoci/domy/wynajem-2/ad1339266"><img src="https://ireland.apollo.olxcdn.com:443/v1/files/ksxgaxfvqb9f3-PL/image;s=100x100" alt="Wynajem dom z ogrodem przy granicy Tarnowa"/></a>
            </td>
    <td>
        <div><a href="/leg-tarnowski/nieruchomoci/domy/wynajem-2/ad1339266" class="serp_link">Wynajem dom z ogrodem przy granicy Tarnowa</a></div>
        <div>Witam,
do wynajęcia dom od 8.11.2021. Zdjęcia robione 1.11.2021.
Dom posiada:
- 2 duże pokoje (nieumeblowane)
- łazienka (piecyk gazowy...</div>
        <div class="serp_bread">
                                            <a href="/leg-tarnowski/nieruchomoci" class="bread_link">Nieruchomości</a>
                                                <span class="sep">»</span>
                                <a href="/leg-tarnowski/nieruchomoci/domy" class="bread_link">Domy</a>
                                                <span class="sep">»</span>
                                <a href="/leg-tarnowski/nieruchomoci/domy/wynajem-2" class="bread_link">Wynajem</a>
                    </div>
                    <div class="serp_price">🏷1,500 zł </div>
        
        <div class="serp_region">📍<a class="bread_link" href="/leg-tarnowski">Łęg Tarnowski</a></div>
    </td>
</tr>
                    <tr>
    <td class="serp_img">
                    <a href="/rozyny/nieruchomoci/domy/wynajem-2/ad1339265"><img src="https://ireland.apollo.olxcdn.com:443/v1/files/rehdx3vkz8sd3-PL/image;s=100x100" alt="Do wynajęci dom 3 pokojowy, dwie łazienki, taras, balkon,parking"/></a>
            </td>
    <td>
        <div><a href="/rozyny/nieruchomoci/domy/wynajem-2/ad1339265" class="serp_link">Do wynajęci dom 3 pokojowy, dwie łazienki, taras, balkon,parking</a></div>
        <div>Wynajmę dom 3 pokojowy w Różynach.
Dwie sypialnie z dużym salonem, dwoma łazienkami i przestrzenną kuchnią w pelni wyposażoną wraz z zm...</div>
        <div class="serp_bread">
                                            <a href="/rozyny/nieruchomoci" class="bread_link">Nieruchomości</a>
                                                <span class="sep">»</span>
                                <a href="/rozyny/nieruchomoci/domy" class="bread_link">Domy</a>
                                                <span class="sep">»</span>
                                <a href="/rozyny/nieruchomoci/domy/wynajem-2" class="bread_link">Wynajem</a>
                    </div>
                    <div class="serp_price">🏷3,800 zł </div>
        
        <div class="serp_region">📍<a class="bread_link" href="/rozyny">Różyny</a></div>
    </td>
</tr>
                    <!--                                Counter-->
                    <tr>
    <td class="serp_img">
                    <a href="/praga-poludnie/nieruchomoci/domy/wynajem-2/ad1339264"><img src="https://ireland.apollo.olxcdn.com:443/v1/files/1dpb7fwzra523-PL/image;s=100x100" alt="Dom bliźniak do wynajęcia"/></a>
            </td>
    <td>
        <div><a href="/praga-poludnie/nieruchomoci/domy/wynajem-2/ad1339264" class="serp_link">Dom bliźniak do wynajęcia</a></div>
        <div>Wynajmę przestronny dom wolnostojący z widokiem na rezerwat przyrody -  Marysin Wawerski ul. Kościuszkowców 67 i 67A , budynek nowy po ...</div>
        <div class="serp_bread">
                                            <a href="/praga-poludnie/nieruchomoci" class="bread_link">Nieruchomości</a>
                                                <span class="sep">»</span>
                                <a href="/praga-poludnie/nieruchomoci/domy" class="bread_link">Domy</a>
                                                <span class="sep">»</span>
                                <a href="/praga-poludnie/nieruchomoci/domy/wynajem-2" class="bread_link">Wynajem</a>
                    </div>
                    <div class="serp_price">🏷28,000 zł </div>
        
        <div class="serp_region">📍<a class="bread_link" href="/praga-poludnie">Praga-Południe</a></div>
    </td>
</tr>
                    <tr>
    <td class="serp_img">
                    <a href="/bakalarzewo/nieruchomoci/domy/wynajem-2/ad1339263"><img src="https://ireland.apollo.olxcdn.com:443/v1/files/s7bov4llp9qa1-PL/image;s=100x100" alt="Dom na Suwalszczyźnie do wynajęcia. Ostatnie zimowe wolne terminy!"/></a>
            </td>
    <td>
        <div><a href="/bakalarzewo/nieruchomoci/domy/wynajem-2/ad1339263" class="serp_link">Dom na Suwalszczyźnie do wynajęcia. Ostatnie zimowe wolne terminy!</a></div>
        <div>Dom sytuowany w centrum miejscowości Bakałarzewo w dolinie rzeki Rozpudy, jeziora Sumowo (0,5 km) i jeziora Garbaś (1,5 km). Daje swoim...</div>
        <div class="serp_bread">
                                            <a href="/bakalarzewo/nieruchomoci" class="bread_link">Nieruchomości</a>
                                                <span class="sep">»</span>
                                <a href="/bakalarzewo/nieruchomoci/domy" class="bread_link">Domy</a>
                                                <span class="sep">»</span>
                                <a href="/bakalarzewo/nieruchomoci/domy/wynajem-2" class="bread_link">Wynajem</a>
                    </div>
                    <div class="serp_price">🏷61 zł </div>
        
        <div class="serp_region">📍<a class="bread_link" href="/bakalarzewo">Bakałarzewo</a></div>
    </td>
</tr>
                    <tr>
    <td class="serp_img">
                    <a href="/nowa-huta/nieruchomoci/domy/wynajem-2/ad1339262"><img src="https://ireland.apollo.olxcdn.com:443/v1/files/vpvl92t59p2w-PL/image;s=100x100" alt="Noclegi pokoje, Dom dla pracowników."/></a>
            </td>
    <td>
        <div><a href="/nowa-huta/nieruchomoci/domy/wynajem-2/ad1339262" class="serp_link">Noclegi pokoje, Dom dla pracowników.</a></div>
        <div>Do wynajęcia wpełni wyposarzony  dom  . Oś. Branice ul. Branicka 

Wynajem dla pracowników zapewniam pełne wyposażenie oraz potrzebną i...</div>
        <div class="serp_bread">
                                            <a href="/nowa-huta/nieruchomoci" class="bread_link">Nieruchomości</a>
                                                <span class="sep">»</span>
                                <a href="/nowa-huta/nieruchomoci/domy" class="bread_link">Domy</a>
                                                <span class="sep">»</span>
                                <a href="/nowa-huta/nieruchomoci/domy/wynajem-2" class="bread_link">Wynajem</a>
                    </div>
                    <div class="serp_price">🏷5,000 zł </div>
        
        <div class="serp_region">📍<a class="bread_link" href="/nowa-huta">Nowa Huta</a></div>
    </td>
</tr>
            </table>
<br style="clear: both">
        <br style="clear: both">
        <h2>🔔 Ostatnie reklamy</h2>
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-4251434934288140"
     crossorigin="anonymous"></script>
<!-- apola_adaptive -->
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-4251434934288140"
     data-ad-slot="5199190551"
     data-ad-format="auto"
     data-full-width-responsive="true"></ins>
<script>
     (adsbygoogle = window.adsbygoogle || []).push({});
</script>
        <table class="serp">
            <tr>
    <td class="serp_img">
                    <a href="/debiec/usugi-i-firmy/usugi/usugi-motoryzacyjne/ad1341259"><img src="https://ireland.apollo.olxcdn.com:443/v1/files/vgjgt80zwrlo1-PL/image;s=100x100" alt="SKUP AUT GOTÓWKA 24/7 odbiór od klienta WYCENA"/></a>
            </td>
    <td>
        <div><a href="/debiec/usugi-i-firmy/usugi/usugi-motoryzacyjne/ad1341259" class="serp_link">SKUP AUT GOTÓWKA 24/7 odbiór od klienta WYCENA</a></div>
        <div>SKUP AUT GOTÓWKA

ODBIÓR OD KLIENTA

WYCENA TELEFONICZNA

KONTAKT :
-Telefoniczny lub SMS
-Odpowiadamy na wiadomości OLX
-Whatsapp

Zac...</div>
        <div class="serp_bread">
                                            <a href="/debiec/usugi-i-firmy" class="bread_link">Usługi i Firmy</a>
                                                <span class="sep">»</span>
                                <a href="/debiec/usugi-i-firmy/usugi" class="bread_link">Usługi</a>
                                                <span class="sep">»</span>
                                <a href="/debiec/usugi-i-firmy/usugi/usugi-motoryzacyjne" class="bread_link">Usługi motoryzacyjne</a>
                    </div>
        
        <div class="serp_region">📍<a class="bread_link" href="/debiec">Dębiec</a></div>
    </td>
</tr>
                    <tr>
    <td class="serp_img">
                    <a href="/pabianice/usugi-i-firmy/usugi/usugi-motoryzacyjne/ad1341258"><img src="https://ireland.apollo.olxcdn.com:443/v1/files/c31f407gtztt-PL/image;s=100x100" alt="złomowanie skup aut pojazdów woj. łódzke 1zł/kg"/></a>
            </td>
    <td>
        <div><a href="/pabianice/usugi-i-firmy/usugi/usugi-motoryzacyjne/ad1341258" class="serp_link">złomowanie skup aut pojazdów woj. łódzke 1zł/kg</a></div>
        <div>skup aut w każdym stanie i wieku
uszkodzone
powypadkowe
bez opłat
bez dokumentów
zwracam za polisę oc
każdy pojazd odbieram auto lawetą...</div>
        <div class="serp_bread">
                                            <a href="/pabianice/usugi-i-firmy" class="bread_link">Usługi i Firmy</a>
                                                <span class="sep">»</span>
                                <a href="/pabianice/usugi-i-firmy/usugi" class="bread_link">Usługi</a>
                                                <span class="sep">»</span>
                                <a href="/pabianice/usugi-i-firmy/usugi/usugi-motoryzacyjne" class="bread_link">Usługi motoryzacyjne</a>
                    </div>
        
        <div class="serp_region">📍<a class="bread_link" href="/pabianice">Pabianice</a></div>
    </td>
</tr>
                    <!--                                Counter-->
                    <tr>
    <td class="serp_img">
                    <a href="/tarnow/usugi-i-firmy/usugi/usugi-motoryzacyjne/ad1341257"><img src="https://ireland.apollo.olxcdn.com:443/v1/files/dauurrqk2sv21-PL/image;s=100x100" alt="Skup Aut Samochodów Angielskich Anglików z Angli Dostawczych na czesci"/></a>
            </td>
    <td>
        <div><a href="/tarnow/usugi-i-firmy/usugi/usugi-motoryzacyjne/ad1341257" class="serp_link">Skup Aut Samochodów Angielskich Anglików z Angli Dostawczych na czesci</a></div>
        <div>Kupimy Auta ——Europejskie—-Angielskie ——Osobowe, Dostawcze, Uszkodzone. Dobre ceny skupu.
Jesteśmy zainteresowani Każdym Modelem i Mark...</div>
        <div class="serp_bread">
                                            <a href="/tarnow/usugi-i-firmy" class="bread_link">Usługi i Firmy</a>
                                                <span class="sep">»</span>
                                <a href="/tarnow/usugi-i-firmy/usugi" class="bread_link">Usługi</a>
                                                <span class="sep">»</span>
                                <a href="/tarnow/usugi-i-firmy/usugi/usugi-motoryzacyjne" class="bread_link">Usługi motoryzacyjne</a>
                    </div>
        
        <div class="serp_region">📍<a class="bread_link" href="/tarnow">Tarnów</a></div>
    </td>
</tr>
                    <tr>
    <td class="serp_img">
                    <a href="/olesnica/usugi-i-firmy/usugi/usugi-motoryzacyjne/ad1341256"><img src="https://ireland.apollo.olxcdn.com:443/v1/files/99tkjxbbm44p3-PL/image;s=100x100" alt="Spawanie Plastików, Polerowanie Lamp, Regeneracja Lamp"/></a>
            </td>
    <td>
        <div><a href="/olesnica/usugi-i-firmy/usugi/usugi-motoryzacyjne/ad1341256" class="serp_link">Spawanie Plastików, Polerowanie Lamp, Regeneracja Lamp</a></div>
        <div>LINK DO WYKONANYCH PRAC DOTYCZĄCYCH SPAWANIA PLASTIKU:
https://www.facebook.com/Spawanie-Plastik%C3%B3w-Ole%C5%9Bnica-i-okolice-1851940...</div>
        <div class="serp_bread">
                                            <a href="/olesnica/usugi-i-firmy" class="bread_link">Usługi i Firmy</a>
                                                <span class="sep">»</span>
                                <a href="/olesnica/usugi-i-firmy/usugi" class="bread_link">Usługi</a>
                                                <span class="sep">»</span>
                                <a href="/olesnica/usugi-i-firmy/usugi/usugi-motoryzacyjne" class="bread_link">Usługi motoryzacyjne</a>
                    </div>
        
        <div class="serp_region">📍<a class="bread_link" href="/olesnica">Oleśnica</a></div>
    </td>
</tr>
                    <tr>
    <td class="serp_img">
                    <a href="/ochota/usugi-i-firmy/usugi/usugi-motoryzacyjne/ad1341255"><img src="https://ireland.apollo.olxcdn.com:443/v1/files/8f3b8pdh9q6l2-PL/image;s=100x100" alt="Konserwacja podwozia zabezpieczenie antykorozyjne GWARANCJA"/></a>
            </td>
    <td>
        <div><a href="/ochota/usugi-i-firmy/usugi/usugi-motoryzacyjne/ad1341255" class="serp_link">Konserwacja podwozia zabezpieczenie antykorozyjne GWARANCJA</a></div>
        <div>1. Przygotowanie samochodu oraz jego zabezpieczenie
2. Mycie wstępne, pod wysokim ciśnieneim
3. Odkręcenie nadkoli oraz wszelkich osł...</div>
        <div class="serp_bread">
                                            <a href="/ochota/usugi-i-firmy" class="bread_link">Usługi i Firmy</a>
                                                <span class="sep">»</span>
                                <a href="/ochota/usugi-i-firmy/usugi" class="bread_link">Usługi</a>
                                                <span class="sep">»</span>
                                <a href="/ochota/usugi-i-firmy/usugi/usugi-motoryzacyjne" class="bread_link">Usługi motoryzacyjne</a>
                    </div>
        
        <div class="serp_region">📍<a class="bread_link" href="/ochota">Ochota</a></div>
    </td>
</tr>
            </table>

<?php $this->partial('footer.inc', []);