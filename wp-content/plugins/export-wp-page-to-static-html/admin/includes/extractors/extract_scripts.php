<?php

namespace ExportHtmlAdmin\extract_scripts;
class extract_scripts
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
    public function get_scripts($url="")
    {
        $saveAllAssetsToSpecificDir = $this->export_Wp_Page_To_Static_Html_Admin->getSaveAllAssetsToSpecificDir();
        $src = $this->export_Wp_Page_To_Static_Html_Admin->site_data;
        $jsLinks = $src->find('script');
        $path_to_dot = $this->export_Wp_Page_To_Static_Html_Admin->rc_path_to_dot($url, true, true);

        if (!empty($jsLinks)) {
            foreach ($jsLinks as $key => $link) {
                if (isset($link->src) && !empty($link->src)) {
                    $src_link = $link->src;
                    $src_link = html_entity_decode($src_link, ENT_QUOTES);
                    $src_link = $this->export_Wp_Page_To_Static_Html_Admin->ltrim_and_rtrim($src_link);
                    $src_link = url_to_absolute($url, $src_link);
                    $host = $this->export_Wp_Page_To_Static_Html_Admin->get_host($src_link);
                    $exclude_url = apply_filters('wp_page_to_html_exclude_urls', false, $src_link);

                    if (!empty($host) && strpos($src_link, '.js') !== false && strpos($url, $host) !== false && !$exclude_url) {

                        if(!$this->export_Wp_Page_To_Static_Html_Admin->is_link_exists($src_link)){
                            $this->export_Wp_Page_To_Static_Html_Admin->add_urls_log($src_link, $url, 'js');
                            $newlyCreatedBasename = $this->save_scripts($src_link, $url);
                            if($newlyCreatedBasename !==false){

                                if(!$saveAllAssetsToSpecificDir){
                                    $middle_p = $this->export_Wp_Page_To_Static_Html_Admin->rc_get_url_middle_path_for_assets($src_link);
                                    $link->src = $path_to_dot . $middle_p . $newlyCreatedBasename;
                                }
                                else {
                                    $link->src = $path_to_dot .'js/'. $newlyCreatedBasename;
                                }
                            }
                        }
                        else{
                            $middle_pathname = $this->export_Wp_Page_To_Static_Html_Admin->middle_path_for_filename($src_link);
                            $basename = $this->export_Wp_Page_To_Static_Html_Admin->url_to_basename($src_link);
                            $basename = $this->export_Wp_Page_To_Static_Html_Admin->filter_filename($basename);

                            if(!$saveAllAssetsToSpecificDir){
                                $middle_p = $this->export_Wp_Page_To_Static_Html_Admin->rc_get_url_middle_path_for_assets($src_link);
                                $link->src = $path_to_dot . $middle_p . $basename;
                            }
                            else {
                                $link->src = $path_to_dot .'js/'. $middle_pathname . $basename;
                            }
                        }

                    }
                }
            }

            $this->export_Wp_Page_To_Static_Html_Admin->site_data = $src;
        }

    }

    public function save_scripts($script_url_prev = "", $found_on = "")
    {
        $script_url = $script_url_prev;
        $pathname_js = $this->export_Wp_Page_To_Static_Html_Admin->getJsPath();
        $script_url = url_to_absolute($found_on, $script_url);
        $m_basename = $this->export_Wp_Page_To_Static_Html_Admin->middle_path_for_filename($script_url);
        $saveAllAssetsToSpecificDir = $this->export_Wp_Page_To_Static_Html_Admin->getSaveAllAssetsToSpecificDir();
        $exportTempDir = $this->export_Wp_Page_To_Static_Html_Admin->getExportTempDir();



        if ($this->export_Wp_Page_To_Static_Html_Admin->update_export_log($script_url_prev)) {
            $data = $this->export_Wp_Page_To_Static_Html_Admin->get_url_data($script_url);

            $basename = $this->export_Wp_Page_To_Static_Html_Admin->url_to_basename($script_url);

            if (!(strpos($basename, ".") !== false)) {
                $basename = rand(5000, 9999) . ".js";
            }
            $basename = $this->export_Wp_Page_To_Static_Html_Admin->filter_filename($basename);

            $my_file = $pathname_js . $m_basename . $basename;

            if(!$saveAllAssetsToSpecificDir){
                $middle_p = $this->export_Wp_Page_To_Static_Html_Admin->rc_get_url_middle_path_for_assets($script_url);
                if(!file_exists($exportTempDir .'/'. $middle_p)){
                    @mkdir($exportTempDir .'/'. $middle_p, 0777, true);
                }
                $my_file = $exportTempDir .'/'. $middle_p .'/'. $basename;
            }

            if (!file_exists($my_file)) {
                $handle = @fopen($my_file, 'w') or die('Cannot open file:  ' . $my_file);

                $data .= "\n/*This file was exported by \"Export WP Page to Static HTML\" plugin which created by ReCorp (https://myrecorp.com) */";
                @fwrite($handle, $data);
                fclose($handle);

                $this->export_Wp_Page_To_Static_Html_Admin->update_urls_log($script_url_prev, $basename, 'new_file_name');
                $this->export_Wp_Page_To_Static_Html_Admin->update_urls_log($script_url_prev, 1);

            }

            $this->export_Wp_Page_To_Static_Html_Admin->update_urls_log($script_url_prev, 1);
            if(!empty($m_basename)&&$saveAllAssetsToSpecificDir){
                return $m_basename . $basename;
            }
            $basename = $this->export_Wp_Page_To_Static_Html_Admin->filter_filename($basename);
            return $basename;

        }

        return false;
    }
}