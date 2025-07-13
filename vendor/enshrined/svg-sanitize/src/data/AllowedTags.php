<?php
namespace enshrined\svgSanitize\data;

/**
 * Class AllowedTags
 *
 * @package enshrined\svgSanitize\data
 */
class AllowedTags implements TagInterface
{

    /**
     * Returns an array of tags
     *
     * @return array
     */
    public static function getTags()
    {
        return array (
            // HTML
            'a',
            'font',
            'image',
            'style',

            // SVG
            'svg',
            'altglyph',
            'altglyphdef',
            'altglyphitem',
            'animatecolor',
            'animatemotion',
            'animatetransform',
            'circle',
            'clippath',
            'defs',
            'desc',
            'ellipse',
            'filter',
            'font',
            'g',
            'glyph',
            'glyphref',
            'hkern',
            'image',
            'line',
            'lineargradient',
            'marker',
            'mask',
            'metadata',
            'mpath',
            'path',
            'pattern',
            'polygon',
            'polyline',
            'radialgradient',
            'rect',
            'stop',
            'switch',
            'symbol',
            'text',
            'textpath',
            'title',
            'tref',
            'tspan',
            'use',
            'view',
            'vkern',

            // SVG Filters
            'feBlend',
            'feColorMatrix',
            'feComponentTransfer',
            'feComposite',
            'feConvolveMatrix',
            'feDiffuseLighting',
            'feDisplacementMap',
            'feDistantLight',
            'feFlood',
            'feFuncA',
            'feFuncB',
            'feFuncG',
            'feFuncR',
            'feGaussianBlur',
            'feMerge',
            'feMergeNode',
            'feMorphology',
            'feOffset',
            'fePointLight',
            'feSpecularLighting',
            'feSpotLight',
            'feTile',
            'feTurbulence',

            //text
            '#text'
        );
    }
}
