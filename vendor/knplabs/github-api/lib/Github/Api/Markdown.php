<?php

namespace Github\Api;

/**
 * Markdown Rendering API.
 *
 * @link   http://developer.github.com/v3/markdown/
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class Markdown extends AbstractApi
{
    /**
     * @param string $text
     * @param string $mode
     * @param string $context
     *
     * @return string
     */
    public function render($text, $mode = 'markdown', $context = null)
    {
        if (!in_array($mode, ['gfm', 'markdown'])) {
            $mode = 'markdown';
        }

        $params = [
            'text' => $text,
            'mode' => $mode,
        ];
        if (null !== $context && 'gfm' === $mode) {
            $params['context'] = $context;
        }

        return $this->post('/markdown', $params);
    }

    /**
     * @param string $file
     *
     * @return string
     */
    public function renderRaw($file)
    {
        return $this->post('/markdown/raw', [
            'file' => $file,
        ]);
    }
}
