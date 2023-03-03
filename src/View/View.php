<?php

namespace Baubyte\View;

/**
 * View template render
 */
interface View {
    /**
     * Render given view passing `$params` and using `$layout`.
     *
     * @param string $view
     * @param string $params Parameters passed to view.
     * @param string $layout Layout to use.
     * @return string Rendered content.
     */
    public function render(string $view, array $params = [], string $layout = null): string;
}
