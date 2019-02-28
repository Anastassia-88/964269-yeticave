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
        <form class="form container <?= $classname_form; ?>" action="sign-up.php" method="post" enctype="multipart/form-data">
            <h2>Регистрация нового аккаунта</h2>

            <?php $classname = isset($errors['email']) ? "form__item--invalid" : "";
            // Возвращаем введенные значения в форму
            $value = isset($sign_up_form['email']) ? $sign_up_form['email'] : ""; ?>
            <div class="form__item <?= $classname; ?>">
                <label for="email">E-mail*</label>
                <input id="email" type="text" name="email" placeholder="Введите e-mail" value="<?= $value; ?>" required>
                <?php if (isset($errors['email'])): ?>
                <span class="form__error"><?= $errors['email']; ?></span>
                <?php endif; ?>
            </div>

            <?php $classname = isset($errors['password']) ? "form__item--invalid" : "";
            // Возвращаем введенные значения в форму
            $value = isset($sign_up_form['password']) ? $sign_up_form['password'] : ""; ?>
            <div class="form__item <?= $classname; ?>">
                <label for="password">Пароль*</label>
                <input id="password" type="text" name="password" placeholder="Введите пароль" value="<?= $value; ?>" required>
                <?php if (isset($errors['password'])): ?>
                <span class="form__error"><?= $errors['password']; ?></span>
                <?php endif; ?>
            </div>

            <?php $classname = isset($errors['name']) ? "form__item--invalid" : "";
            // Возвращаем введенные значения в форму
            $value = isset($sign_up_form['name']) ? $sign_up_form['name'] : ""; ?>
            <div class="form__item <?= $classname; ?>">
                <label for="name">Имя*</label>
                <input id="name" type="text" name="name" placeholder="Введите имя" value="<?= $value; ?>" required>
                <?php if (isset($errors['name'])): ?>
                <span class="form__error"><?= $errors['name']; ?></span>
                <?php endif; ?>
            </div>

            <?php $classname = isset($errors['message']) ? "form__item--invalid" : "";
            // Возвращаем введенные значения в форму
            $value = isset($sign_up_form['message']) ? $sign_up_form['message'] : ""; ?>
            <div class="form__item <?= $classname; ?>">
                <label for="message">Контактные данные*</label>
                <textarea id="message" name="message" placeholder="Напишите как с вами связаться" required><?= $value; ?></textarea>
                <?php if (isset($errors['message'])): ?>
                <span class="form__error"><?= $errors['message']; ?></span>
                <?php endif; ?>
            </div>

            <?php $classname = isset($errors['image']) ? "form__item--invalid" : "";
            // Возвращаем введенные значения в форму
            $value = isset($sign_up_form['image']) ? $sign_up_form['image'] : ""; ?>
            <div class="form__item form__item--file form__item--last <?= $classname; ?>">
                <label>Аватар</label>
                <div class="preview">
                    <button class="preview__remove" type="button">x</button>
                    <div class="preview__img">
                        <img src="img/avatar.jpg" width="113" height="113" alt="Ваш аватар">
                    </div>
                </div>
                <div class="form__input-file">
                    <input class="visually-hidden" type="file" id="photo2" name="image" value="<?= $value; ?>">
                    <label for="photo2">
                        <span>+ Добавить</span>
                    </label>
                </div>
                <?php if (isset($errors['image'])): ?>
                    <span class="form__error"><?= $errors['image']; ?></span>
                <?php endif; ?>
            </div>

            <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>

            <button type="submit" class="button">Зарегистрироваться</button>
            <a class="text-link" href="login.php">Уже есть аккаунт</a>
        </form>
