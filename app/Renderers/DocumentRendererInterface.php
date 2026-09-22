<?php
namespace App\Renderers;

/**
 * Interface DocumentRendererInterface
 *
 * Defines the contract for converting a Website Document Model (JSON)
 * into a renderable visual representation (HTML/DOM).
 */
interface DocumentRendererInterface {
    /**
     * Renders the document model to a string (HTML).
     *
     * @param array $documentModel The JSON-structured Website Document Model
     * @return string Validated HTML output
     */
    public function render(array $documentModel): string;
}
