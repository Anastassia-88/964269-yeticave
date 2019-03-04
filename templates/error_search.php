        <nav class="nav">
            <ul class="nav__list container">
                <?php foreach ($categories as $category): ?>
                <li class="nav__item">
                    <a href="all-lots.html"><?= htmlspecialchars($category['name']); ?></a>
                </li>
                <?php endforeach; ?>
            </ul>
        </nav>
        <section class="lot-item container">
            <h2>Результаты поиска по запросу «<span><?= $search; ?></span>»</h2>
            <p>Ничего не найдено по вашему запросу</p>
        </section>
