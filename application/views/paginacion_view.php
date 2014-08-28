<html>
	<h1>LISTA DE NOTICIAS</h1>

	<?php foreach($noticias as $n):?>
	<h3><?= $n['titulo']; ?></h3>
	<p><?= $n['cuerpo']; ?></p>
	<hr />
	<?php endforeach; ?>

	<?= $paginacion; ?>

</html>