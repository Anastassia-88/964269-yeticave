<section class="promo">
    <h2 class="promo__title">Нужен стафф для катки?</h2>
    <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и
        горнолыжное снаряжение.
    </p>
<!--    Выводим категории-->
    <ul class="promo__list">
        <?php foreach ($categories as $category): ?>
            <li class="promo__item promo__item--boards">
                <a class="promo__link" href="/all-lots.php?id=<?= $category['id'];?>"><?= $category['name']; ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
<section class="lots">
    <div class="lots__header">
        <h2>Открытые лоты</h2>
    </div>
<!--    Выводим 9 лотов-->
    <ul class="lots__list">
        <?php foreach ($lots as $lot): ?>
            <li class="lots__item lot">
                <div class="lot__image">
                    <img src="<?= htmlspecialchars($lot['image']); ?>" width="350" height="260"
                         alt="<?= htmlspecialchars($lot['name']); ?>">
                </div>
                <div class="lot__info">
                    <span class="lot__category"><?= $lot['category']; ?></span>
                    <h3 class="lot__title"><a class="text-link"
                                              href="/lot.php?id=<?= $lot['id']; ?>"><?= htmlspecialchars($lot['name']); ?></a>
                    </h3>
                    <div class="lot__state">
                        <div class="lot__rate">
                            <span class="lot__amount">Стартовая цена</span>
                            <span class="lot__cost"><?= price_format($lot['start_price']); ?></span>
                        </div>
                        <div class="lot__timer timer">
                            <?= $time_left = (strtotime("now") < strtotime($lot['dt_end'])) ? time_left ($lot['dt_end']) : 'Торги окончены'; ?>
                        </div>
                    </div>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
