<div class="flex-center">
  <section class="login-wrapper">
    <div class="title-login-page">Page de connexion</div>
    <form action="" method="post">
      <input type="hidden" name="context" value="connect" />
      <?= $forms->FieldWithLabel('Nom d\'utilusateur', 'username', 'text'); ?>
      <?= $forms->FieldWithLabel('Mot de passe', 'password', 'password'); ?>

      <div class="flex-center" style="margin-top: 50px">
        <button class="fat-btn" type="submit">Se connecter</button>
      </div>
    </form>
  </section>
</div>