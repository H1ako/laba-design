<?php

global $SITE_URL, $session;

$session->set_csrf_meta();
?>
<link rel="stylesheet" href="<?= $SITE_URL ?>/assets/styles/css/global.css">
<script defer src="<?= $SITE_URL ?>/assets/scripts/libs/loadingScreen.js"></script>
<script defer src="<?= $SITE_URL ?>/assets/scripts/libs/phoneFieldFormatter.js"></script>
<script defer src="<?= $SITE_URL ?>/assets/scripts/libs/banner.js"></script>
<script defer src="<?= $SITE_URL ?>/assets/scripts/libs/cart.js"></script>
<script defer src="<?= $SITE_URL ?>/assets/scripts/libs/header.js"></script>
