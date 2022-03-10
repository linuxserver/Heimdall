<?php

namespace Http\Message\MultipartStream;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
interface MimetypeHelper
{
    /**
     * Determines the mimetype of a file by looking at its extension.
     *
     * @param string $filename
     *
     * @return string|null
     */
    public function getMimetypeFromFilename($filename);

    /**
     * Maps a file extensions to a mimetype.
     *
     * @param string $extension The file extension
     *
     * @return string|null
     */
    public function getMimetypeFromExtension($extension);
}
