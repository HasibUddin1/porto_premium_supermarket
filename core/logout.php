<?php 

// Path: core\logout.php

session_start();

session_destroy();

header("Location: ../index");