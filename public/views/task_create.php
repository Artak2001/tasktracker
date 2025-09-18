<?php
//  require __DIR__ . '/../../src/controller/task_create.php';
    
?>

<form method="post" action="<?= BASE_URL ?>/jwt_hook/task_create.php">
  <div class="form-row">
    <label>Заголовок</label>
    <input type="text" name="title" required>
  </div>

  <div class="form-row">
    <label>Описание</label>
    <textarea name="description"></textarea>
  </div>

  <input type="hidden" name="status" value="new">

  <button class="btn" type="submit">Создать</button>
</form>
