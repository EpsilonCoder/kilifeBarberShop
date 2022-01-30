<?php

namespace ExportHtmlAdmin\extract_images;
class extract_images
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
    public function get_images($url="")
    {
        $src = $this->export_Wp_Page_To_Static_Html_Admin->site_data;
        $path_to_dot = $this->export_Wp_Page_To_Static_Html_Admin->rc_path_to_dot($url, true, true);
        $saveAllAssetsToSpecificDir = $this->export_Wp_Page_To_Static_Html_Admin->getSaveAllAssetsToSpecificDir();

        $images = array();
        foreach ($src->find('img') as $img) {
            if (strpos($img->src, 'data:') == false && strpos($img->src, 'svg+xml') == false && strpos($img->src, 'base64') == false) {
                $img_src = html_entity_decode($img->src, ENT_QUOTES);
                $img_src = $this->export_Wp_Page_To_Static_Html_Admin->ltrim_and_rtrim($img_src);
                $img_src = url_to_absolute($url, $img_src);

                $imgExts = array("gif", "jpg", "jpeg", "png", "tiff", "tif");
                $urlExt = pathinfo($img_src, PATHINFO_EXTENSION);

                $exclude_url = apply_filters('wp_page_to_html_exclude_urls', false, $img_src);

                if (in_array($urlExt, $imgExts) && !$exclude_url) {
                    $this->save_image($img_src, $url);
                    $basename = $this->export_Wp_Page_To_Static_Html_Admin->url_to_basename($img_src);
                    $basename = $this->export_Wp_Page_To_Static_Html_Admin->filter_filename($basename);

                    if(!$saveAllAssetsToSpecificDir){
                        $middle_p = $this->export_Wp_Page_To_Static_Html_Admin->rc_get_url_middle_path_for_assets($img_src);
                        $img->setAttribute('src', $path_to_dot . $middle_p . $basename);
                    }
                    else {
                        $img->setAttribute('src', $path_to_dot.'images/'.$basename);
                    }

                    $images[] = $img->src;
                }
            }

            if (isset($img->attr['data-lazyload']) && strpos($img->attr['data-lazyload'], 'data:') == false && strpos($img->attr['data-lazyload'], 'svg+xml') == false && strpos($img->attr['data-lazyload'], 'base64') == false) {
                $imgSrc = $img->attr['data-lazyload'];

                $img_src = html_entity_decode($imgSrc, ENT_QUOTES);
                $img_src = $this->export_Wp_Page_To_Static_Html_Admin->ltrim_and_rtrim($img_src);
                $imgSrc = url_to_absolute($url, $img_src);

                $imgExts = array("gif", "jpg", "jpeg", "png", "tiff", "tif");
                $urlExt = pathinfo($imgSrc, PATHINFO_EXTENSION);

                $exclude_url = apply_filters('wp_page_to_html_exclude_urls', false, $imgSrc);

                if (in_array($urlExt, $imgExts) && !$exclude_url) {
                    $this->save_image($imgSrc, $url);
                    $basename = $this->export_Wp_Page_To_Static_Html_Admin->url_to_basename($imgSrc);
                    $basename = $this->export_Wp_Page_To_Static_Html_Admin->filter_filename($basename);

                    if(!$saveAllAssetsToSpecificDir){
                        $middle_p = $this->export_Wp_Page_To_Static_Html_Admin->rc_get_url_middle_path_for_assets($img->src);
                        $img->setAttribute('data-lazyload', $path_to_dot . $middle_p . $basename);
                    }
                    else {
                        $img->setAttribute('data-lazyload', $path_to_dot.'images/'.$basename);
                    }
                    $images[] = $imgSrc;
                }
            }

            if (isset($img->srcset)){
                $srcset = $img->srcset;
                $srcset = explode(' ', $srcset);

                $imgFind = array();
                $imgReplace = array();
                foreach ($srcset as $key => $item) {
                    $img_src = html_entity_decode($item, ENT_QUOTES);
                    $img_src = $this->export_Wp_Page_To_Static_Html_Admin->ltrim_and_rtrim($img_src);
                    $item = url_to_absolute($url, $img_src);

                    $imgExts = array("gif", "jpg", "jpeg", "png", "tiff", "tif");
                    $urlExt = pathinfo($item, PATHINFO_EXTENSION);
                    //echo $urlExt;
                    if (in_array($urlExt, $imgExts)) {
                        $this->save_image($item, $url);
                        $basename = $this->export_Wp_Page_To_Static_Html_Admin->url_to_basename($item);
                        $basename = $this->export_Wp_Page_To_Static_Html_Admin->filter_filename($basename);
                        $imgFind[] = $item;

                        if(!$saveAllAssetsToSpecificDir){
                            $middle_p = $this->export_Wp_Page_To_Static_Html_Admin->rc_get_url_middle_path_for_assets($item);
                            $imgReplace[] = $path_to_dot. $middle_p . $basename;
                        }
                        else {
                            $imgReplace[] = $path_to_dot.'images/'.$basename;
                        }

                        $images[] = $item;
                    }
                }

                $img->setAttribute('srcset', str_replace($imgFind, $imgReplace, $img->srcset));
            }

        }
        $this->export_Wp_Page_To_Static_Html_Admin->site_data = $src;


        return $images;
    }

    public function save_image($img_src = "", $found_on = "")
    {
        $scheme = $this->export_Wp_Page_To_Static_Html_Admin->get_site_scheme($found_on);
        $host = $this->export_Wp_Page_To_Static_Html_Admin->get_host($found_on);
        $pathname_images = $this->export_Wp_Page_To_Static_Html_Admin->getImgPath();
        $saveAllAssetsToSpecificDir = $this->export_Wp_Page_To_Static_Html_Admin->getSaveAllAssetsToSpecificDir();
        $exportTempDir = $this->export_Wp_Page_To_Static_Html_Admin->getExportTempDir();



        if (strpos($img_src, 'data:') == false) {
            $img_src = html_entity_decode($img_src, ENT_QUOTES);
            $basename = $this->export_Wp_Page_To_Static_Html_Admin->url_to_basename($img_src);
            $prevImgSrc = $img_src;
            $img_src = url_to_absolute($found_on, $img_src);

            if (!$this->export_Wp_Page_To_Static_Html_Admin->is_link_exists($img_src)) {
                $this->export_Wp_Page_To_Static_Html_Admin->update_export_log($img_src);
                $this->export_Wp_Page_To_Static_Html_Admin->add_urls_log($img_src, $found_on, 'image');
                $data = $this->export_Wp_Page_To_Static_Html_Admin->get_url_data($img_src);

                if (strpos($basename, '.') == false) {
                    $basename = rand(5000, 9999) . ".jpg";
                }
                $basename = $this->export_Wp_Page_To_Static_Html_Admin->filter_filename($basename);

                $img_path_src = $pathname_images . $basename;

                if(!$saveAllAssetsToSpecificDir){
                    $middle_p = $this->export_Wp_Page_To_Static_Html_Admin->rc_get_url_middle_path_for_assets($img_src);
                    if(!file_exists($exportTempDir .'/'. $middle_p)){
                        @mkdir($exportTempDir .'/'. $middle_p, 0777, true);
                    }
                    $img_path_src = $exportTempDir .'/'. $middle_p .'/'. $basename;
                }

                if (!file_exists($img_path_src)) {
                    $handle = @fopen($img_path_src, 'w') or die('Cannot open file:  ' . $img_path_src);

                    @fwrite($handle, $data);
                    fclose($handle);

                    //$this->export_Wp_Page_To_Static_Html_Admin->update_urls_log($img_src, 1);
                }
                $this->export_Wp_Page_To_Static_Html_Admin->update_urls_log($img_src, 1);
            }

        }

    }
}