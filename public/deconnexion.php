<?php
require __DIR__ . '/../src/bootstrap.php';

App\Auth::deconnecter();
header('Location: /index.php');
