<?php if ($pages_count > 1): ?>
        <ul class="pagination-list">
            <li class="pagination-item pagination-item-prev"><a>Назад</a></li>
            <?php foreach ($pages as $page): ?>







                <li class="pagination__item <?php if ($page == $cur_page): ?>pagination__item--active<?php endif; ?>">
                    <a href="/?page=<?=$page;?>"><?=$page;?></a>
                </li>

            <?php endforeach; ?>
            <li class="pagination-item pagination-item-next"><a href="#">Вперед</a></li>
        </ul>
<?php endif; ?>















    <li class="pagination-item pagination-item-active"><a>1</a></li>
    <li class="pagination-item"><a href="#">2</a></li>
    <li class="pagination-item"><a href="#">3</a></li>
    <li class="pagination-item"><a href="#">4</a></li>

