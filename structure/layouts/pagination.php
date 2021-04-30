<?php if ($pagesCount > 1) : ?>
    <div>
        <table class="pager">
            <tr>
                <td>
                    <?php if ($pageNumber > 1) : ?>
                        <a href="<?= Danannoncer::$previousPageUrl ?>">« Forrige side</a>
                    <?php endif; ?>
                    <?php if ($pageNumber < $pagesCount) : ?>
                        <a href="<?= Danannoncer::$nextPageUrl ?>"> Næste side »</a>
                    <?php endif; ?>
                    <div class="c"></div>
                </td>
            </tr>
        </table>
    </div>
<?php endif; ?>