<?php

namespace Filament\Schema\Components;

use Closure;
use Filament\Schema\Components\Concerns\CanBeCollapsed;
use Filament\Schema\Components\Concerns\CanBeCompacted;
use Filament\Schema\Components\Concerns\EntanglesStateWithSingularRelationship;
use Filament\Schema\Components\Concerns\HasDescription;
use Filament\Schema\Components\Concerns\HasFooterActions;
use Filament\Schema\Components\Concerns\HasHeaderActions;
use Filament\Schema\Components\Concerns\HasHeading;
use Filament\Schema\Components\Contracts\CanConcealComponents;
use Filament\Schema\Components\Contracts\CanEntangleWithSingularRelationships;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Filament\Support\Concerns\HasIcon;
use Filament\Support\Concerns\HasIconColor;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class Section extends Component implements CanConcealComponents, CanEntangleWithSingularRelationships, Contracts\HasFooterActions, Contracts\HasHeaderActions
{
    use CanBeCollapsed;
    use CanBeCompacted;
    use EntanglesStateWithSingularRelationship;
    use HasDescription;
    use HasExtraAlpineAttributes;
    use HasFooterActions;
    use HasHeaderActions;
    use HasHeading;
    use HasIcon;
    use HasIconColor;

    /**
     * @var view-string
     */
    protected string $view = 'filament-schema::components.section';

    protected bool | Closure | null $isAside = null;

    protected bool | Closure $isFormBefore = false;

    /**
     * @param  string | array<Component> | Htmlable | Closure | null  $heading
     */
    final public function __construct(string | array | Htmlable | Closure | null $heading = null)
    {
        is_array($heading)
            ? $this->childComponents($heading)
            : $this->heading($heading);
    }

    /**
     * @param  string | array<Component> | Htmlable | Closure | null  $heading
     */
    public static function make(string | array | Htmlable | Closure | null $heading = null): static
    {
        $static = app(static::class, ['heading' => $heading]);
        $static->configure();

        return $static;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->columnSpan('full');

        $this->key(function (Section $component): ?string {
            if ($statePath = $component->getStatePath(isAbsolute: false)) {
                return $statePath;
            }

            $heading = $this->getHeading();

            if (blank($heading)) {
                return null;
            }

            return Str::slug($heading);
        });
    }

    public function aside(bool | Closure | null $condition = true): static
    {
        $this->isAside = $condition;

        return $this;
    }

    public function canConcealComponents(): bool
    {
        return $this->isCollapsible();
    }

    public function isAside(): bool
    {
        return (bool) ($this->evaluate($this->isAside) ?? false);
    }

    public function formBefore(bool | Closure $condition = true): static
    {
        $this->isFormBefore = $condition;

        return $this;
    }

    public function isFormBefore(): bool
    {
        return (bool) $this->evaluate($this->isFormBefore);
    }
}
