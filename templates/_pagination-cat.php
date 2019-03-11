<?php if ($pages_count > 1): ?>

    <ul class="pagination-list">

        <?php if ($cur_page === 1): ?>
            <li class="pagination-item pagination-item-prev"><a href="#">Назад</a></li>
        <?php else: ?>
            <li class="pagination-item pagination-item-prev"><a
                        href="all-lots.php?id=<?= $category_id; ?>&page=<?= ($cur_page - 1); ?>">Назад</a></li>
        <?php endif; ?>

        <?php foreach ($pages as $page): ?>
            <li class="pagination-item <?php if ($page === $cur_page): ?>pagination-item-active<?php endif; ?>">
                <a href="all-lots.php?id=<?= $category_id; ?>&page=<?= $page; ?>"> <?= $page; ?> </a></li>
        <?php endforeach; ?>

        <?php if ($cur_page === $pages_count): ?>
            <li class="pagination-item pagination-item-next"><a href="#">Вперед</a></li>
        <?php else: ?>
            <li class="pagination-item pagination-item-next"><a
                        href="all-lots.php?id=<?= $category_id; ?>&page=<?= ($cur_page + 1); ?>">Вперед</a></li>
        <?php endif; ?>

    </ul>

<?php endif; ?>

