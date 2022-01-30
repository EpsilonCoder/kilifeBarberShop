<?php

namespace ExportHtmlAdmin\extract_meta_images;
class extract_meta_images
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
    public function get_meta_images($url="")
    {
        $src = $this->export_Wp_Page_To_Static_Html_Admin->site_data;
        $path_to_dot = $this->export_Wp_Page_To_Static_Html_Admin->rc_path_to_dot($url, true, true);
        $saveAllAssetsToSpecificDir = $this->export_Wp_Page_To_Static_Html_Admin->getSaveAllAssetsToSpecificDir();
        //preg_match_all("/(?<=\<img).*?(?=\/\>)/",$src,$matches_images);

        /*Extract shortcut icons*/
        foreach ($src->find('link') as $img) {
            if(isset($img->rel) && ($img->rel == "shortcut icon" || $img->rel == "icon" ) && isset($img->href) && !empty($img->href)){
                if (strpos($img->href, 'data:') == false && strpos($img->href, 'svg+xml') == false && strpos($img->href, 'base64') == false) {
                    $img_src = html_entity_decode($img->href, ENT_QUOTES);
                    $img_src = $this->export_Wp_Page_To_Static_Html_Admin->ltrim_and_rtrim($img_src);
                    $src_link = url_to_absolute($url, $img_src);

                    $imgExts = $this->export_Wp_Page_To_Static_Html_Admin->getImageExtensions();
                    $urlExt = pathinfo($src_link, PATHINFO_EXTENSION);

                    $exclude_url = apply_filters('wp_page_to_html_exclude_urls', false, $src_link);
                    if (in_array($urlExt, $imgExts) && !$exclude_url) {
                        $this->save_images($src_link, $url);
                        $basename = $this->export_Wp_Page_To_Static_Html_Admin->url_to_basename($src_link);
                        $basename = $this->export_Wp_Page_To_Static_Html_Admin->filter_filename($basename);

                        if(!$saveAllAssetsToSpecificDir){
                            $middle_p = $this->export_Wp_Page_To_Static_Html_Admin->rc_get_url_middle_path_for_assets($src_link);
                            $img->setAttribute('href', $path_to_dot . $middle_p . $basename);
                        }
                        else {
                            $img->setAttribute('href', $path_to_dot . 'images/' . $basename);
                        }
                    }
                }
            }
        }

        /*Extract meta images*/
        foreach ($src->find('meta') as $img) {
            if(isset($img->name) && $img->name == "thumbnail" && isset($img->content) && !empty($img->content)){
                if (strpos($img->content, 'data:') == false && strpos($img->content, 'svg+xml') == false && strpos($img->content, 'base64') == false) {
                    $src_link = html_entity_decode($img->content, ENT_QUOTES);
                    $src_link = $this->export_Wp_Page_To_Static_Html_Admin->ltrim_and_rtrim($src_link);
                    $src_link = url_to_absolute($url, $src_link);

                    $imgExts = $this->export_Wp_Page_To_Static_Html_Admin->getImageExtensions();
                    $urlExt = pathinfo($src_link, PATHINFO_EXTENSION);

                    if (in_array($urlExt, $imgExts)) {
                        $this->save_images($src_link, $url);
                        $basename = $this->export_Wp_Page_To_Static_Html_Admin->url_to_basename($src_link);
                        $basename = $this->export_Wp_Page_To_Static_Html_Admin->filter_filename($basename);

                        if(!$saveAllAssetsToSpecificDir){
                            $middle_p = $this->export_Wp_Page_To_Static_Html_Admin->rc_get_url_middle_path_for_assets($src_link);
                            $img->setAttribute('content', $path_to_dot . $middle_p . $basename);
                        }
                        else {
                            $img->setAttribute('content', $path_to_dot . 'images/' . $basename);
                        }
                    }
                }
            }
        }

        /*Extract og images*/
        foreach ($src->find('meta') as $img) {
            if(isset($img->property) && $img->property == "og:image" && isset($img->content) && !empty($img->content)){
                if (strpos($img->content, 'data:') == false && strpos($img->content, 'svg+xml') == false && strpos($img->content, 'base64') == false) {
                    $src_link = html_entity_decode($img->content, ENT_QUOTES);
                    $src_link = $this->export_Wp_Page_To_Static_Html_Admin->ltrim_and_rtrim($src_link);
                    $src_link = url_to_absolute($url, $src_link);

                    $imgExts = $this->export_Wp_Page_To_Static_Html_Admin->getImageExtensions();
                    $urlExt = pathinfo($src_link, PATHINFO_EXTENSION);
                    if (in_array($urlExt, $imgExts)) {
                        $this->save_images($src_link, $url);
                        $basename = $this->export_Wp_Page_To_Static_Html_Admin->url_to_basename($src_link);
                        $basename = $this->export_Wp_Page_To_Static_Html_Admin->filter_filename($basename);

                        if(!$saveAllAssetsToSpecificDir){
                            $middle_p = $this->export_Wp_Page_To_Static_Html_Admin->rc_get_url_middle_path_for_assets($src_link);
                            $img->setAttribute('content', $path_to_dot . $middle_p . $basename);
                        }
                        else {
                            $img->setAttribute('content', $path_to_dot . 'images/' . $basename);
                        }
                    }
                }
            }
        }

        $this->export_Wp_Page_To_Static_Html_Admin->site_data = $src;

        return true;
    }

    public function save_images($img_src = "", $found_on = "")
    {
//        $img_src = html_entity_decode($img_src, ENT_QUOTES);
//        $img_src = $this->export_Wp_Page_To_Static_Html_Admin->ltrim_and_rtrim($img_src);
//        $img_src = url_to_absolute($found_on, $img_src);
        $pathname_images = $this->export_Wp_Page_To_Static_Html_Admin->getImgPath();
        $saveAllAssetsToSpecificDir = $this->export_Wp_Page_To_Static_Html_Admin->getSaveAllAssetsToSpecificDir();
        $exportTempDir = $this->export_Wp_Page_To_Static_Html_Admin->getExportTempDir();



        $basename = $this->export_Wp_Page_To_Static_Html_Admin->url_to_basename($img_src);
        $basename = $this->export_Wp_Page_To_Static_Html_Admin->filter_filename($basename);

        if (!(strpos($img_src, 'data:') !== false)) {
            $this->export_Wp_Page_To_Static_Html_Admin->add_urls_log($img_src, $found_on, 'image5');
            $data = $this->export_Wp_Page_To_Static_Html_Admin->get_url_data($img_src);

            if (strpos($basename, ".") == false) {
                $basename = rand(5000, 9999) . ".jpg";
            }
            $basename = $this->export_Wp_Page_To_Static_Html_Admin->filter_filename($basename);

            $my_file = $pathname_images . $basename;

            if(!$saveAllAssetsToSpecificDir){
                $middle_p = $this->export_Wp_Page_To_Static_Html_Admin->rc_get_url_middle_path_for_assets($img_src);
                if(!file_exists($exportTempDir .'/'. $middle_p)){
                    @mkdir($exportTempDir .'/'. $middle_p, 0777, true);
                }
                $my_file = $exportTempDir .'/'. $middle_p .'/'. $basename;
            }

            if (!file_exists($my_file)) {
                $handle = @fopen($my_file, 'w') or die('Cannot open file:  ' . $my_file);

                @fwrite($handle, $data);
                fclose($handle);
            }

        }
    }
}