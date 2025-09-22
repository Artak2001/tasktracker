<?php
// public/views/task_create.php
// $activeNav = 'task_create'; // (установи перед require header)
?>

<main class="tt-main">
  <div class="tt-page tt-page--task-create">
    <section class="panel" aria-labelledby="create-task-title">
      <header class="panel__head">
        <div>
          <h2 id="create-task-title" class="panel__title">Создать задачу</h2>
          <p class="panel__subtitle">Заполните поля и сохраните</p>
        </div>
      </header>

      <form method="post" action="<?= BASE_URL ?>/jwt_hook/task_create.php" class="form">
        <div class="form-row">
          <label for="title">Заголовок</label>
          <input id="title" type="text" name="title" required>
        </div>

        <div class="form-row">
          <label for="description">Описание</label>
          <textarea id="description" name="description" rows="5"></textarea>
        </div>

        <input type="hidden" name="status" value="new">

        <div class="form-row-btn">
          <button class="btn" type="submit">Создать</button>
        </div>
      </form>
    </section>
  </div>
</main>
