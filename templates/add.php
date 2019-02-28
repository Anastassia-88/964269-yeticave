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
    <form class="form form--add-lot container <?= $classname_form; ?>" action="add.php" method="post"
          enctype="multipart/form-data">
      <h2>Добавление лота</h2>
      <div class="form__container-two">

        <?php $classname = isset($errors['name']) ? "form__item--invalid" : "";
        // Возвращаем введенные значения в форму
        $value = isset($lot['name']) ? $lot['name'] : ""; ?>
        <div class="form__item <?= $classname; ?>">
          <label for="lot-name">Наименование</label>
          <input id="lot-name" type="text" name="lot[name]" placeholder="Введите наименование лота"
                 value="<?= $value; ?>" required>
          <?php if (isset($errors['name'])): ?>
          <span class="form__error"><strong><?=  $errors['name']; ?></span>
          <?php endif; ?>
        </div>

        <?php $classname = isset($errors['category']) ? "form__item--invalid" : ""; ?>
        <div class="form__item <?= $classname; ?>">
          <label for="category">Категория</label>
          <select id="category" name="lot[category]" required>
            <option value = "select">Выберите категорию</option>
            <?php foreach($categories as $category): ?>
              <option value="<?= $category['id']; ?>" <? if($lot['category'] == $category['id'])
              {print ('selected');} ?>><?= $category['name']; ?></option>
            <?php endforeach; ?>
          </select>
            <?php if (isset($errors['category'])): ?>
            <span class="form__error"><strong><?=  $errors['category']; ?></span>
            <?php endif; ?>
        </div>
        </div>

        <?php $classname = isset($errors['description']) ? "form__item--invalid" : "";
        $value = isset($lot['description']) ? $lot['description'] : ""; ?>
          <div class="form__item form__item--wide <?= $classname; ?>">
              <label for="message">Описание</label>
              <textarea id="message" name="lot[description]" placeholder="Напишите описание лота" required><?= $value; ?></textarea>
              <?php if (isset($errors['description'])): ?>
              <span class="form__error"><strong><?=  $errors['description']; ?></span>
              <?php endif; ?>
          </div>

          <?php $classname = isset($errors['image']) ? "form__item--invalid" : "";
          $value = isset($lot['image']) ? $lot['image'] : ""; ?>
          <div class="form__item form__item--file <?= $classname; ?>">
              <label>Изображение</label>
              <div class="preview">
                  <button class="preview__remove" type="button">x</button>
                  <div class="preview__img">
                      <img src="img/avatar.jpg" width="113" height="113" alt="Изображение лота">
                  </div>
              </div>
              <div class="form__input-file">
                  <input class="visually-hidden" type="file" id="photo2" name="image" value="<?= $value; ?>" required>
                  <label for="photo2">
                      <span>+ Добавить</span>
                  </label>
              </div>
              <?php if (isset($errors['image'])): ?>
                  <span class="form__error"><?= $errors['image']; ?></span>
              <?php endif; ?>
          </div>

          <div class="form__container-three">

          <?php $classname = isset($errors['start_price']) ? "form__item--invalid" : "";
          $value = isset($lot['start_price']) ? $lot['start_price'] : ""; ?>
          <div class="form__item form__item--small <?= $classname; ?>">
              <label for="lot-rate">Начальная цена</label>
              <input id="lot-rate" type="text" name="lot[start_price]" placeholder="0" value="<?= $value; ?>" required>
                  <?php if (isset($errors['start_price'])): ?>
                      <span class="form__error"><?=  $errors['start_price']; ?></span>
                  <?php endif; ?>
          </div>

        <?php $classname = isset($errors['bet_step']) ? "form__item--invalid" : "";
        $value = isset($lot['bet_step']) ? $lot['bet_step'] : ""; ?>
        <div class="form__item form__item--small <?= $classname; ?>">
          <label for="lot-step">Шаг ставки</label>
          <input id="lot-step" type="text" name="lot[bet_step]" placeholder="0" value="<?= $value; ?>" required>
            <?php if (isset($errors['bet_step'])): ?>
          <span class="form__error"><?=  $errors['bet_step']; ?></span>
            <?php endif; ?>
        </div>

        <?php $classname = isset($errors['dt_end']) ? "form__item--invalid" : "";
        $value = isset($lot['dt_end']) ? $lot['dt_end'] : ""; ?>
        <div class="form__item <?= $classname; ?>">
          <label for="lot-date">Дата окончания торгов</label>
          <input class="form__input-date" id="lot-date" type="text" name="lot[dt_end]" value="<?= $value; ?>" required>
            <?php if (isset($errors['dt_end'])): ?>
          <span class="form__error"><?=  $errors['dt_end']; ?></span>
            <?php endif; ?>
        </div>
      </div>

      <?php if (isset($errors)): ?>
          <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
      <?php endif; ?>
        <button type="submit" class="button">Добавить лот</button>
    </form>
