<?php

apc_clear_cache();
apc_clear_cache('user');
apc_clear_cache('opcode');
echo json_encode(array('success' => true));
