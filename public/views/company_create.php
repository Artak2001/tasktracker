<?php 
// require __DIR__ . '/../../src/controller/company_create.php';
?>  

  <div class="card">
    <h2>Создать компанию</h2>
    <form method="post" action="<?= BASE_URL ?>/jwt_hook/company_create.php">
      <div class="form-row"><label>Название</label>
        <input type="text" name="company_name" required>
      </div>
      <button class="btn" type="submit">Создать</button>
    </form>
  </div>

