<?php

namespace App\Libraries\Ui;

class DataRendererHelper
{

    /**
     * Generate action link for iframe popup.
     *
     * @param string $title
     * @param string $modalClassname
     * @param string $divId
     * @param integer $iframeHeight
     * @param mixed $iframeWidth
     * @param mixed $href
     * @param string $icon
     * @return string
     */
    public function generateActionLink(
        $title,
        $modalClassname,
        $divId,
        $iframeHeight,
        $iframeWidth,
        $href,
        $icon
    ) {
    
        return "<li>"
            ."<a href=\"javascript:void(0)\"  "
            ."data-original-title=\"".$title."\" data-placement=\"top\" "
            ."data-toggle=\"modal\" "
            ."data-target=\"#".$divId."\" "
            ."class=\"".$divId."\" "
            ."data-height=\"".$iframeHeight."\" "
            ."data-width=\"".$iframeWidth."\" "
            ."data-href=\"".$href."\">"
            . $icon
            ."</a>"
            ."</li>";
    }

    /**
     * Generate link for add/edit pages.
     *
     * @param string $title
     * @param mixed $href
     * @param string $icon
     * @return string
     */
    public function generateLink($title, $href, $icon, $class)
    {
        return "<li>"
            ."<a href=\"".$href."\"  "
            ."data-original-title=\"".$title."\" "
            ."data-placement=\"top\" "
            ."class=\"".$class."\">"
            .$icon
            ."</a>"
            ."</li>";
    }
}
