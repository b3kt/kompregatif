<?php

namespace App\Helpers;

use TCG\Voyager\Models\Menu;
use TCG\Voyager\Events\MenuDisplay;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class DashboardHelper{

    public static function getDashboardUrl($code){
        $url = 'https://bigdata.pln.co.id/trusted';
        $server = 'https://bigdata.pln.co.id';
        $data = array('username' => 'dev.mka');
        $params = ':embed=yes&:toolbar=bottom';
        $views = "views/".str_replace("::","/", $code);

        $embed_template =
           "<script type='text/javascript' src='https://bigdata.pln.co.id/javascripts/api/viz_v1.js'></script>
            <div class='tableauPlaceholder' style='width: 1330px; height: 757px;'>
              <object class='tableauViz' width='1330' height='757' style='display:none;'>
                <param name='host_url' value='https%3A%2F%2Fbigdata.pln.co.id%2F' />
                <param name='embed_code_version' value='3' />
                <param name='site_root' value='' />
                <param name='name' value='SIMANISDev_DashboardDistribusi&#47;Dash2_2MapTiangTM' />
                <param name='tabs' value='no' />
                <param name='toolbar' value='yes' />
                <param name='showAppBanner' value='false' />
              </object>
            </div>";



        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            ),
            'ssl' => array(
                'verify_peer' => false,
                'verify_name' => false
            ),
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        if ($result === FALSE) { }

        $workbook = $url.'/'.$result.'/'.$views.'?'.$params;

        // return $workbook;

        // return $embed_template;

        return "<iframe src=\"$workbook\" width=\"100%\" height=\"800vm\" style=\"background: #FFFFFF;border:1px solid lightgrey;\"></iframe>";
    }


    public static function getValue($dataType, $code){

    }

}
