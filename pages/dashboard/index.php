<?php
role_include('admin');
include_component('header', ['title' => 'Dashboard']);
?>

<h1>Dashboard</h1>
<p>Welcome, <?= esc(get_session()['user']) ?>.</p>

<?php include_component('footer'); ?>
