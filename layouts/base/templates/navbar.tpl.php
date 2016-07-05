<?php $this->before('navbar') ?>
<header id="top-nav" class="navbar default <?php $this->insert('navbar.class') ?>" role="banner">
  <div class="container">
    <?php $this->attach('navbar') ?>
  </div>
</header>
<?php $this->after('navbar') ?>
