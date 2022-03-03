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

/* Where the XML files are located */
$BASE_DIR = "./XML";

/* How the site is called */
$SITE_TITLE = "Open Meyton Webviewer";

/* Localization */
$SITE_LANG = "en";

/* Mapping of discipline IDs to target image variants (default is 0 / 25m pistol) */
$TARGET_DISCIPLINES = [
  "10110040" => "1", // LG 40
  "10210040" => "0", // LP 40
  "19901020" => "0", // LP K-Liga Auflage
  "10111030" => "1", // LG Auflage 30
  "19902030" => "1", // LG K-Liga
];

/* comment to show you've read/configured this application */
die("Read config.php");

/**
***
*** END CUSTOMIZATION
***
**/

$site_config['site_lang'] = $SITE_LANG;
$site_config['site_title'] = $SITE_TITLE;
$site_config['target_disciplines'] = $TARGET_DISCIPLINES;
$SITE_CONFIG = json_encode($site_config);
