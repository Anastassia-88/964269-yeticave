<nav class="nav">
    <ul class="nav__list container">
        <?php foreach ($categories as $category): ?>
            <li class="nav__item">
                <a href="/all-lots.php?id=<?= $category['id']; ?>"><?= $category['name']; ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
<section class="rates container">
    <h2>Мои ставки</h2>
    <table class="rates__list">

        <?php foreach ($rates as $rate): ?>
            <?php $classname = ($rate['winner_id'] === $user_id) ? "rates__item--win" : ""; ?>
            <tr class="rates__item <?= $classname; ?>">
                <td class="rates__info">
                    <div class="rates__img">
                        <img src="<?= htmlspecialchars($rate['image']); ?>" width="54" height="40"
                             alt="<?= htmlspecialchars($rate['lot_name']); ?>">
                    </div>
                    <h3 class="rates__title"><a href="/lot.php?id=<?= $rate['lot_id']; ?>">
                            <?= htmlspecialchars($rate['lot_name']); ?></a></h3>
                    <?php if ($rate['winner_id'] === $user_id): ?>
                        <p><?= htmlspecialchars($rate['message']); ?></p>
                    <?php endif; ?>
                </td>
                <td class="rates__category"><?= $rate['category_name']; ?></td>
                <td class="rates__timer">
                    <?php $classname = ($rate['winner_id'] === $user_id) ? "timer--win" : "timer--finishing";
                    $value = ($rate['winner_id'] === $user_id) ? "Ставка выиграла" : time_left_short($rate['dt_end']); ?>
                    <div class="timer <?= $classname; ?>"><?= $value; ?></div>
                </td>
                <td class="rates__price">
                    <?= price_format($rate['amount']); ?>
                </td>
                <td class="rates__time">
                    <?= time_ago($rate['dt_add']); ?>
                </td>
            </tr>
        <?php endforeach; ?>

    </table>
</section>