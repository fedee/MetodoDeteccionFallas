<html>
	<h1>LISTA DE NOTICIAS</h1>

	<?php foreach($mensajes as $m):?>
	<h3><?= $m['titulo']; ?></h3>
	<p><?= $m['cuerpo']; ?></p>
	<hr />
	<?php endforeach; ?>

	<?= $paginacion; ?>

</html>