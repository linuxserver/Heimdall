<?php

namespace Spatie\Html\Elements;

use Spatie\Html\BaseElement;
use Spatie\Html\Elements\Attributes\Autofocus;
use Spatie\Html\Elements\Attributes\Disabled;
use Spatie\Html\Elements\Attributes\Name;
use Spatie\Html\Elements\Attributes\Required;

/**
 * @method static acceptIf(bool $condition, string|null $type)
 * @method static acceptIfNotNull(bool $condition, string|null $type)
 * @method static acceptUnless(bool $condition, string|null $type)
 * @method static acceptAudioIf(bool $condition)
 * @method static acceptAudioIfNotNull(bool $condition)
 * @method static acceptAudioUnless(bool $condition)
 * @method static acceptVideoIf(bool $condition)
 * @method static acceptVideoIfNotNull(bool $condition)
 * @method static acceptVideoUnless(bool $condition)
 * @method static acceptImageIf(bool $condition)
 * @method static acceptImageIfNotNull(bool $condition)
 * @method static acceptImageUnless(bool $condition)
 * @method static multipleIf(bool $condition)
 * @method static multipleIfNotNull(bool $condition)
 * @method static multipleUnless(bool $condition)
 */
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
     * @param string|null $type
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
