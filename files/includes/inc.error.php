<?php

	$error = isset($_GET['error'])? $_GET['error']: 'Un error ha ocurrido. No ha sido detallado.';

?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	</head>
	<body>
		<?php echo $error; ?>
	</body>
</html>