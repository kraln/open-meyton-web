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

function competitionPreview($folder, $xmlpaths) {   
  foreach ($xmlpaths as $xmlpath)
  {
    $xml = simplexml_load_file($xmlpath);
    $preview['disciplines'][] =  (string)($xml->ResultRecord->Discipline->Name);
    $preview['discipline_ids'][] = (string)($xml->ResultRecord->Discipline->ID);
    $preview['date'] = new DateTime($xml->ResultRecord->Aimings->AimingData->Shot->TimeStamp->DateTime);
  }
  
  $preview['disciplines'] = array_values(array_unique($preview['disciplines']));
  $preview['discipline_ids'] = array_values(array_unique($preview['discipline_ids']));
  $base_path = realpath($GLOBALS['BASE_DIR']);
  $preview['path'] = $GLOBALS['BASE_DIR'] . substr($folder, strlen($base_path));
  $preview['name'] = ucwords(dirname(substr($folder, strlen($base_path) + 1)));
  
  return $preview;
}

/* get all leaf directories */
$leafDirectories = getLeafDirectories($BASE_DIR);

/* go through each XML in the directory to understand which (virtual) sub-directories
 * there are, based on which disciplines are present. For the main list, we only surface
 * the list. */

/* grab all XMLs */
for ($i = 0; $i < count($leafDirectories); ++$i) {
  $glob = glob($leafDirectories[$i] . "/*.xml");
  if ($glob != false)
  {
    $comp_list[] = competitionPreview($leafDirectories[$i], $glob);
  }
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
