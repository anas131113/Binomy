<?php
require_once __DIR__ . '/../../api/helpers.php';

session_destroy();
jsonResponse(true, null, 'Déconnexion réussie.');
