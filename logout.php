<?php
require_once __DIR__ . '/auth.php';

logout_user();

// Po wylogowaniu przenosimy na stronę główną
header('Location: /');
exit;
