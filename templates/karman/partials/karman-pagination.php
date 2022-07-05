<nav aria-label="Pagination">
    <ul class="pagination">
        <?php
        if ($this->data['page'] > 1) :?>
            <li class="page-item">
                <a class="page-link" href="<?=sprintf($this->data['url'], $this->data['page'] - 1)?>" aria-label="Предыдущая">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        <?php endif;?>

        <?php for ($page = max(1, $this->data['page'] - 2); $page <= min($this->data['page'] + 2, $this->data['pages_count']); $page++) :?>
            <li class="page-item <?php if ($this->data['page'] == $page) :?>active<?php endif;?>">
                <a class="page-link" href="<?=sprintf($this->data['url'], $page)?>">
                    <?=$page?>
                </a>
            </li>
        <?php endfor;?>

        <?php if ($this->data['page'] < $this->data['pages_count']) :?>
            <li class="page-item">
                <a class="page-link" href="<?=sprintf($this->data['url'], $this->data['page'] + 1)?>" aria-label="Следующая">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        <?php endif;?>

        <span class="text-muted p-2">из <?=$this->data['pages_count']?></span>
    </ul>
</nav>