<?php
/**
 * Hide Subpage Parents
 * Hides the parent titles from a subpage title.
 *
 * @author    Blake Harley <blake@blakeharley.com>
 * @version   0.1
 * @copyright Copyright (C) 2014 Blake Harley
 * @license   MIT License
 * @link      https://github.com/bharley/mw-hidesubpageparents
 */

// Prevent global hijackingengine
if (!defined('MEDIAWIKI')) die();

// Credits
$wgExtensionCredits['parserhook'][] = array(
    'name'         => 'Hide Subpage Parents',
    'description'  => 'Hides the parent titles from a subpage title.',
    'version'      => '0.1',
    'author'       => 'Blake Harley',
    'url'          => 'https://github.com/bharley/mw-hidesubpageparents',
    'license-name' => 'MIT',
);

// Available config options
$wgHideSubpageParentsDefaultOn  = true;
$wgHideSubpageParentsMainNSOnly = true;

// Hook
$wgHooks['ParserFirstCallInit'][] = 'HideSubpageParentsExtension::onParserFirstCallInit';
$wgHooks['ArticleViewHeader'][]   = 'HideSubpageParentsExtension::onArticleViewHeader';
$wgHooks['BeforePageDisplay'][]   = 'HideSubpageParentsExtension::onBeforePageDisplay';

// Set up internationalization
$wgExtensionMessagesFiles['HideNamespaceMagic'] = dirname(__FILE__) .'/HideSubpageParents.i18n.magic.php';

/**
 * Wrap the hook function in a class so we don't pollute the global namespace.
 */
class HideSubpageParentsExtension
{
    /**
     * @var bool|null Whether or not the user has altered functionality in the content.
     */
    protected static $inlineHide = null;

    /**
     * @var int|null The number of parents to show/hide
     */
    protected static $numberToMod = null;

    /**
     * @var bool Whether or not this is a subpage
     */
    protected static $isSubpage = false;

    /**
     * @param Article $article
     */
    public static function onArticleViewHeader($article)
    {
        global $wgHideSubpageParentsMainNSOnly;

        static::$isSubpage = $article->getTitle()->isSubpage() && (!$wgHideSubpageParentsMainNSOnly || $article->getTitle()->getNamespace() == NS_MAIN);
    }

    /**
     * @param OutputPage $out
     */
    public static function onBeforePageDisplay($out)
    {
        global $wgHideSubpageParentsDefaultOn;

        // Go away if we're not set to display
        if (!static::$isSubpage
            || (static::$inlineHide === false && static::$numberToMod === null) // "Show all parents"
            || (static::$inlineHide === true && static::$numberToMod === 0) // "Hide 0 parents"
            || (static::$inlineHide === null && !$wgHideSubpageParentsDefaultOn))
        {
            return true;
        }

        $titleParts = explode('/', $out->getPageTitle());

        // "Show 0 parents"
        if (static::$inlineHide === false && static::$numberToMod === 0)
        {
            static::$numberToMod = null;
        }

        if (static::$inlineHide !== null && static::$numberToMod !== null)
        {
            if (static::$numberToMod >= count($titleParts))
            {
                $titleParts = array(end($titleParts));
            }
            elseif (static::$inlineHide)
            {
                $titleParts = array_slice($titleParts, static::$numberToMod);
            }
            else
            {
                $titleParts = array_slice($titleParts, count($titleParts) - static::$numberToMod - 1);
            }
        }
        else
        {
            $titleParts = array(end($titleParts));
        }

        $out->setPageTitle(implode('/', $titleParts));

        return true;
    }

    /**
     * @param Parser $parser
     * @return bool
     */
    public static function onParserFirstCallInit($parser)
    {
        $parser->setFunctionHook('hideparents', array( __CLASS__, 'onHideParents'));
        $parser->setFunctionHook('showparents', array( __CLASS__, 'onShowParents'));

        return true;
    }

    /**
     * @param Parser $parser
     * @param string $numberToHide
     */
    public static function onHideParents($parser, $numberToHide)
    {
        if (strlen($numberToHide) > 0 && is_numeric($numberToHide))
        {
            static::$numberToMod = (int) $numberToHide;
        }

        static::$inlineHide = true;

        return '';
    }

    /**
     * @param Parser $parser
     * @param string $numberToShow
     */
    public static function onShowParents($parser, $numberToShow)
    {
        if (strlen($numberToShow) > 0 && is_numeric($numberToShow))
        {
            static::$numberToMod = (int) $numberToShow;
        }

        static::$inlineHide = false;

        return '';
    }
}

