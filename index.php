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
  include('config.php');
?>
<!DOCTYPE HTML>
<html>
  <head>
    <title><?php echo($SITE_TITLE); ?></title>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- vendor scripts -->
    <script src="vendor/jquery.min.js"></script>
    <script src="vendor/bootstrap.bundle.min.js"></script>
    <script src="vendor/mustache.min.js"></script>
    <script src="vendor/chevron.min.js"></script>

    <!-- vendor styles -->
    <link rel="stylesheet" href="vendor/normalize.css" />
    <link rel="stylesheet" href="vendor/bootstrap.min.css" />
    <link rel="stylesheet" href="vendor/fonts/css/all.min.css" />

    <!-- our configuration -->
    <script type="text/javascript">
      window.config = <?php echo($SITE_CONFIG); ?>;
    </script>

    <!-- our scripts -->
    <script src="main.js"></script>
    <script src="locale-<?php echo($SITE_LANG); ?>.js"></script>

    <!-- our styles -->
    <link rel="stylesheet" href="vendor/fonts/fonts.css" />
    <link rel="stylesheet" href="main.css" />

    <link rel="icon" type="image/svg+xml" href="images/favicon.svg">
    <link rel="icon" type="image/png" href="images/favicon.png">
  </head>
  <body>
    <div id="content">

    </div>
    <!-- templates -->
    <div id="templates">
      <nav class="navbar sticky-top navbar-expand-lg navbar-light bg-light" id="nav-template">
        <div class="container-fluid">
          <a class="navbar-brand" href="#"><i class="fa-solid fa-crosshairs"></i> {{site_title}}</a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
              <li class="nav-item">
                <a class="nav-link" id="link-competition-list" href="#">{{link_competition_list}}</a>
              </li>
{{ #show_live }}
              <li class="nav-item">
                <a class="nav-link" href="#live">{{link_live}}</a>
              </li>
{{ /show_live }}
              <li class="nav-item">
                <a class="nav-link" href="#impressum">{{link_impressum}}</a>
              </li>
            </ul>
{{ #show_search }}
            <form class="d-flex">
              <input class="form-control me-2" type="search" placeholder="{{search_placeholder}}" aria-label="{{search_placeholder}}">
              <button class="btn btn-outline-success" type="submit">{{search_button}}</button>
            </form>
{{ /show_search }} 
{{ #back_link }}
            <a class="btn me-2 btn-outline-secondary" href="#competition:{{back_link}}" id="back_to_competition">{{link_back_to_competition}}</a>
{{ /back_link }}

          </div>
        </div>
      </nav>

      <div class="container" id="competitions-template">
        <div class="row">
          <div class="col">
            <table class="table table-striped table-hover">
              <thead class="sticky">
                <tr>
                  <th scope="col">{{header_date}}</th>
                  <th scope="col">{{header_discipline}}</th>
                </tr>
              </thead>
              <tbody>
<!--              {{#competitions}} -->
                <tr>
                  <td><a href="#competition:{{path}}" class="rowlink">{{date_trimmed}}</a></td>
                  <td>{{discipline}}</td>
                </tr>
<!--              {{/competitions}} -->
              </tbody>              
            </table>
          </div>
        </div>
      </div>

      <div class="container competition-view" id="competition-view-template">
        <div class="row">
          <div class="col">
            <div class="">
              <h1>{{meta.discipline}}</h1>
              <h2>{{date_trimmed}}</h2>
            </div>
            <table class="table table-striped table-hover table-bordered">
              <thead class="sticky">
                <tr>
                  <th scope="col">{{header_place}}</th>
                  <th scope="col">{{header_competitor}}</th>
                  <th scope="col">{{header_series}}</th>
                  <th scope="col">{{header_result}}</th>
                </tr>
              </thead>
              <tbody>
<!--              {{#results}} -->
                <tr>
                  <td class="place"><a href="#entrant:{{path}}:{{place}}" class="rowlink">{{place}}</a></td>
                  <td>
                    <div class="d-flex">
                      <div class="flex-column">
                        <div class="flex-row">
                          <div class="competitor"><span class="flag">{{data.shooter.Country}}</span> {{data.shooter.FamilyName}}, {{data.shooter.GivenName}}</div>
                        </div>
                        <div class="flex-row">
                          <div class="team">{{data.team}}</div>
                          <div class="class">{{data.matchclass}}</div>
                        </div>
                      </div>
                    </div>
                  </td>
                  <td>{{#data.series}}<span class="series-result">{{series_result}}</span>{{/data.series}}</td>
                  <td>
                    <div class="d-flex">
                      <div class="flex-column">
                        <div class="result">{{result}}</div>
                        <div class="average">Ø ({{average}})</div>
                      </div>
                    </div>
                  </td>
                </tr>
<!--              {{/results}} -->
              </tbody>              
            </table>
          </div>
        </div>
      </div>

      <div class="container shooter-view" id="shooter-view-template">
        <div class="row backlink">
          <div class="col">
          </div>
        </div>
        <div class="row">
          <div class="col">
            <div class="row">
              <div class="col-sm-6">
                <h1><span class="flag">{{record.Shooter.Country}}</span> {{record.Shooter.FamilyName}}, {{record.Shooter.GivenName}}</h1>
              </div>
              <div class="col-sm-6 text-sm-end align-text-bottom">
                <h2>{{record.Club.Name}}</h2>
              </div>
            </div>              

            <div class="meta-info">
              <div class="row">
                <div class="col-sm-4"><span>{{header_class}}:</span> {{record.MatchClass.Name}}</div>
                <div class="col-sm-4 text-sm-center"><span>{{header_date}}:</span> {{start_time}}</div>
                <div class="col-sm-4 text-sm-end"><span>{{header_lane_nr}}:</span> {{record.@attributes.LaneNo}}</div>
              </div>
              <div class="row">
                <div class="col-sm-6"><span>{{header_discipline}}:</span> {{record.Discipline.Name}}</div>
                <div class="col-sm-6 text-sm-end"><span>{{header_start_nr}}:</span> {{record.@attributes.StartID}}</div>
              </div>
            </div>

            <div class="results">
              <div class="row">
                <div class="col-sm-4 order-sm-last p-2 p-sm-0"><img class="img-fluid rounded-3" src="image_shooter_view.php?{{image_point_list}}" /></div>
                <div class="col-sm-8">
                  <div class="card result-top">
                    <div class="card-body m-2 {{#is_rank_1}} text-white bg-success {{/is_rank_1}}">
                      <h5 class="card-title">{{header_result}}: {{result_exact}} ({{result_floor}})</h5>
                      <h6 class="card-subtitle">{{header_place}}: {{result_place}}</h6>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-sm-5">
                      <div class="card result-series">
                        <div class="card-header">
                          {{header_series}}
                        </div>
                        <ul class="list-group list-group-flush">
                          {{#record.Aimings.AimingData.Series}}
                          <li class="list-group-item"><strong>{{@attributes.SeriesID}}</strong>: <span>{{series_exact}} <span class="text-muted">({{series_floor}})</span></span></li>
                          {{/record.Aimings.AimingData.Series}}
                        </ul>
                      </div></div>
  
                      <div class="col-sm-7 order-sm-first result-statistics">
                        <dl>
                          <dt>{{header_distribution}}</dt><dd>{{statistics.distribution}}</dd>
                          <dt>{{header_inner_rings}}</dt><dd>{{statistics.inner_rings}}</dd>
                          <dt>{{header_competition_time}}</dt><dd>{{statistics.total_time}}</dd>
                          <dt>{{header_avg_time_per_shot}}</dt><dd>{{statistics.avg_time_per_shot}} {{unit_seconds_short}}</dd>
                          <dt>{{header_best_teiler}}</dt><dd>{{#statistics.best_three}} {{RingValue.1.Result}} <span class="text-muted">(#{{@attributes.ShotID}})</span>{{/statistics.best_three}}</dd>
                          <dt>{{header_worst_teiler}}</dt><dd>{{#statistics.worst_three}} {{RingValue.1.Result}} <span class="text-muted">(#{{@attributes.ShotID}})</span>{{/statistics.worst_three}}</dd>
                          <dt>{{header_centerpoint}}</dt><dd>{{statistics.centerpoint.x}} {{unit_mm}} {{statistics.centerpoint.xu}}, {{statistics.centerpoint.y}} {{unit_mm}} {{statistics.centerpoint.yu}}</dd>
                        </dl>
                      </div>
                    </div>
                  </div>
                </div>
              {{#record.Aimings.AimingData.Series}}
                <div class="row series-detail-head">
                  <h5>{{header_series}} {{@attributes.SeriesID}}</h5>
                </div>
                <div class="row series-detail">
                  <div class="col-sm-2 p-sm-0 p-2 series-detail-statistics">
                     <img class="img-fluid" src="image_shooter_view.php?{{series_image_point_list}}" />
                  </div>
                  <div class="col-sm-10 p-sm-0 d-flex flex-column series-detail">
                    <div class="d-flex p-sm-0 flex-row flex-wrap justify-content-between bg-light">
                      {{#series_shot_values}}
                      <div class="p-2 col-sm-1">{{.}}</div>
                      {{/series_shot_values}}
                      <div class="p-2 col-sm-2 text-sm-end"><span class="badge bg-secondary">{{header_total}}: {{series_exact}}</span></div>                   
                    </div>
                    <div class="series-statistics p-3">
                      <dl>
                        <dt>{{header_avg_time_per_shot}}</dt><dd>{{series_avg_time}} {{unit_seconds_short}}</dd>
                        <dt>{{header_best_s_teiler}}</dt><dd>{{series_best_shot}}</dd>
                        <dt>{{header_worst_s_teiler}}</dt><dd>{{series_worst_shot}}</dd>
                      </dl>
                    </div>
                  </div>
                </div>
              {{/record.Aimings.AimingData.Series}}
            </div>
          </div>
        </div>
      </div>

      <div class="container" id="search-template">
        <div class="row">
          <div class="col">
            search
          </div>
        </div>
      </div>

      <div class="container" id="liveview-template">
        <div class="row">
          <div class="col">
            live
          </div>
        </div>
      </div>

      <div class="container" id="impressum-template">
        <div class="row">
          <div class="col contents">
            
          </div>
        </div>
      </div>
    </div>
    <!-- / templates -->
  </body>
</html>
