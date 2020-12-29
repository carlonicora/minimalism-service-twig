<?php
namespace CarloNicora\Minimalism\Services\Twig;

use CarloNicora\JsonApi\Document;
use CarloNicora\Minimalism\Interfaces\ServiceInterface;
use CarloNicora\Minimalism\Interfaces\TransformerInterface;
use CarloNicora\Minimalism\Services\Path;
use Exception;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Twig implements ServiceInterface, TransformerInterface
{
    /**
     * Twig constructor.
     * @param Path $path
     */
    public function __construct(private Path $path) {}

    /**
     *
     */
    public function initialise(): void {}

    /**
     *
     */
    public function destroy(): void {}

    /**
     * @param Document $document
     * @param string $viewFile
     * @return string
     * @throws Exception
     */
    public function transform(Document $document, string $viewFile): string
    {
        $paths = [];

        if (file_exists(($defaultDirectory = $this->path->getRoot() . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Views'))){
            $paths[] = $defaultDirectory;
        }

        /*
        foreach ($this->services->paths()->getServicesViewsDirectories() as $additionalPaths) {
            $paths[] = $additionalPaths;
        }
        */

        $twigLoader = new FilesystemLoader($paths);
        $environment = new Environment($twigLoader);

        return $environment->render($viewFile . '.twig', $document->prepare());
    }

    /**
     * @return string
     */
    public function getContentType(): string
    {
        return 'text/html';
    }
}