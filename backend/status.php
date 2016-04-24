<?php

echo json_decode( file_get_contents("server.json") )->status;

?>