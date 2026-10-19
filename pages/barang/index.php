<?php 
session_include();
include_component("header", ["title" => "laporan"]) 
?>

<h1>Ini Barang</h1>
<p>Welcome, <?= esc(get_session()['user']) ?>.</p>

<?php include_component("footer") ?>
