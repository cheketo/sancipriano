<?php
if( !isset($_COOKIE['renovatio-skin']) || !$_COOKIE['renovatio-skin']) $_COOKIE['renovatio-skin'] = 'skin-purple-light'; ?>
<body class="hold-transition <?php if(isset($_COOKIE['sidebarmenu'])) echo $_COOKIE['sidebarmenu'] ?> <?= $_COOKIE['renovatio-skin'] ?> sidebar-mini">
<!-- Site wrapper -->
<div class="wrapper">
  <?php include('../../includes/inc.header.php'); ?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <?php include('../../includes/inc.breadcrums.php'); ?>
    <!-- Main content -->
    <section class="content">
