<?php
// public/views/company_create.php
// $activeNav = 'company_create'; // (установи перед require header)
?>

<main class="tt-main">
  <div class="tt-page tt-page--company-create">
    <section class="panel" aria-labelledby="create-company-title">
      <header class="panel__head">
        <div>
          <h2 id="create-company-title" class="panel__title">Создать компанию</h2>
          <p class="panel__subtitle">Укажите название и сохраните</p>
        </div>
      </header>

      <div class="card">
        <form method="post" action="<?= BASE_URL ?>/jwt_hook/company_create.php" class="form">
          <div class="form-row">
            <label for="company_name">Название</label>
            <input id="company_name" type="text" name="company_name" required>
          </div>

          <div class="form-row-btn">
            <button class="btn" type="submit">Создать</button>
          </div>
        </form>
      </div>
    </section>
  </div>
</main>
