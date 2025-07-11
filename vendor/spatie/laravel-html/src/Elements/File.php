<?php

namespace Spatie\Html\Elements;

use Spatie\Html\BaseElement;
use Spatie\Html\Elements\Attributes\Autofocus;
use Spatie\Html\Elements\Attributes\Disabled;
use Spatie\Html\Elements\Attributes\Name;
use Spatie\Html\Elements\Attributes\Required;

class File extends BaseElement
{
    use Autofocus;
    use Disabled;
    use Name;
    use Required;

    protected $tag = 'input';

    public const ACCEPT_AUDIO = 'audio/*';
    public const ACCEPT_VIDEO = 'video/*';
    public const ACCEPT_IMAGE = 'image/*';

    public function __construct()
    {
        parent::__construct();

        $this->attributes->setAttribute('type', 'file');
    }

    /**
     * @param string|null $name
     *
     * @return static
     */
    public function accept($type)
    {
        return $this->attribute('accept', $type);
    }

    /**
     * @return static
     */
    public function acceptAudio()
    {
        return $this->attribute('accept', self::ACCEPT_AUDIO);
    }

    /**
     * @return static
     */
    public function acceptVideo()
    {
        return $this->attribute('accept', self::ACCEPT_VIDEO);
    }

    /**
     * @return static
     */
    public function acceptImage()
    {
        return $this->attribute('accept', self::ACCEPT_IMAGE);
    }

    /**
     * @return static
     */
    public function multiple()
    {
        return $this->attribute('multiple');
    }
}
