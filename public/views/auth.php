<?php
// public/views/auth.php
// $activeNav = 'auth'; // можно не подсвечивать в меню, оставляю как опцию
?>

<main class="tt-main">
  <div class="tt-page tt-page--auth">

    <div class="reg-wrap">
      <div class="login-part sign-in-area" id="login" aria-labelledby="auth-login-title">
        <h2 class="auth-title" id="auth-login-title">Вход</h2>
        <form method="post" action="<?= BASE_URL ?>/../src/controller/login.php" class="auth-form auth-form-login">
          <div class="form-row auth-form-row">
            <label class="auth-label" for="login-email">Email</label>
            <input id="login-email" type="email" name="email" required class="auth-input" />
          </div>
          <div class="form-row auth-form-row">
            <label class="auth-label" for="login-password">Пароль</label>
            <input id="login-password" type="password" name="password" required class="auth-input" />
          </div>
          <div class="form-row-btn inline auth-form-actions">
            <button class="btn auth-btn" type="submit">Войти</button>
          </div>
        </form>
      </div>

      <div class="register-part sign-up-area display-none" id="register" aria-labelledby="auth-register-title">
        <h2 class="auth-title" id="auth-register-title">Регистрация</h2>
        <form method="post" action="<?= BASE_URL ?>/../src/controller/reg.php" class="auth-form auth-form-register">
          <div class="form-row split auth-form-row">
            <div class="half auth-half">
              <label class="auth-label" for="reg-name">Имя</label>
              <input id="reg-name" type="text" name="name" placeholder="Иван" required class="auth-input" />
            </div>
            <div class="half auth-half">
              <label class="auth-label" for="reg-email">Email</label>
              <input id="reg-email" type="email" name="email" placeholder="you@example.com" required class="auth-input" />
            </div>
          </div>
          <div class="form-row split auth-form-row">
            <div class="half auth-half">
              <label class="auth-label" for="reg-password">Пароль</label>
              <input id="reg-password" type="password" name="password" placeholder="минимум 8 символов" minlength="8" required class="auth-input" />
            </div>
            <div class="half auth-half">
              <label class="auth-label" for="reg-password2">Повтор пароля</label>
              <input id="reg-password2" type="password" name="password2" placeholder="ещё раз пароль" minlength="8" required class="auth-input" />
            </div>
          </div>
          <div class="form-row-btn auth-form-actions">
            <button class="btn auth-btn" type="submit">Создать аккаунт</button>
          </div>
        </form>
      </div>
    </div>

  </div>
</main>
