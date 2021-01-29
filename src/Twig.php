<?php
namespace CarloNicora\Minimalism\Services\Twig;

use CarloNicora\JsonApi\Document;
use CarloNicora\Minimalism\Interfaces\ServiceInterface;
use CarloNicora\Minimalism\Interfaces\TransformerInterface;
use CarloNicora\Minimalism\Services\Path;
use Exception;
use RuntimeException;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Twig implements ServiceInterface, TransformerInterface
{
    /** @var string  */
    private string $twigCache;

    /**
     * Twig constructor.
     * @param Path $path
     */
    public function __construct(
        private Path $path
    ) {
        $this->twigCache = $this->path->getRoot()
            . DIRECTORY_SEPARATOR . 'cache'
            . DIRECTORY_SEPARATOR . 'twig';

        $defaultMask = umask(0);

        if (!file_exists($this->twigCache) && !mkdir($this->twigCache, 0777) && !is_dir($this->twigCache)) {
            throw new RuntimeException('Cannot create twig cache directory', 500);
        }
        umask($defaultMask);

    }

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

        foreach ($this->path->getServicesViewsDirectories() ?? [] as $additionalPaths) {
            $paths[] = $additionalPaths;
        }

        $twigLoader = new FilesystemLoader($paths);
        $twig = new Environment($twigLoader, [
            'cache' => $this->twigCache,
        ]);
        $template = $twig->load($viewFile . '.twig');
        return $template->render($document->prepare());
    }

    /**
     * @return string
     */
    public function getContentType(): string
    {
        return 'text/html';
    }
}