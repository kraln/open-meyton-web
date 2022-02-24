<?php
 /*
  * Licensed under the Apache License, Version 2.0 (the "License");
  * you may not use this file except in compliance with the License.
  * 
  * You may obtain a copy of the License at
  * http://www.apache.org/licenses/LICENSE-2.0
  * 
  * Unless required by applicable law or agreed to in writing, software
  * distributed under the License is distributed on an "AS IS" BASIS,
  * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
  * See the License for the specific language governing permissions and
  * limitations under the License.
  */

 /*
  * data competition list
  * 
  * given a directory of directories of meyton XML files discover all available
  * directories, and gather some information about what the directory contains
  * based on the first available xml file
  *
  * return format is a JSON object
  *
  */
$starttime = microtime(true);

require 'config.php';

function getLeafDirectories($dir, &$results = array()) {
  if (!is_dir($dir))
  {
    return $results;
  }

  $files = scandir($dir);

  foreach ($files as $key => $value) {
    $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
    if ($value != "." && $value != "..") {
      getLeafDirectories($path, $results);
      $glob = glob($path . "/*");
      if ($glob != false)
      {
        if (!array_reduce($glob, function($carry, $item) { return ($carry || is_dir($item)); }))
        {
          $results[] = $path;
        }
      }
    }
  }
  return $results;
}

function XMLpreview($folder, $xmlpath) {
  /* quick and dirty for previews */
  $xml = simplexml_load_file($xmlpath);
  
  $preview['discipline'] = (string)($xml->ResultRecord->Discipline->Name);
  $preview['date'] = new DateTime($xml->ResultRecord->Aimings->AimingData->Shot->TimeStamp->DateTime);
  $base_path = realpath($GLOBALS['BASE_DIR']);
  $preview['path'] = $GLOBALS['BASE_DIR'] . substr($folder, strlen($base_path));
  
  return $preview;
}

/* get all leaf directories */
$leafDirectories = getLeafDirectories($BASE_DIR);

/* get the first xml in each directory */
$xmlFiles = array_map(function($el) { return scandir($el)[2]; }, $leafDirectories);

/* load the xml to understand what each directory is */
for ($i = 0; $i < count($leafDirectories); ++$i) {
  $comp_list[] = (XMLpreview($leafDirectories[$i], $leafDirectories[$i] . '/' .  $xmlFiles[$i]));
}

/* sort the competition list most recent first */
usort($comp_list, fn($a, $b) => -1*($a['date'] <=> $b['date'])); 

/* return json list of directories + information */
$output['competitions'] = $comp_list;
$endtime = microtime(true);
$debug_info['processing_time'] = $endtime - $starttime;
$output['debug'] = $debug_info;

header('Content-Type: application/json; charset=utf-8');
echo(json_encode($output));
