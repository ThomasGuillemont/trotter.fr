<?php
define('SEARCH', '^[0-9a-zA-ZÀÁÂÆÇÈÉÊËÌÍÎÏÑÒÓÔŒÙÚÛÜÝŸàáâæçèéêëìíîïñòóôœùúûüýÿ=\/\^+·,\';:!°\[\]{}?*<>()&$#%._ -]{1,50}');
define('TEXTAREA', '^[0-9a-zA-ZÀÁÂÆÇÈÉÊËÌÍÎÏÑÒÓÔŒÙÚÛÜÝŸàáâæçèéêëìíîïñòóôœùúûüýÿ=\/\^+·,;:!°\[\]{}?*<>()&$#%._\n\r -]*$');

define('PSEUDO', '^[0-9a-zA-Z]{2,20}');
define('PASSWORD', '^[0-9a-zA-ZÀÁÂÆÇÈÉÊËÌÍÎÏÑÒÓÔŒÙÚÛÜÝŸàáâæçèéêëìíîïñòóôœùúûüýÿ=\/\^+·,;:!°\[\]{}?*<>()&$#@%._ -]{5,30}');

define('LIMIT', 50);
define('SECRET_JWT', 'C5646mYBjSMrMfazaz55f1az6546Bjq');
