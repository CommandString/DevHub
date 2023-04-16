<?php

use Twig\Functions\Ellipses;
use Twig\Functions\Entrypoint;
use Twig\Functions\getCurrentUser;
use Twig\Functions\IsLoggedIn;
use Twig\Functions\Render;

Entrypoint::add();
Render::add();
Ellipses::add();
IsLoggedIn::add();
GetCurrentUser::add();
