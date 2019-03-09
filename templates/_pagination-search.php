<?php if ($pages_count > 1): ?>

    <ul class="pagination-list">

        <?php if ($cur_page == 1): ?>
            <li class="pagination-item pagination-item-prev"><a href="#">Назад</a></li>
        <?php else: ?>
            <li class="pagination-item pagination-item-prev"><a
                        href="search.php?search=<?= $search; ?>&page=<?= ($cur_page - 1); ?>">Назад</a></li>
        <?php endif; ?>

        <?php foreach ($pages as $page): ?>
            <li class="pagination-item <?php if ($page == $cur_page): ?>pagination-item-active<?php endif; ?>">
                <a href="search.php?search=<?= $search; ?>&page=<?= $page; ?>"> <?= $page; ?> </a></li>
        <?php endforeach; ?>

        <?php if ($cur_page == $pages_count): ?>
            <li class="pagination-item pagination-item-next"><a href="#">Вперед</a></li>
        <?php else: ?>
            <li class="pagination-item pagination-item-next"><a
                        href="search.php?search=<?= $search; ?>&page=<?= ($cur_page + 1); ?>">Вперед</a></li>
        <?php endif; ?>

    </ul>

<?php endif; ?>

