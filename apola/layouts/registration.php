<?php

/**
 * @var $this \Palto\Palto
 */
$this->partial('header.inc', [
    'title' => 'Rejestracja',
    'description' => 'Rejestracja',
]);
?>

    <h2>Zaloguj sie</h2>
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
    <form action="#">
        <input type="email" placeholder="email" class="reg"><br/>
        <input type="password" placeholder="password" class="reg"><br/>
        <button class="button">Zaloguj sie</button>
        <div><a href="#">Zapomniałeś hasła?</a></div>
    </form>
    <br/>
    <p>or</p>
    <h2>Utwórz konto</h2>
    <form action="#">
        <input type="email" placeholder="email" class="reg"><br/>
        <button class="button">Utwórz konto</button>
    </form>
    <div style="height: 300px;"></div>

<?php $this->partial('footer.inc', []);