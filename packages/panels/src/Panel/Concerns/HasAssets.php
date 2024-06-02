<?php

namespace Filament\Panel\Concerns;

use Closure;
use Filament\Support\Assets\Asset;
use Filament\Support\Facades\FilamentAsset;

trait HasAssets
{
    /**
     * @var array<string, array<Asset>>
     */
    protected array $assets = [];

    protected string | Closure | null $cspNonce = null;

    /**
     * @param  array<Asset>  $assets
     */
    public function assets(array $assets, string $package = 'app'): static
    {
        $this->assets[$package] = [
            ...($this->assets[$package] ?? []),
            ...$assets,
        ];

        return $this;
    }

    public function cspNonce(string | Closure | null $nonce = null): static
    {
        $this->cspNonce = $nonce;

        return $this;
    }

    public function registerAssets(): void
    {
        FilamentAsset::registerCspNonce($this->getCspNonce());

        foreach ($this->assets as $package => $assets) {
            FilamentAsset::register($assets, $package);
        }
    }

    public function getCspNonce(): ?string
    {
        return $this->evaluate($this->cspNonce);
    }
}
