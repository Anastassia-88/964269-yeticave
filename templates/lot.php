<nav class="nav">
    <ul class="nav__list container">
        <?php foreach ($categories as $category): ?>
            <li class="nav__item">
                <a href="/all-lots.php?id=<?= $category['id']; ?>"><?= $category['name']; ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
<section class="lot-item container">
    <h2><?= htmlspecialchars($lot['name']); ?></h2>
    <div class="lot-item__content">
        <div class="lot-item__left">
            <div class="lot-item__image">
                <img src="<?= htmlspecialchars($lot['image']); ?>" width="730" height="548"
                     alt="<?= htmlspecialchars($lot['name']); ?>">
            </div>
            <p class="lot-item__category">Категория: <span><?= $lot['category']; ?></span></p>
            <p class="lot-item__description"><?= htmlspecialchars($lot['description']); ?></p>
        </div>
        <div class="lot-item__right">
            <div class="lot-item__state">
                <div class="lot-item__timer timer">
                    <?= time_left_short($lot['dt_end']); ?>
                </div>
                <div class="lot-item__cost-state">
                    <div class="lot-item__rate">
                        <span class="lot-item__amount">Текущая цена</span>
                        <span class="lot-item__cost"><?= price_format($current_price); ?></span>
                    </div>
                    <div class="lot-item__min-cost">
                        Мин. ставка <span><?= price_format($min_bet); ?></span>
                    </div>
                </div>
                <?php if ($show_bet_form): ?>
                    <form class="lot-item__form" action="/lot.php?id=<?= $lot_id; ?>" method="post">
                        <?php $classname = isset($error) ? "form__item--invalid" : ""; ?>
                        <p class="lot-item__form-item form__item <?= $classname; ?>
                                        <label for=" cost">Ваша ставка</label>
                        <input id="cost" type="text" name="amount" placeholder="<?= $min_bet; ?>">
                        <?php if (isset($error)): ?>
                            <span class="form__error"><?= $error; ?></span>
                        <?php endif; ?>
                        </p>
                        <button type="submit" class="button">Сделать ставку</button>
                    </form>
                <?php endif; ?>
            </div>

            <div class="history">
                <h3>История ставок (<span><?= $bets_count; ?></span>)</h3>
                <table class="history__list">
                    <?php foreach ($bets as $bet): ?>
                        <tr class="history__item">
                            <td class="history__name"><?= $bet['name']; ?></td>
                            <td class="history__price"><?= price_format($bet['amount']); ?></td>
                            <td class="history__time"><?= time_ago($bet['dt_add']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
</section>
