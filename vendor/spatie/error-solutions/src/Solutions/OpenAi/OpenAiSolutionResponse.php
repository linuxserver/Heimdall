<?php

namespace Spatie\ErrorSolutions\Solutions\OpenAi;

use Illuminate\Support\Str;

class OpenAiSolutionResponse
{
    protected string $rawText;

    public function __construct(string $rawText)
    {
        $this->rawText = trim($rawText);
    }

    public function description(): string
    {
        return $this->between('FIX', 'ENDFIX', $this->rawText);
    }

    public function links(): array
    {
        $rawText = Str::finish($this->rawText, 'ENDLINKS');

        $textLinks = $this->between('LINKS', 'ENDLINKS', $rawText);

        $textLinks = explode(PHP_EOL, $textLinks);

        $textLinks = array_map(function ($textLink) {
            $textLink = str_replace('\\', '\\\\', $textLink);
            $textLink = str_replace('\\\\\\', '\\\\', $textLink);

            return json_decode($textLink, true);
        }, $textLinks);

        array_filter($textLinks);

        $links = [];
        foreach ($textLinks as $textLink) {
            if (isset($textLink['title']) && isset($textLink['url'])) {
                $links[$textLink['title']] = $textLink['url'];
            }
        }

        return $links;
    }

    protected function between(string $start, string $end, string $text): string
    {
        $startPosition = strpos($text, $start);
        if ($startPosition === false) {
            return "";
        }

        $startPosition += strlen($start);
        $endPosition = strpos($text, $end, $startPosition);

        if ($endPosition === false) {
            return "";
        }

        return trim(substr($text, $startPosition, $endPosition - $startPosition));
    }
}
