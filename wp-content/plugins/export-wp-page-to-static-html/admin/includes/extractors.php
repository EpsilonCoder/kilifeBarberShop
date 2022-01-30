<?php

namespace ExportHtmlAdmin\Extractors;
/**
 * Class name: Extractors
 */
class Extractors extends \ExportHtmlAdmin\Export_Wp_Page_To_Static_Html_Admin
{

    public function __construct()
    {
        $this->extractorFiles();
        $this->extractorClass();
    }

    public function extractorFiles()
    {
        require 'extractors/extract_stylesheets.php';
        require 'extractors/extract_scripts.php';
        require 'extractors/extract_images.php';
        require 'extractors/inline_css.php';
        require 'extractors/extract_meta_images.php';
        require 'extractors/extract_videos.php';

    }

    public function extractorClass()
    {
/*        $this->extract_stylesheets = new \extract_stylesheets($this);
        $this->extract_scripts = new \extract_scripts($this);
        $this->extract_images = new \extract_images($this);
        $this->inline_css = new \inline_css($this);
        $this->extract_meta_images = new \extract_meta_images($this);
        $this->extract_videos = new \extract_videos($this);*/

    }

}

new Extractors;