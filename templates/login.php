<nav class="nav">
    <ul class="nav__list container">
        <?php foreach($categories as $category): ?>
            <li class="nav__item">
                <a href="all-lots.html"><?= $category['name']; ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
<?php $classname_form = isset($errors) ? "form--invalid" : ""; ?>
<form class="form container <?= $classname_form; ?>" action="/login.php" method="post">
    <h2>Вход</h2>

    <?php $classname = isset($errors['email']) ? "form__item--invalid" : "";
    // Возвращаем введенные значения в форму
    $value = isset($login_form['email']) ? $login_form['email'] : ""; ?>
    <div class="form__item <?= $classname; ?>">
        <label for="email">E-mail*</label>
        <input id="email" type="text" name="email" placeholder="Введите e-mail" value="<?= $value; ?>" required>
        <?php if (isset($errors['email'])): ?>
            <span class="form__error"><?= $errors['email']; ?></span>
        <?php endif; ?>
    </div>

    <?php $classname = isset($errors['password']) ? "form__item--invalid" : ""; ?>
    <div class="form__item form__item--last <?= $classname; ?>">
        <label for="password">Пароль*</label>
        <input id="password" type="password" name="password" placeholder="Введите пароль" required>
        <?php if (isset($errors['password'])): ?>
            <span class="form__error"><?= $errors['password']; ?></span>
        <?php endif; ?>
    </div>

    <button type="submit" class="button">Войти</button>
</form>

