<?php

declare(strict_types=1);

namespace Core\App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait TotpTrait
{
    #[ORM\Column(name: 'totp_secret', type: 'string', length: 32, nullable: true)]
    protected ?string $totpSecret = null;

    #[ORM\Column(name: 'totp_enabled', type: 'boolean', options: ['default' => false])]
    protected bool $totpEnabled = false;

    /** @var string[]|null */
    #[ORM\Column(name: 'recovery_codes', type: Types::JSON, nullable: true)]
    protected ?array $recoveryCodes = null;

    public function enableTotp(string $secret): self
    {
        $this->totpSecret  = $secret;
        $this->totpEnabled = true;

        return $this;
    }

    public function disableTotp(): self
    {
        $this->totpSecret    = null;
        $this->totpEnabled   = false;
        $this->recoveryCodes = null;

        return $this;
    }

    public function isTotpEnabled(): bool
    {
        return $this->totpEnabled;
    }

    public function getTotpSecret(): ?string
    {
        return $this->totpSecret;
    }

    /**
     * @param string[]|null $recoveryCodes
     */
    public function setRecoveryCodes(?array $recoveryCodes = null): void
    {
        $this->recoveryCodes = $recoveryCodes;
    }

    /**
     * @return string[]|null
     */
    public function getRecoveryCodes(): ?array
    {
        return $this->recoveryCodes;
    }
}