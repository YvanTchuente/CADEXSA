<?php

namespace Application\Membership\Chats;

enum ChatStatus: int
{
    case READ = 1;
    case UNREAD = 0;
}
