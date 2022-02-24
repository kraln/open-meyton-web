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

$target_path = $_GET['target_path'];

/* check to ensure that the target path is a subdirectory of the BASE_DIR */ 
$base_path = realpath($BASE_DIR);
$target_path = realpath($target_path);

/* XXX: is this enough sanitization? */
if (strlen($target_path) < strlen($base_path) || (!strpos($target_path, $base_path) === 0))
{
  die();
} 

$xml = simplexml_load_file($target_path);

$endtime = microtime(true);
$debug_info['processing_time'] = $endtime - $starttime;
$output['record'] = (array)$xml->ResultRecord;
$output['debug'] = $debug_info;

header('Content-Type: application/json; charset=utf-8');
echo(json_encode($output));
