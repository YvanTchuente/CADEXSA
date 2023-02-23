<?php

declare(strict_types=1);

namespace Cadexsa\Domain\Exceptions;

class AuthenticationException extends \RuntimeException
{
    /**
     * The path the user should be redirected to.
     *
     * @var string|null
     */
    private ?string $redirectTo;

    public function __construct(string $message = 'Unauthenticated.', string $redirectTo = null)
    {
        parent::__construct($message);

        $this->redirectTo = $redirectTo;
    }
    
    /**
     * Get the path the user should be redirected to.
     */
    public function redirectTo()
    {
        return $this->redirectTo;
    }
}
