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


/* comment to show you've read/configured this application */
die("Read config.php");

/**
***
*** END CUSTOMIZATION
***
**/

$site_config['site_lang'] = $SITE_LANG;
$site_config['site_title'] = $SITE_TITLE;
$SITE_CONFIG = json_encode($site_config);
