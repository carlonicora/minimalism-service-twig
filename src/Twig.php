<?php
namespace CarloNicora\Minimalism\Services\Twig;

use CarloNicora\JsonApi\Document;
use CarloNicora\Minimalism\Interfaces\ServiceInterface;
use CarloNicora\Minimalism\Interfaces\TransformerInterface;
use Exception;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Twig implements ServiceInterface, TransformerInterface
{
    /**
     *
     */
    public function initialise(): void
    {
    }

    /**
     *
     */
    public function destroy(): void
    {
    }

    /**
     * @param Document $document
     * @param string $viewFile
     * @return string
     * @throws Exception
     */
    public function transform(Document $document, string $viewFile): string
    {
        $twigLoader = new FilesystemLoader();
        $environment = new Environment($twigLoader);

        return $environment->render($viewFile, $document->prepare());
    }

    /**
     * @return string
     */
    public function getContentType(): string
    {
        return 'text/html';
    }
}