<?php

namespace ExportHtmlAdmin\inline_css;
class inline_css
{

    private $export_Wp_Page_To_Static_Html_Admin;

    public function __construct($export_Wp_Page_To_Static_Html_Admin)
    {
        $this->export_Wp_Page_To_Static_Html_Admin = $export_Wp_Page_To_Static_Html_Admin;
    }

    /**
     * @since 2.0.0
     * @param string $url
     * @return array
     */
    public function get_inline_css($url="")
    {
        $host = $this->export_Wp_Page_To_Static_Html_Admin->get_host($url);
        $pathname_fonts = $this->export_Wp_Page_To_Static_Html_Admin->getFontsPath();
        $pathname_css = $this->export_Wp_Page_To_Static_Html_Admin->getCssPath();
        $pathname_images = $this->export_Wp_Page_To_Static_Html_Admin->getImgPath();
        $saveAllAssetsToSpecificDir = $this->export_Wp_Page_To_Static_Html_Admin->getSaveAllAssetsToSpecificDir();
        $exportTempDir = $this->export_Wp_Page_To_Static_Html_Admin->getExportTempDir();

        $path_to_dot = $this->export_Wp_Page_To_Static_Html_Admin->rc_path_to_dot($url, true, true);
        //$m_basename = $this->export_Wp_Page_To_Static_Html_Admin->middle_path_for_filename($url);

        $src = $this->export_Wp_Page_To_Static_Html_Admin->site_data;
        //preg_match_all("/(?<=\<img).*?(?=\/\>)/",$src,$matches_images);
        $stylesSrc = $src->find('style');
        if(!empty($stylesSrc)){
            foreach ($stylesSrc as $style) {
                $data = $style->innertext;

                preg_match_all("/(?<=url\().*?(?=\))/", $data, $images_links);

                foreach ($images_links as $key => $images) {
                    foreach ($images as $key => $image) {
                        $my_file = "";
                        //$path_to_dot = $this->export_Wp_Page_To_Static_Html_Admin->rc_path_to_dot($url, true, true);
                        if (strpos($image, 'data:') == false && strpos($image, 'svg+xml') == false && strpos($image, 'svg') == false && strpos($image, 'base64') == false) {
                            $image = html_entity_decode($image, ENT_QUOTES);
                            $image = $this->export_Wp_Page_To_Static_Html_Admin->ltrim_and_rtrim($image);
                            $url_basename = $this->export_Wp_Page_To_Static_Html_Admin->url_to_basename($image);
                            $url_basename = $this->export_Wp_Page_To_Static_Html_Admin->filter_filename($url_basename);
                            $item_url = url_to_absolute($url, $image);

                            if(strpos($item_url, $host)!==false){
                                $fontExt = array("eot", "woff", "woff2", "ttf", "otf");
                                $urlExt = pathinfo($url_basename, PATHINFO_EXTENSION);
                                if (in_array($urlExt, $fontExt)) {
                                    $my_file = $pathname_fonts . $url_basename;
                                    $data = str_replace($image, $path_to_dot . 'fonts/' . $url_basename, $data);
                                }

                                $urlExt = pathinfo($item_url, PATHINFO_EXTENSION);
                                if (in_array($urlExt, $this->export_Wp_Page_To_Static_Html_Admin->getImageExtensions())) {
                                    $my_file = $pathname_images . $url_basename;
                                    $data = str_replace($image, $path_to_dot . 'images/' . $url_basename, $data);

                                }

                                if (strpos($item_url, 'css') !== false) {
                                    $my_file = $pathname_css . $url_basename;
                                    $data = str_replace($image, $path_to_dot . 'css/' . $url_basename, $data);
                                }

                                if(!$saveAllAssetsToSpecificDir){
                                    $middle_p = $this->export_Wp_Page_To_Static_Html_Admin->rc_get_url_middle_path_for_assets($item_url);
                                    if(!file_exists($exportTempDir .'/'. $middle_p)){
                                        @mkdir($exportTempDir .'/'. $middle_p, 0777, true);
                                    }
                                    $my_file = $exportTempDir .'/'. $middle_p .'/'. $url_basename;
                                }

                                if (!empty($my_file)&&!file_exists($my_file)) {

                                    $handle = @fopen($my_file, 'w') or die('Cannot open file:  ' . $my_file);

                                    if ($this->export_Wp_Page_To_Static_Html_Admin->update_export_log($item_url)) {
                                        $item_data = $this->export_Wp_Page_To_Static_Html_Admin->get_url_data($item_url);
                                    }
                                    @fwrite($handle, $item_data);
                                    @fclose($handle);

                                    $this->export_Wp_Page_To_Static_Html_Admin->update_urls_log($image, $url_basename, 'new_file_name');
                                    $this->export_Wp_Page_To_Static_Html_Admin->update_urls_log($image, 1);

                                }
                            }
                        }
                    }
                }

                $style->innertext = $data;
            }

            $this->export_Wp_Page_To_Static_Html_Admin->site_data = $src;
        }
        return true;
    }

    public function get_div_inline_css($url="")
    {
        $host = $this->export_Wp_Page_To_Static_Html_Admin->get_host($url);
        $pathname_fonts = $this->export_Wp_Page_To_Static_Html_Admin->getFontsPath();
        $pathname_css = $this->export_Wp_Page_To_Static_Html_Admin->getCssPath();
        $pathname_images = $this->export_Wp_Page_To_Static_Html_Admin->getImgPath();
        $saveAllAssetsToSpecificDir = $this->export_Wp_Page_To_Static_Html_Admin->getSaveAllAssetsToSpecificDir();
        $exportTempDir = $this->export_Wp_Page_To_Static_Html_Admin->getExportTempDir();

        $path_to_dot = $this->export_Wp_Page_To_Static_Html_Admin->rc_path_to_dot($url, true, true);
        //$m_basename = $this->export_Wp_Page_To_Static_Html_Admin->middle_path_for_filename($url);

        $src = $this->export_Wp_Page_To_Static_Html_Admin->site_data;
        //preg_match_all("/(?<=\<img).*?(?=\/\>)/",$src,$matches_images);
        $stylesDivs = $src->find('div[style]');
        if(!empty($stylesDivs)){
            foreach ($stylesDivs as $div) {

                if(isset($div->style)){
                    $data = $div->style;

                    preg_match_all("/(?<=url\().*?(?=\))/", $data, $images_links);

                    foreach ($images_links as $key => $images) {
                        foreach ($images as $key => $image) {
                            //$path_to_dot = $this->export_Wp_Page_To_Static_Html_Admin->rc_path_to_dot($url, true, true);
                            if (strpos($image, 'data:') == false && strpos($image, 'svg+xml') == false && strpos($image, 'svg') == false && strpos($image, 'base64') == false) {
                                //$this->export_Wp_Page_To_Static_Html_Admin->update_urls_log($image);
                                $image = html_entity_decode($image, ENT_QUOTES);
                                $image = $this->export_Wp_Page_To_Static_Html_Admin->ltrim_and_rtrim($image);

                                $url_basename = $this->export_Wp_Page_To_Static_Html_Admin->url_to_basename($image);
                                $url_basename = $this->export_Wp_Page_To_Static_Html_Admin->filter_filename($url_basename);
                                $item_url = url_to_absolute($url, $image);
                                $my_file = "";


                                if (strpos($item_url, $host) !== false) {
                                    $fontExt = array("eot", "woff", "woff2", "ttf", "otf");
                                    $urlExt = pathinfo($url_basename, PATHINFO_EXTENSION);
                                    if (in_array($urlExt, $fontExt)) {
                                        $my_file = $pathname_fonts . $url_basename;
                                        $data = str_replace($image, $path_to_dot . 'fonts/' . $url_basename, $data);
                                    }

                                    $urlExt = pathinfo($item_url, PATHINFO_EXTENSION);
                                    if (in_array($urlExt, $this->export_Wp_Page_To_Static_Html_Admin->getImageExtensions())) {
                                        $my_file = $pathname_images . $url_basename;
                                        $data = str_replace($image, $path_to_dot . 'images/' . $url_basename, $data);

                                    }

                                    if (strpos($item_url, 'css') !== false) {
                                        $my_file = $pathname_css . $url_basename;
                                        $data = str_replace($image, $path_to_dot . 'css/' . $url_basename, $data);
                                    }

                                    if(!$saveAllAssetsToSpecificDir){
                                        $middle_p = $this->export_Wp_Page_To_Static_Html_Admin->rc_get_url_middle_path_for_assets($item_url);
                                        if(!file_exists($exportTempDir .'/'. $middle_p)){
                                            @mkdir($exportTempDir .'/'. $middle_p, 0777, true);
                                        }
                                        $my_file = $exportTempDir .'/'. $middle_p .'/'. $url_basename;
                                    }

                                    if (!empty($my_file)&&!file_exists($my_file)) {

                                        $handle = @fopen($my_file, 'w') or die('Cannot open file:  ' . $my_file);

                                        if ($this->export_Wp_Page_To_Static_Html_Admin->update_export_log($item_url)) {
                                            $item_data = $this->export_Wp_Page_To_Static_Html_Admin->get_url_data($item_url);
                                        }
                                        @fwrite($handle, $item_data);
                                        @fclose($handle);

                                        $this->export_Wp_Page_To_Static_Html_Admin->update_urls_log($image, $url_basename, 'new_file_name');
                                        $this->export_Wp_Page_To_Static_Html_Admin->update_urls_log($image, 1);

                                    }
                                }
                            }
                        }
                    }

                    $div->style = $data;
                }
            }

            $this->export_Wp_Page_To_Static_Html_Admin->site_data = $src;
        }
        return true;
    }
}