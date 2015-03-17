<?php
require_once('conf.php');
include ("functions/global-loginincludes.php");
$objechecklogin->db_connect();
$objechecklogin->session_end();
?>
<script language="Javascript">
function Fermer()
{
opener=self;
self.close();
}
Fermer();
</script>
		<br />
		<br />
		</div>
<div id="content">
			<h2>Deconnexion</h2>
			<p>
Vous vous êtes bien déconnecté.
</p>
		</div>
<div id="footer">
	</div>
	
</div>
</body>
</html>
