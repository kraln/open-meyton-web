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
  *
  *
  *
  * main single page logic
  *
  */

(function () {
  'use strict';

  /* utility functions */
  window.getFlagEmoji = function(countryCode) {
    const codePoints = countryCode
      .toUpperCase()
      .split('')
      .map(char =>  127397 + char.charCodeAt());
    return String.fromCodePoint(...codePoints);
  };

  function parseISOString(s) {
    var b = s.split(/\D+/);
    return new Date(Date.UTC(b[0], --b[1], b[2], b[3], b[4], b[5], b[6]));
  }

  function millisToMinutesAndSeconds(millis) {
    var minutes = Math.floor(millis / 60000);
    var seconds = ((millis % 60000) / 1000).toFixed(0);
    return minutes + ":" + (seconds < 10 ? '0' : '') + seconds;
  }

  /* page plumbing */

  function navigation(args)
  {
    let template = $("nav").last().prop("outerHTML");

    let back_link = false;
    /* show only if we're looking at a specific entry */
    if (args[0] == "#entrant")
    {
      back_link = args[1].substring(0, args[1].lastIndexOf("/"));
    } 

    let merged   = { ...window.config, ...window.locale, back_link};
    let rendered = Mustache.render(template, merged);
    document.getElementById('content').innerHTML = rendered;
  }

  function setupCompetitionList()
  {
    $.get("data_competition_list.php", function(data) {
        var template = document.getElementById('competitions-template').outerHTML;

        data.date_trimmed = function() { return this.date.date.slice(0, 10); };

        let merged   = { ...window.config, ...window.locale, ...data};
        var rendered = Mustache.render(template, merged);

        document.getElementById('content').insertAdjacentHTML('beforeend', rendered);
        }, "json");
  }

  function setupCompetitionView(which)
  {
    $.get("data_competition_view.php?competition_path=" + which, function(data) {
        var template = document.getElementById('competition-view-template').outerHTML;

        data.date_trimmed = function() { return this.meta.date.date.slice(0, 10); };

        data.average = function() { return (this.data.result / this.data.shotcount / 10).toFixed(2); };
        data.result = function() { return Math.floor(this.data.result / 10); };
        data.series_result = function() { return Math.floor(this.result.Result / 10); };

        /* need to sort and index results */
        data.results.sort((a, b) => { return b.data.result - a.data.result; });
        data.results.forEach((e, i) => { e.place = i+1; });        

        let merged   = { ...window.config, ...window.locale, ...data};
        var rendered = Mustache.render(template, merged);

        document.getElementById('content').insertAdjacentHTML('beforeend', rendered);
        }, "json")
        .done(() => { $("#content tbody tr").first().addClass("table-success"); })
        .done(() => { $("#content span.flag").each(function(e) {
          var tlc = $(this).text();
          if (tlc == "GER") { tlc = "DEU"; } // sigh
          const country = window.iso3166.find(el => el['alpha-3'] == tlc); 
          if (country) {
            $(this).text(window.getFlagEmoji(country['alpha-2']));
          }
        }); });
  }

  function setupEntrantView(which, rank)
  {
    $.get("data_shooter_view.php?target_path=" + which, function(data) {
        var template = document.getElementById('shooter-view-template').outerHTML;

        data.start_time = function() {
          const ev = parseISOString(this.record.Aimings.AimingData.Shot[0].TimeStamp.DateTime);
          return ev.toDateString(); 
        };

        /* 
         * due to a quirk, we need to calculate the capped values based on the shots
         * as the xml does not give us the rounded data
         */
        data.result_floor = function() {
          return data.record.Aimings.AimingData.Shot.reduce( (p, c) => (p + Math.floor(c.RingValue[0].Result/10)), 0);
        };        

        data.series_floor = function() {
          const series = this['@attributes'].SeriesID;
          const totalshots = data.record.Aimings.AimingData.Shot.length;
          const totalseries = data.record.Aimings.AimingData.Series.length;
          const shotsperseries = totalshots/totalseries;
          
          let shots_in_series = data.record.Aimings.AimingData.Shot.slice(shotsperseries*(series-1),shotsperseries*series); 
          return shots_in_series.reduce( (p, c) => (p + Math.floor(c.RingValue[0].Result/10)), 0);
        };

        data.result_exact = function() {
          return (this.record.Total.Result / 10).toFixed(1);
        };

        data.series_exact = function() {
          return (this.ValueSerie.Result / 10).toFixed(1);
        };        

        data.series_shot_values = function() {
          const series = this['@attributes'].SeriesID;
          const totalshots = data.record.Aimings.AimingData.Shot.length;
          const totalseries = data.record.Aimings.AimingData.Series.length;
          const shotsperseries = totalshots/totalseries;
          
          let shots = data.record.Aimings.AimingData.Shot.slice(shotsperseries*(series-1),shotsperseries*series); 
          return shots.map(shot => (shot.RingValue[0].Result/10).toFixed(1) + (shot['@attributes'].IsInnerTen?"*":""));
        }

        /* precalculate the statistics for each series + the overall attempt */
        data.statistics = {};
        data.statistics.total_time_raw = (parseISOString(data.record.Aimings.AimingData.Shot[data.record.Aimings.AimingData.Shot.length-1].TimeStamp.DateTime) -
          parseISOString(data.record.Aimings.AimingData.Shot[0].TimeStamp.DateTime));
        data.statistics.total_time = millisToMinutesAndSeconds(data.statistics.total_time_raw); 

        data.statistics.inner_rings = data.record.Aimings.AimingData.Shot.reduce( 
          (p,c) => p + (c['@attributes'].IsInnerTen?1:0), 0);

        data.statistics.avg_time_per_shot = ((data.statistics.total_time_raw / data.record.Aimings.AimingData.Shot.length)/1000).toFixed(2);

        var sorted_best_worst = [...data.record.Aimings.AimingData.Shot].sort(
          (a, b) => { return a.RingValue[1].Result - b.RingValue[1].Result;}
        );

        data.statistics.best_three = sorted_best_worst.slice(0, 3);

        data.statistics.worst_three = sorted_best_worst.slice(-3).reverse();

        data.statistics.centerpoint = {};
        data.statistics.spread = {};
        data.statistics.centerpoint.x = 0;
        data.statistics.centerpoint.y = 0;

        //TODO: calculate
        data.statistics.spread.total = 0;
        data.statistics.spread.x = 0;
        data.statistics.spread.y = 0;

        let buckets = Array.from({length: 11}, (v,i)=>0);
        for (const shot of data.record.Aimings.AimingData.Shot)
        {
          var val = Math.floor(shot.RingValue[0].Result/10);
          buckets[val]++;
          data.statistics.centerpoint.x += (shot.Coordinate.CCoordinate.X / data.record.Aimings.AimingData.AimingShotNo);
          data.statistics.centerpoint.y += (shot.Coordinate.CCoordinate.Y / data.record.Aimings.AimingData.AimingShotNo);
        }

        //TODO set or derive properly
        let unit_conv = 25;

        data.statistics.centerpoint.x /= unit_conv;
        data.statistics.centerpoint.y /= unit_conv;

        data.statistics.centerpoint.x = data.statistics.centerpoint.x.toFixed(2);
        data.statistics.centerpoint.y = data.statistics.centerpoint.y.toFixed(2);

        data.statistics.centerpoint.xu = data.statistics.centerpoint.x < 0 ? window.locale.unit_left : window.locale.unit_right;
        data.statistics.centerpoint.yu = data.statistics.centerpoint.y < 0 ? window.locale.unit_up : window.locale.unit_down;

        data.statistics.centerpoint.x = Math.abs(data.statistics.centerpoint.x);
        data.statistics.centerpoint.y = Math.abs(data.statistics.centerpoint.y);

        data.statistics.distribution = buckets.reverse().join(", ");

        /*
         * unfortunately the data quality is crap. we need to calculate the divisor
         * based on the x/y values as well as the result
         */
        var sx = data.record.Aimings.AimingData.Shot[0].Coordinate.CCoordinate.X;
        var sy = data.record.Aimings.AimingData.Shot[0].Coordinate.CCoordinate.Y;
        var sresult = (109 - data.record.Aimings.AimingData.Shot[0].RingValue[0].Result);

        /* figure out what the "should" result should be */
        var dresult = Math.sqrt(Math.pow(sx, 2) + Math.pow(sy, 2));

        const magic_fudge = 5; /* do not question the source of the magic fudge */
        var divisor = (dresult / sresult / magic_fudge);

        data.image_point_list = data.record.Aimings.AimingData.Shot.map(point => {       
          return "point[]=" + Math.floor(point.Coordinate.CCoordinate.X/divisor)+ "," + Math.floor(point.Coordinate.CCoordinate.Y/divisor); 
        }).join("&");

        data.series_avg_time = function() {
          const series = this['@attributes'].SeriesID;
          const totalshots = data.record.Aimings.AimingData.Shot.length;
          const totalseries = data.record.Aimings.AimingData.Series.length;
          const shotsperseries = totalshots/totalseries;
          let shots = data.record.Aimings.AimingData.Shot.slice(shotsperseries*(series-1),shotsperseries*series); 

          return (parseISOString(shots[shots.length-1].TimeStamp.DateTime) - parseISOString(shots[0].TimeStamp.DateTime))/shotsperseries/1000;

        };

        data.series_best_shot = function() {
          const series = this['@attributes'].SeriesID;
          const totalshots = data.record.Aimings.AimingData.Shot.length;
          const totalseries = data.record.Aimings.AimingData.Series.length;
          const shotsperseries = totalshots/totalseries;
          let shots = data.record.Aimings.AimingData.Shot.slice(shotsperseries*(series-1),shotsperseries*series); 
          let res = shots.reduce((p, c) => 
            { 
              return ((Number(c.RingValue[1].Result) < p) ? Number(c.RingValue[1].Result) : p); 
            }, 10000000);
          return res;
        };

        data.series_worst_shot = function() {
          const series = this['@attributes'].SeriesID;
          const totalshots = data.record.Aimings.AimingData.Shot.length;
          const totalseries = data.record.Aimings.AimingData.Series.length;
          const shotsperseries = totalshots/totalseries;
          let shots = data.record.Aimings.AimingData.Shot.slice(shotsperseries*(series-1),shotsperseries*series); 
          let res = shots.reduce((p, c) => 
            { 
              return ((Number(c.RingValue[1].Result) > p) ? Number(c.RingValue[1].Result) : p); 
            }, 0);
          return res;
        };




        data.series_image_point_list = function() {
          const series = this['@attributes'].SeriesID;
          const totalshots = data.record.Aimings.AimingData.Shot.length;
          const totalseries = data.record.Aimings.AimingData.Series.length;
          const shotsperseries = totalshots/totalseries;
          let shots = data.record.Aimings.AimingData.Shot.slice(shotsperseries*(series-1),shotsperseries*series); 
          return shots.map(point => {       
            return "point[]=" + Math.floor(point.Coordinate.CCoordinate.X/divisor) + "," + Math.floor(point.Coordinate.CCoordinate.Y/divisor); 
          }).join("&");
        };
 
        /* comes from the url, because the rank is not in the xml file */
        data.result_place = rank;
        data.is_rank_1 = rank == 1;

        let merged   = { ...window.config, ...window.locale, ...data};
        var rendered = Mustache.render(template, merged);

        document.getElementById('content').insertAdjacentHTML('beforeend', rendered);
        }, "json")
        .done(() => { $("#content span.flag").each(function(e) {
          var tlc = $(this).text();
          if (tlc == "GER") { tlc = "DEU"; } // sigh
          const country = window.iso3166.find(el => el['alpha-3'] == tlc); 
          if (country) {
            $(this).text(window.getFlagEmoji(country['alpha-2']));
          }
        }); });
  }



  /* main dispatch */
  function setupPage(whichPage)
  {    
    var args = whichPage.split(":");
    whichPage = args[0].slice(1);

    console.log("Navigation: " + whichPage);

    $("nav.navbar a").removeClass("active");

    navigation(args);

    if (whichPage == "live")
    {
      // do live page
    }
    else if (whichPage == "impressum")
    {
      // impressum stuff
    } 
    else if (whichPage == "competition")
    {
      setupCompetitionView(args[1]);
    }
    else if (whichPage == "entrant")
    {
      setupEntrantView(args[1], args[2]);
    } 
    else
    {
      // default / competition list
      setupCompetitionList();
      $("#link-competition-list").first().addClass("active");
    }
  }

  function onWindowLoaded() {
    $.getJSON("vendor/countries.json", function(json) {
      window.iso3166 = json;
    }).always(() => {      
      setupPage(window.location.hash);     
    });
  }

  function onHashChanged() {
    setupPage(window.location.hash);
  }

  window.addEventListener('load', onWindowLoaded, false);
  window.addEventListener('hashchange', onHashChanged, false);
})();
