<?php

namespace Baubyte\View;

use Baubyte\View\Exceptions\ViewNotFoundException;

/**
 *  Baubyte template engine.
 */
class BaubyteEngine implements View {
    /**
     * Directory where the views are located.
     *
     * @var string
     */
    protected string $viewsDirectory;
    /**
     * Layout to use in case none was given.
     *
     * @var string
     */
    protected string $defaultLayout = "main";
    /**
     * Annotation used in layouts to mark where to put the view content.
     *
     * @var string
     */
    protected $contentAnnotation = "@content";
    /**
     * Annotation used in layouts to mark where to put input fields PUT.
     *
     * @var string
     */
    protected $putAnnotation = "@PUT";
    /**
     * Annotation used in layouts to mark where to put input fields DELETE.
     *
     * @var string
     */
    protected $deleteAnnotation = "@DELETE";

    /**
     * input field for PUT method.
     *
     * @var string
     */
    protected $inputMethodPut = "<input type='hidden' name='_method' value='PUT'>";

    /**
     * input field for DELETE method.
     *
     * @var string
     */
    protected $inputMethodDelete = "<input type='hidden' name='_method' value='DELETE'>";

    /**
     * BaubyteEngine constructor.
     *
     * @param string $viewsDirectory Directory where the views are located.
     */
    public function __construct(string $viewsDirectory) {
        $this->viewsDirectory = $viewsDirectory;
    }
    /**
     * @inheritDoc
     */
    public function render(string $view, array $params = [], string $layout = null): string {
        $layoutContent = $this->renderLayout($layout ?? $this->defaultLayout);
        $viewContent = $this->renderView($view, $params);
        return str_replace([
            $this->contentAnnotation,
            $this->putAnnotation,
            $this->deleteAnnotation
        ], [
            $viewContent,
            $this->inputMethodPut,
            $this->inputMethodDelete
        ], $layoutContent);
    }
    /**
     * Render view only, without replacing annotations.
     *
     * @param $view View to render.
     * @param string $params Parameters passed to view.
     * @return string Rendered view.
     */
    protected function renderView(string $view, array $params = []): string {
        return $this->phpFileOutput("{$this->viewsDirectory}".DIRECTORY_SEPARATOR."{$view}.php", $params);
    }
    /**
     * Render layout only, without replacing annotations.
     *
     * @param $layout
     * @return string Rendered layout.
     */
    protected function renderLayout(string $layout): string {
        return $this->phpFileOutput("{$this->viewsDirectory}".DIRECTORY_SEPARATOR."layouts".DIRECTORY_SEPARATOR."{$layout}.php");
    }
    /**
     * Process PHP file and get string output.
     *
     * @param string $phpFile
     * @param array $params Variables to be made available inside the file.
     * @return string Processed output.
     */
    protected function phpFileOutput(string $phpFile, array $params = []): string {
        foreach ($params as $param => $value) {
            $$param = $value;
        }
        if (!is_file($phpFile)) {
            throw new ViewNotFoundException("El layout o vista {$phpFile} no se encuentra.");
        }
        ob_start();
        include_once $phpFile;
        return ob_get_clean();
    }
}
