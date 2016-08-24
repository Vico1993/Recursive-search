<?php

// Path Module
$path_initial = '/var/www/magento2/app/code/Girodmedical/Availablestock';

// Dev secure
$limit = 0;

$array_exclusion = array(
  'etc', 'css', 'js', '.', '..', '.DS_Store', 'i18n'
);

$array_extension = array(
  'php', 'phtml'
);

$aray_lang = array(
    'de_DE', 'en_GB', 'en_US', 'fr_FR', 'it_IT', 'es_ES', 'nl_NL', 'pl_PL', 'pt_PT', 'sk_SK', 'sl_SL'
);

$need_to_trad = array();

// folder with path
function scanRecusif ( $folder, $array_exclusion, $array_extension, $need_to_trad ) {
  $folder_content = scandir( $folder );
  foreach ( $folder_content as $key => $content ) {
    if ( is_dir( $folder.'/'.$content ) && !in_array( $content, $array_exclusion ) ) {
      scanRecusif( $folder.'/'.$content, $array_exclusion, $array_extension, $need_to_trad );
    } elseif ( is_file( $folder.'/'.$content ) ) {
        if ( in_array( pathinfo( $folder.'/'.$content, PATHINFO_EXTENSION), $array_extension ) ) {
            // Lis le fichier
            $file_content = file_get_contents( $folder.'/'.$content , FILE_USE_INCLUDE_PATH);

            if( preg_match_all( '/__\(([^_$]*)\)/', $file_content, $result ) ) {
                if ( is_array( $result[1] ) ) {
                    foreach ( $result[1] as $key => $value ) {
                        $clean_value = preg_replace( '/\'*|"*/', "", $value );
                        if ( !in_array( $clean_value, $GLOBALS['need_to_trad'] ) ) {
                            $GLOBALS['need_to_trad'][] = $clean_value; // array( $value, '' )
                        }
                    }
                }
            }
        } else {
          continue;
        }
    }
  }
}

scanRecusif( $path_initial, $array_exclusion, $array_extension, $need_to_trad );

// on vide les lignes vides du tableaux
$trad_clean = array_filter( $need_to_trad );

// Le dossier de lang
$lang_dir = $path_initial.'/i18n';
if ( !is_dir( $lang_dir ) ) {
    if ( !mkdir( $lang_dir ) ) {
        echo "ERROR folder not created";
        die();
    }
}

// creez les fichier CSV
foreach ( $aray_lang as $key => $lang ) {
    $file_path = $lang_dir.'/'.$lang.'.csv';
    $file = fopen( $file_path , "a+" );

    foreach ( $trad_clean as $fields ) {
        fputcsv( $file, array( $fields, '' ) );
    }

    fclose( $file );
}


echo "END ?";

echo "<pre>";
print_r( $trad_clean );
echo "</pre>";

?>
