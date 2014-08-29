<?php
 final class Omx_Hooks_Model_Helper { static public function getPrefixCode ($prefix) { switch (strtolower($prefix)) { case 'mr': return 1; break; case 'mrs': return 2; break; case 'ms': return 3; break; case 'miss' : return 4; break; default: return 0; break; } } } ?>
