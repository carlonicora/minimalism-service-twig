<?php
namespace CarloNicora\Minimalism\Services\Twig\Extensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class JsonApiExtension extends AbstractExtension
{
    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('lookupIncluded', [$this, 'lookupIncluded']),
        ];
    }

    /**
     * @param string $type
     * @param string $id
     * @param array $included
     * @return array|null
     */
    public function lookupIncluded(
        string $type,
        string $id,
        array  $included,
    ): ?array
    {
        foreach ($included as $includedItem) {
            if ($includedItem['type'] === $type && $includedItem['id'] === $id) {
                return $includedItem;
            }
        }

        return null;
    }
}