<?php

namespace ExportHtmlAdmin\extract_videos;

class extract_videos
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
    public function get_videos($url="")
    {
        $src = $this->export_Wp_Page_To_Static_Html_Admin->site_data;
        $videoLinks = $src->find('source');
        $path_to_dot = $this->export_Wp_Page_To_Static_Html_Admin->rc_path_to_dot($url, true, true);
        $saveAllAssetsToSpecificDir = $this->export_Wp_Page_To_Static_Html_Admin->getSaveAllAssetsToSpecificDir();

        if (!empty($videoLinks)) {
            $videos_path = $this->export_Wp_Page_To_Static_Html_Admin->getVideosPath();
            if (!file_exists($videos_path)) {
                @mkdir($videos_path);
            }

            foreach ($videoLinks as $link) {
                if (isset($link->src) && !empty($link->src)) {
                    $src_link = $link->src;
                    $src_link = html_entity_decode($src_link, ENT_QUOTES);
                    $src_link = $this->export_Wp_Page_To_Static_Html_Admin->ltrim_and_rtrim($src_link);

                    $src_link = url_to_absolute($url, $src_link);
                    $host = $this->export_Wp_Page_To_Static_Html_Admin->get_host($src_link);

                    $videoExts = $this->export_Wp_Page_To_Static_Html_Admin->getVideoExtensions();
                    $videoBasename = $this->export_Wp_Page_To_Static_Html_Admin->url_to_basename($src_link);
                    $videoBasename = $this->export_Wp_Page_To_Static_Html_Admin->filter_filename($videoBasename);
                    $urlExt = pathinfo($videoBasename, PATHINFO_EXTENSION);
                    $exclude_url = apply_filters('wp_page_to_html_exclude_urls', false, $src_link);

                    if (!empty($host) && isset($src_link) && in_array($urlExt, $videoExts) && strpos($url, $host) !== false && !$this->export_Wp_Page_To_Static_Html_Admin->is_link_exists($src_link) && !$exclude_url) {
                        $this->export_Wp_Page_To_Static_Html_Admin->add_urls_log($src_link, $url, 'js');
                        $this->save_videos($src_link, $url);

                        $newlyCreatedBasename = $this->save_videos($src_link, $url);
                        if ($newlyCreatedBasename !== false) {
                            $link->src = $path_to_dot . 'videos/' . $newlyCreatedBasename;

                            if(!$saveAllAssetsToSpecificDir){
                                $middle_p = $this->export_Wp_Page_To_Static_Html_Admin->rc_get_url_middle_path_for_assets($src_link);
                                $link->src = $path_to_dot . $middle_p . $newlyCreatedBasename;
                            }
                            else {
                                $link->src = $path_to_dot .'js/'. $newlyCreatedBasename;
                            }
                        }
                    }
                }
            }
            $this->export_Wp_Page_To_Static_Html_Admin->site_data = $src;
        }


    }

    public function save_videos($video_url_prev = "", $found_on = "")
    {
        $video_url = $video_url_prev;
        $videos_path = $this->export_Wp_Page_To_Static_Html_Admin->getVideosPath();
        $video_url = url_to_absolute($found_on, $video_url);
        $m_basename = $this->export_Wp_Page_To_Static_Html_Admin->middle_path_for_filename($video_url);
        $saveAllAssetsToSpecificDir = $this->export_Wp_Page_To_Static_Html_Admin->getSaveAllAssetsToSpecificDir();
        $exportTempDir = $this->export_Wp_Page_To_Static_Html_Admin->getExportTempDir();

        if (!$this->export_Wp_Page_To_Static_Html_Admin->is_link_exists($video_url) && $this->export_Wp_Page_To_Static_Html_Admin->update_export_log($video_url)) {
            $data = $this->export_Wp_Page_To_Static_Html_Admin->get_url_data($video_url);

            $basename = $this->export_Wp_Page_To_Static_Html_Admin->url_to_basename($video_url);

            if (!(strpos($basename, ".") !== false)) {
                $basename = rand(5000, 9999) . ".mp4";
            }
            $basename = $this->export_Wp_Page_To_Static_Html_Admin->filter_filename($basename);

            $my_file = $videos_path . $m_basename . $basename;
            if(!$saveAllAssetsToSpecificDir){
                $middle_p = $this->export_Wp_Page_To_Static_Html_Admin->rc_get_url_middle_path_for_assets($video_url);
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

                $this->export_Wp_Page_To_Static_Html_Admin->update_urls_log($video_url_prev, $basename, 'new_file_name');
                $this->export_Wp_Page_To_Static_Html_Admin->update_urls_log($video_url_prev, 1);

            }

            if(!empty($m_basename)){
                return $m_basename . $basename;
            }
            return $basename;

        }
        return false;
    }
}