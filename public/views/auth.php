
<div class="reg-wrap">
    <div class="login-part sign-in-area">
      <h2 class="auth-title">Вход</h2>
      <form method="post" action="<?= BASE_URL ?>/../src/controller/login.php" class="auth-form auth-form-login">
        <!-- оставь свои классы -->
        <div class="form-row auth-form-row">
          <label class="auth-label">Email</label>
          <input type="email" name="email"  required class="auth-input" />
        </div>
        <div class="form-row auth-form-row">
          <label class="auth-label">Пароль</label>
          <input type="password" name="password"  required class="auth-input" />
        </div>
        <div class="form-row-btn inline auth-form-actions">
          <button class="btn auth-btn" type="submit">Войти</button>
          <!-- <a class="register-btn auth-register-link" href="<?= BASE_URL ?>/?page=home#register">Регистрация</a> -->
        </div>
      </form>
    </div>
  <div class="register-part sign-up-area display-none">
    <h2 id="register" class="auth-title">Регистрация</h2>
    <form method="post" action="<?= BASE_URL ?>/../src/controller/reg.php" class="auth-form auth-form-register">
      <!-- здесь — твои поля и классы из макета -->
      <div class="form-row split auth-form-row">
        <div class="half auth-half">
          <label class="auth-label">Имя</label>
          <input type="text" name="name" placeholder="Иван" required class="auth-input" />
        </div>
        <div class="half auth-half">
          <label class="auth-label">Email</label>
          <input type="email" name="email" placeholder="you@example.com" required class="auth-input" />
        </div>
      </div>
      <div class="form-row split auth-form-row">
        <div class="half auth-half">
          <label class="auth-label">Пароль</label>
          <input type="password" name="password" placeholder="минимум 8 символов" minlength="8" required class="auth-input" />
        </div>
        <div class="half auth-half">
          <label class="auth-label">Повтор пароля</label>
          <input type="password" name="password2" placeholder="ещё раз пароль" minlength="8" required class="auth-input" />
        </div>
      </div>
      <div class="form-row-btn auth-form-actions">
        <button class="btn auth-btn" type="submit">Создать аккаунт</button>
      </div>
    </form>
  </div>
</div>  

