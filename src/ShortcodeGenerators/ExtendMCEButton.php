<?php


namespace WPDM\AddOn\ArchivePage\ShortcodeGenerators;


class ExtendMCEButton
{
    function __construct()
    {

    }

    function render()
    {
        include __DIR__.'/views/mce-button-helper.php';
    }
}