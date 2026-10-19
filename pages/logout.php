<?php
session_include();

destroy_session();

redirect('login', 303);
