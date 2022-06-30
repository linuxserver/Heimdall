<?php

declare(strict_types=1);

namespace PHPSTORM_META;

override(\Nette\Utils\Arrays::get(0), elementType(0));
override(\Nette\Utils\Arrays::getRef(0), elementType(0));
override(\Nette\Utils\Arrays::grep(0), type(0));
override(\Nette\Utils\Arrays::toObject(0), type(1));

expectedArguments(\Nette\Utils\Arrays::grep(), 2, PREG_GREP_INVERT);
expectedArguments(\Nette\Utils\Image::resize(), 2, \Nette\Utils\Image::SHRINK_ONLY, \Nette\Utils\Image::STRETCH, \Nette\Utils\Image::FIT, \Nette\Utils\Image::FILL, \Nette\Utils\Image::EXACT);
expectedArguments(\Nette\Utils\Image::calculateSize(), 4, \Nette\Utils\Image::SHRINK_ONLY, \Nette\Utils\Image::STRETCH, \Nette\Utils\Image::FIT, \Nette\Utils\Image::FILL, \Nette\Utils\Image::EXACT);
expectedArguments(\Nette\Utils\Json::encode(), 1, \Nette\Utils\Json::PRETTY);
expectedArguments(\Nette\Utils\Json::decode(), 1, \Nette\Utils\Json::FORCE_ARRAY);
expectedArguments(\Nette\Utils\Strings::split(), 2, \PREG_SPLIT_NO_EMPTY | \PREG_OFFSET_CAPTURE);
expectedArguments(\Nette\Utils\Strings::match(), 2, \PREG_OFFSET_CAPTURE | \PREG_UNMATCHED_AS_NULL);
expectedArguments(\Nette\Utils\Strings::matchAll(), 2, \PREG_OFFSET_CAPTURE | \PREG_UNMATCHED_AS_NULL | \PREG_PATTERN_ORDER);
