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
  * image_shooter_view
  *
  * draw a target, optionally with a numbered list of hits
  * the viewport will be adjusted to the extent of the hits, if present
  *
  */

header('Content-type: image/svg+xml');

if (!strlen($_SERVER['QUERY_STRING']))
{
  die();
}

parse_str($_SERVER['QUERY_STRING'], $points);

/* determine outer extents */
$max = 0;
$min = 0;

if (!array_key_exists('point', $points))
{
  die();
}

foreach($points['point'] as $idx => $ps)
{
  $point = explode(",", $ps);
  if ($point[0] > $max) { $max = $point[0]; }
  if ($point[1] > $max) { $max = $point[1]; }
  if ($point[0] < $min) { $min = $point[0]; }
  if ($point[1] < $min) { $min = $point[1]; }
}

$max_max = max($max, abs($min));
$dim = ($max_max * 2) + 20;

?>
<svg width="1000" height="1000" viewBox="<?php echo $dim/-2 . " " . $dim/-2 . " " . $dim . " ". $dim ?>" xmlns="http://www.w3.org/2000/svg" version="1.1">
<!-- background circles -->
<circle cx="0" cy="0" r="499" fill="white" stroke="grey" stroke-width="2" />
<circle cx="0" cy="0" r="450" fill="white" stroke="grey" stroke-width="2" />
<circle cx="0" cy="0" r="400" fill="white" stroke="grey" stroke-width="2" />
<circle cx="0" cy="0" r="350" fill="white" stroke="grey" stroke-width="2" />
<circle cx="0" cy="0" r="300" fill="white" stroke="grey" stroke-width="2" />
<circle cx="0" cy="0" r="250" fill="white" stroke="grey" stroke-width="2" />
<circle cx="0" cy="0" r="200" fill="black" />
<circle cx="0" cy="0" r="200" stroke="grey" stroke-width="2" />
<circle cx="0" cy="0" r="200" stroke="grey" stroke-width="2" />
<circle cx="0" cy="0" r="200" stroke="grey" stroke-width="2" />
<circle cx="0" cy="0" r="150" stroke="grey" stroke-width="2" />
<circle cx="0" cy="0" r="100" stroke="grey" stroke-width="2" />
<circle cx="0" cy="0" r="50" stroke="grey" stroke-width="2" />
<circle cx="0" cy="0" r="25" stroke="grey" stroke-width="1" />
<!-- numbers -->
<text x="37.5" y="0" dominant-baseline="middle" text-anchor="middle" fill="grey">10</text>
<text x="75"   y="0" dominant-baseline="middle" text-anchor="middle" fill="grey">9</text>
<text x="125"  y="0" dominant-baseline="middle" text-anchor="middle" fill="white">8</text>
<text x="175"  y="0" dominant-baseline="middle" text-anchor="middle" fill="white">7</text>
<text x="225"  y="0" dominant-baseline="middle" text-anchor="middle" fill="black">6</text>
<text x="275"  y="0" dominant-baseline="middle" text-anchor="middle" fill="black">5</text>
<text x="325"  y="0" dominant-baseline="middle" text-anchor="middle" fill="black">4</text>
<text x="375"  y="0" dominant-baseline="middle" text-anchor="middle" fill="black">3</text>
<text x="425"  y="0" dominant-baseline="middle" text-anchor="middle" fill="black">2</text>
<text x="475"  y="0" dominant-baseline="middle" text-anchor="middle" fill="black">1</text>
<text x="-125" y="0" dominant-baseline="middle" text-anchor="middle" fill="white">8</text>
<text x="-175" y="0" dominant-baseline="middle" text-anchor="middle" fill="white">7</text>
<text x="-225" y="0" dominant-baseline="middle" text-anchor="middle" fill="black">6</text>
<text x="-275" y="0" dominant-baseline="middle" text-anchor="middle" fill="black">5</text>
<text x="-325" y="0" dominant-baseline="middle" text-anchor="middle" fill="black">4</text>
<text x="-375" y="0" dominant-baseline="middle" text-anchor="middle" fill="black">3</text>
<text x="-425" y="0" dominant-baseline="middle" text-anchor="middle" fill="black">2</text>
<text x="-475" y="0" dominant-baseline="middle" text-anchor="middle" fill="black">1</text>
<text y="125"  x="0" dominant-baseline="middle" text-anchor="middle" fill="white">8</text>
<text y="175"  x="0" dominant-baseline="middle" text-anchor="middle" fill="white">7</text>
<text y="225"  x="0" dominant-baseline="middle" text-anchor="middle" fill="black">6</text>
<text y="275"  x="0" dominant-baseline="middle" text-anchor="middle" fill="black">5</text>
<text y="325"  x="0" dominant-baseline="middle" text-anchor="middle" fill="black">4</text>
<text y="375"  x="0" dominant-baseline="middle" text-anchor="middle" fill="black">3</text>
<text y="425"  x="0" dominant-baseline="middle" text-anchor="middle" fill="black">2</text>
<text y="475"  x="0" dominant-baseline="middle" text-anchor="middle" fill="black">1</text>
<text y="-125" x="0" dominant-baseline="middle" text-anchor="middle" fill="white">8</text>
<text y="-175" x="0" dominant-baseline="middle" text-anchor="middle" fill="white">7</text>
<text y="-225" x="0" dominant-baseline="middle" text-anchor="middle" fill="black">6</text>
<text y="-275" x="0" dominant-baseline="middle" text-anchor="middle" fill="black">5</text>
<text y="-325" x="0" dominant-baseline="middle" text-anchor="middle" fill="black">4</text>
<text y="-375" x="0" dominant-baseline="middle" text-anchor="middle" fill="black">3</text>
<text y="-425" x="0" dominant-baseline="middle" text-anchor="middle" fill="black">2</text>
<text y="-475" x="0" dominant-baseline="middle" text-anchor="middle" fill="black">1</text>
<!-- hits -->
<?php

foreach($points['point'] as $idx => $ps)
{
  $point = explode(",", $ps); 
  $color_lt = "red";
  $color_dk = "darkred";
  if (sqrt(pow($point[1], 2) + pow($point[0], 2)) >= 50)
  {
    $color_lt = "yellow";
    $color_dk = "darkgoldenrod";
  }
?>

<circle cx="<?php echo $point[0]; ?>" cy="<?php echo $point[1] ?>" r="10" stroke="<?php echo $color_lt; ?>" fill="<?php echo $color_dk; ?>" stroke-width="1" />
<text font-size="0.8em" x="<?php echo $point[0] ?>" y="<?php echo $point[1] ?>" dominant-baseline="central" text-anchor="middle" fill="white"><?php echo $idx+1; ?></text>

<?php
}
?>
</svg>
