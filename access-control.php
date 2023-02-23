<?php

use Cadexsa\Domain\Model\ExStudent\Level;
use Cadexsa\Domain\Model\ExStudent\ExStudent;

$gate->defineRule('account', function (ExStudent $user) {
    return isLoggedIn($user->getUsername());
});

$gate->defineRule('event', function (ExStudent $user) {
    return isLoggedIn($user->getUsername());
});

$gate->defineRule('cms_home', function (ExStudent $user) {
    return isLoggedIn($user->getUsername()) && $user->getLevel() === Level::EDITOR;
});

$gate->defineRule('cms_news_manager', function (ExStudent $user) {
    return isLoggedIn($user->getUsername()) && $user->getLevel() === Level::EDITOR;
});

$gate->defineRule('cms_news_article_edit', function (ExStudent $user) {
    return isLoggedIn($user->getUsername()) && $user->getLevel() === Level::EDITOR;
});
