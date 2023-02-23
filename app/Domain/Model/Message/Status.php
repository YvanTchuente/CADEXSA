<?php

namespace Cadexsa\Domain\Model\Message;

/**
 * Message status
 */
enum Status: int
{
    case READ = 1;
    case UNREAD = 0;
}
