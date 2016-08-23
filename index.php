<?php

  $path_initial = 'app/toto';
  $limit = 0;

  $array_exclusion = array(
    'etc', 'css', 'js', '.', '..', '.DS_Store'
  );

  $array_extension = array(
    'php', 'phtml'
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
            preg_match_all( '/.*__\((.*)\).*/', $file_content, $result );

            $GLOBALS['need_to_trad'][] = $result[1];

          } else {
            continue;
          }
      }
    }
  }

  scanRecusif( $path_initial, $array_exclusion, $array_extension, $need_to_trad );


  echo "end";

  echo "<pre>";
  print_r( $need_to_trad );
  echo "</pre>";




?>
