<?php 
  use App\HTML\Form;
  $form = new Form();
?>

<div class="flex-center">
  <section class="login-wrapper">
    <div class="title-login-page">Page de connexion</div>
    <form action="">
      <?= $form->field('Nom d\'utilusateur', 'username', 'text'); ?>
      <?= $form->field('Mot de passe', 'password', 'password'); ?>
    </form>
    <div class="flex-center" style="margin-top: 50px">
      <button class="fat-btn" type="submit">Se connecter</button>
    </div>
  </section>
</div>