<?php

namespace ExportHtmlAdmin\extract_stylesheets;
class extract_stylesheets
{

    private  $export_Wp_Page_To_Static_Html_Admin;

    public function __construct($export_Wp_Page_To_Static_Html_Admin)
    {
        $this->export_Wp_Page_To_Static_Html_Admin = $export_Wp_Page_To_Static_Html_Admin;
    }

    /**
     * @since 2.0.0
     * @param string $url
     * @return array
     */
    public function get_stylesheets($url="")
    {
        $saveAllAssetsToSpecificDir = $this->export_Wp_Page_To_Static_Html_Admin->getSaveAllAssetsToSpecificDir();
        $src = $this->export_Wp_Page_To_Static_Html_Admin->getSiteData();
        $path_to_dot = $this->export_Wp_Page_To_Static_Html_Admin->rc_path_to_dot($url, true, true);
        //preg_match_all("/(?<=\<link rel='stylesheet|\<link rel=\"stylesheet).*?(?=\>)/",$src,$matches);
        $cssLinks = $src->find('link');

        if(!empty($cssLinks)){
            foreach ($cssLinks as $key => $link) {
                if(isset($link->href) && !empty($link->href) ){
                    $href_link = $link->href;
                    $href_link = html_entity_decode($href_link, ENT_QUOTES);
                    $href_link = $this->export_Wp_Page_To_Static_Html_Admin->ltrim_and_rtrim($href_link);

                    $href_link = url_to_absolute($url, $href_link);
                    $host = $this->export_Wp_Page_To_Static_Html_Admin->get_host($href_link);
                    $exclude_url = apply_filters('wp_page_to_html_exclude_urls', false, $href_link);
                    if( !empty($host) && strpos($href_link, '.css')!==false && strpos($url, $host)!==false && !$exclude_url){

                        if(!$this->export_Wp_Page_To_Static_Html_Admin->is_link_exists($href_link)){
                            $this->export_Wp_Page_To_Static_Html_Admin->add_urls_log($href_link, $url, 'css');
                            $newlyCreatedBasename = $this->save_stylesheets($href_link, $url);
                            if($newlyCreatedBasename !==false){
                                if(!$saveAllAssetsToSpecificDir){
                                    $middle_p = $this->export_Wp_Page_To_Static_Html_Admin->rc_get_url_middle_path_for_assets($href_link);
                                    $link->href = $path_to_dot . $middle_p . $newlyCreatedBasename;
                                }
                                else{
                                    $link->href = $path_to_dot .'css/'. $newlyCreatedBasename;
                                }
                            }
                        }
                        else{
                            $middle_pathname = $this->export_Wp_Page_To_Static_Html_Admin->middle_path_for_filename($href_link);
                            $basename = $this->export_Wp_Page_To_Static_Html_Admin->url_to_basename($href_link);
                            $basename = $this->export_Wp_Page_To_Static_Html_Admin->filter_filename($basename);

                            if(!$saveAllAssetsToSpecificDir){
                                $middle_p = $this->export_Wp_Page_To_Static_Html_Admin->rc_get_url_middle_path_for_assets($href_link);
                                $link->href = $path_to_dot . $middle_p . $basename;
                            }
                            else {
                                $link->href = $path_to_dot . 'css/' . $middle_pathname . $basename;
                            }
                        }

                    }
                }
            }
            $this->export_Wp_Page_To_Static_Html_Admin->site_data = $src;
        }

    }

    /**
     * @since 2.0.0
     * @param string $stylesheet_url
     * @param string $found_on
     * @return false|string
     */
    public function save_stylesheets($stylesheet_url = "", $found_on = "")
    {
        $pathname_fonts = $this->export_Wp_Page_To_Static_Html_Admin->getFontsPath();
        $pathname_css = $this->export_Wp_Page_To_Static_Html_Admin->getCssPath();
        $pathname_images = $this->export_Wp_Page_To_Static_Html_Admin->getImgPath();
        $host = $this->export_Wp_Page_To_Static_Html_Admin->get_host($found_on);
        $saveAllAssetsToSpecificDir = $this->export_Wp_Page_To_Static_Html_Admin->getSaveAllAssetsToSpecificDir();
        $exportTempDir = $this->export_Wp_Page_To_Static_Html_Admin->getExportTempDir();

        //$stylesheet_url = url_to_absolute($found_on, $stylesheet_url);
        $m_basename = $this->export_Wp_Page_To_Static_Html_Admin->middle_path_for_filename($stylesheet_url);


        if (!$this->export_Wp_Page_To_Static_Html_Admin->rc_is_link_already_generated($stylesheet_url)) {
            if ($this->export_Wp_Page_To_Static_Html_Admin->update_export_log($stylesheet_url, 'copying', '')) {
                $data = $this->export_Wp_Page_To_Static_Html_Admin->get_url_data($stylesheet_url);


                preg_match_all("/(?<=url\().*?(?=\))/", $data, $images_links);

                foreach ($images_links as $key => $images) {
                    foreach ($images as $key => $image) {
                        $image = $this->export_Wp_Page_To_Static_Html_Admin->ltrim_and_rtrim($image);
                        if (strpos($image, 'data:') == false && strpos($image, 'data:image/') == false && strpos($image, 'image/svg') == false && strpos($image, 'base64') == false) {
                            $image = html_entity_decode($image, ENT_QUOTES);
                            $image = $this->export_Wp_Page_To_Static_Html_Admin->ltrim_and_rtrim($image);
                            $newImageUrl = url_to_absolute($stylesheet_url, $image);
                            $this->export_Wp_Page_To_Static_Html_Admin->add_urls_log($image, $stylesheet_url, 'cssFile');
                            $item_url = $newImageUrl;
                            $url_basename = $this->export_Wp_Page_To_Static_Html_Admin->url_to_basename($item_url);
                            $url_basename = $this->export_Wp_Page_To_Static_Html_Admin->filter_filename($url_basename);
                            if (strpos($item_url, $host)!==false) {
                                $fontExt = array("eot", "woff", "woff2", "ttf", "otf");
                                $urlExt = pathinfo($url_basename, PATHINFO_EXTENSION);
                                if (in_array($urlExt, $fontExt)) {
                                    $my_file = $pathname_fonts . $url_basename;
                                    $data = str_replace($image, '../fonts/' . $url_basename, $data);
                                }

                                $urlExt = pathinfo($url_basename, PATHINFO_EXTENSION);
                                if (in_array($urlExt, $this->export_Wp_Page_To_Static_Html_Admin->getImageExtensions())) {
                                    $my_file = $pathname_images . $url_basename;
                                    $data = str_replace($image, '../images/' . $url_basename, $data);

                                }

                                if (strpos($item_url, 'css') !== false) {
                                    $my_file = $pathname_css . $url_basename;
                                    $data = str_replace($image, '../css/' . $url_basename, $data);
                                }

                                if(!$saveAllAssetsToSpecificDir){
                                    $middle_p = $this->export_Wp_Page_To_Static_Html_Admin->rc_get_url_middle_path_for_assets($newImageUrl);
                                    if(!file_exists($exportTempDir .'/'. $middle_p)){
                                        @mkdir($exportTempDir .'/'. $middle_p, 0777, true);
                                    }
                                    $my_file = $exportTempDir .'/'. $middle_p .'/'. $url_basename;
                                }

                                if (!file_exists($my_file)) {

                                    $handle = fopen($my_file, 'w') or die('Cannot open file:  ' . $my_file);

                                    if ($this->export_Wp_Page_To_Static_Html_Admin->update_export_log($item_url)) {
                                        $item_data = $this->export_Wp_Page_To_Static_Html_Admin->get_url_data($item_url);

                                        fwrite($handle, $item_data);
                                        fclose($handle);

                                        $this->export_Wp_Page_To_Static_Html_Admin->update_urls_log($image, $url_basename, 'new_file_name', false, $item_url);
                                        $this->export_Wp_Page_To_Static_Html_Admin->update_urls_log($image, 1);
                                    }

                                }
                                else{
                                    $this->export_Wp_Page_To_Static_Html_Admin->update_urls_log($image, $url_basename, 'new_file_name', false, $item_url);
                                    $this->export_Wp_Page_To_Static_Html_Admin->update_urls_log($image, 1);
                                }
                            }
                        }
                    }
                }


                $basename = $this->export_Wp_Page_To_Static_Html_Admin->url_to_basename($stylesheet_url);

                if (strpos($basename, ".css") == false) {
                    $basename = rand(5000, 9999) . ".css";
                }
                $basename = $this->export_Wp_Page_To_Static_Html_Admin->filter_filename($basename);

                if (!empty($m_basename)) {
                    $my_file = $pathname_css . $m_basename . $basename;
                } else {
                    $my_file = $pathname_css . $basename;
                }

                if(!$saveAllAssetsToSpecificDir){
                    $middle_p = $this->export_Wp_Page_To_Static_Html_Admin->rc_get_url_middle_path_for_assets($stylesheet_url);
                    if(!file_exists($exportTempDir .'/'. $middle_p)){
                        @mkdir($exportTempDir .'/'. $middle_p, 0777, true);
                    }
                    $my_file = $exportTempDir .'/'. $middle_p .'/'. $basename;
                }

                if (!file_exists($my_file)) {
                    $handle = @fopen($my_file, 'w') or die('Cannot open file:  ' . $my_file);
                    $data = $data . "\n/*This file was exported by \"Export WP Page to Static HTML\" plugin which created by ReCorp (https://myrecorp.com) */";
                    @fwrite($handle, $data);


                    fclose($handle);
                }

                if (!empty($m_basename)&&$saveAllAssetsToSpecificDir) {
                    $basename_ = $m_basename . $basename;
                } else {
                    $basename_ = $basename;
                }
                $this->export_Wp_Page_To_Static_Html_Admin->update_urls_log($stylesheet_url, $basename_, 'new_file_name');
                $this->export_Wp_Page_To_Static_Html_Admin->update_urls_log($stylesheet_url, 1);

                //}
                return $basename_;

            }
        }
        return false;
    }

}
