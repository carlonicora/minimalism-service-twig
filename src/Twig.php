<?php
namespace CarloNicora\Minimalism\Services\Twig;

use CarloNicora\JsonApi\Document;
use CarloNicora\Minimalism\Abstracts\AbstractService;
use CarloNicora\Minimalism\Interfaces\TransformerInterface;
use CarloNicora\Minimalism\Services\Path;
use CarloNicora\Minimalism\Services\Twig\Extensions\JsonApiExtension;
use Exception;
use RuntimeException;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Twig extends AbstractService implements TransformerInterface
{
    /** @var string  */
    private string $twigCache;

    /**
     * Twig constructor.
     * @param Path $path
     * @param bool $MINIMALISM_SERVICE_TWIG_USE_CACHE
     */
    public function __construct(
        private Path $path,
        private bool $MINIMALISM_SERVICE_TWIG_USE_CACHE=true,
    ) {
        parent::__construct();

        $this->twigCache = $this->path->getRoot()
            . DIRECTORY_SEPARATOR . 'cache'
            . DIRECTORY_SEPARATOR . 'twig';

        $defaultMask = umask(0);

        if (!is_dir($this->twigCache) && !mkdir($this->twigCache) && !is_dir($this->twigCache)) {
            throw new RuntimeException('Cannot create twig cache directory', 500);
        }
        umask($defaultMask);

    }

    /**
     * @return string|null
     */
    public static function getBaseInterface(
    ): ?string
    {
        return TransformerInterface::class;
    }

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

        $options = [];

        if ($this->MINIMALISM_SERVICE_TWIG_USE_CACHE){
            $options['cache'] = $this->twigCache;
        }

        $twigLoader = new FilesystemLoader($paths);
        $twig = new Environment($twigLoader, $options);
        $twig->addExtension(new JsonApiExtension());

        return $twig->load($viewFile . '.twig')->render($document->prepare());
    }

    /**
     * @return string
     */
    public function getContentType(): string
    {
        return 'text/html';
    }
}