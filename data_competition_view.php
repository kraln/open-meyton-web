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
  * data competition view
  * 
  * given a directory, show all competitors sorted by score (descending)
  *
  * return format is a JSON object
  *
  */
$starttime = microtime(true);

include('config.php');

$target_path = $_GET['competition_path'];

/* check to ensure that the target path is a subdirectory of the BASE_DIR */ 
$base_path = realpath($BASE_DIR);
$target_path = realpath($target_path);

/* XXX: is this enough sanitization? */
if (strlen($target_path) < strlen($base_path) || (!strpos($target_path, $base_path) === 0))
{
  die();
} 

$glob = glob($target_path . "/*.xml");
if ($glob == false)
{
  die();
}

function parseXML($xmlpath)
{
  $xml = simplexml_load_file($xmlpath);

  $result['shooter'] = (array)$xml->ResultRecord->Shooter;
  $result['meta']['lane'] = (string)$xml->ResultRecord->Attributes()['LaneNo'];
  $result['meta']['startid'] = (string)$xml->ResultRecord->Attributes()['StartId'];
  $result['matchclass'] = (string)$xml->ResultRecord->MatchClass->Name;
  $result['matchclassID'] = (string)$xml->ResultRecord->MatchClass->ID;
  $result['club'] = (string)$xml->ResultRecord->Club->Name;
  $result['federation'] = (string)$xml->ResultRecord->Federation->Name;
  $result['team'] = (string)$xml->ResultRecord->Team->Name;
  $result['discipline'] = (string)$xml->ResultRecord->Discipline->Name;
  $result['disciplineID'] = (string)$xml->ResultRecord->Discipline->ID;
  $result['result'] = (string)$xml->ResultRecord->Total->Result;
  $result['shotcount'] = (string)$xml->ResultRecord->ShotNoTotal;
  $result['date'] = new DateTime($xml->ResultRecord->Aimings->AimingData->Shot->TimeStamp->DateTime);
  foreach($xml->ResultRecord->Aimings->AimingData->Children() as $el)
  {
    if($el->getName() == "Series")
    {
      $series['id'] = (integer)$el->Attributes()['SeriesID'];
      $series['result'] = (array)$el->ValueSerie;
      $result['series'][] = $series;
    }
  }

  return $result;
}

foreach ($glob as $file)
{
  $result['path'] = $BASE_DIR . substr($file, strlen($base_path)); 
  $result['data'] = parseXML($file);
  $results[] = $result;
}

$endtime = microtime(true);
$debug_info['processing_time'] = $endtime - $starttime;
$output['results'] = $results;
$output['debug'] = $debug_info;
$output['meta']['date'] = $results[0]['data']['date'];
$output['meta']['discipline'] = $results[0]['data']['discipline'];
header('Content-Type: application/json; charset=utf-8');
echo(json_encode($output));
