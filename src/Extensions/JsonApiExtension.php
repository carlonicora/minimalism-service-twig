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
            new TwigFunction('includedRelationships', [$this, 'includedRelationships']),
        ];
    }

    /**
     * @param array|null $relationships
     * @param string $type
     * @param array $included
     * @return array|null
     */
    public function includedRelationships(
        ?array $relationships,
        string $type,
        array $included,
    ): ?array
    {
        if ($relationships === null){
            return null;
        }

        if (array_key_exists('id', $relationships['data'])){
            $elements = [
                'data' => [$relationships['data']]
            ];
        } else {
            $elements = $relationships;
        }

        $response = [];

        foreach ($elements['data'] as $identifier){
            foreach ($included as $includedItem) {
                if ($includedItem['type'] === $type && $includedItem['id'] === $identifier['id']) {
                    $response[] = $includedItem;
                    break;
                }
            }
        }

        return $response;
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