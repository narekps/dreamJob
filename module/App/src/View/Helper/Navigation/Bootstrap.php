<?php

namespace App\View\Helper\Navigation;

use RecursiveIteratorIterator;
use Zend\Navigation\AbstractContainer;
use Zend\Navigation\Page\AbstractPage;
use Zend\View\Exception;
use Zend\View\Helper\Navigation\Menu;


class Bootstrap extends Menu
{

    const LINK_CLASS = 'nav-link';

    /**
     * Добавляет CSS-класс к элементу a.
     *
     * @param AbstractPage $page
     * @param bool         $escapeLabel
     * @param bool         $addClassToListItem
     *
     * @return string
     */
    public function htmlify(AbstractPage $page, $escapeLabel = true, $addClassToListItem = false)
    {
        $html = parent::htmlify($page, $escapeLabel, $addClassToListItem);

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $xpath   = new \DOMXPath($dom);
        $query   = "//a";
        $entries = $xpath->query($query);
        /** @var \DOMElement $entry */
        foreach ($entries as $entry) {
            $class = $entry->getAttribute("class") . " " . self::LINK_CLASS;
            $entry->setAttribute('class', $class);
            $html = $dom->saveHTML($entry);
        }

        return $html;
    }
}
