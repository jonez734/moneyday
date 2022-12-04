<?php

require_once("config.php");
require_once("zoidweb4.php");
require_once("bbsengine4.php");

//require_once("Calendar/Month.php");
//require_once("Calendar/Month/Weeks.php");
//require_once("Calendar/Util/Uri.php");
require_once("Calendar/Month/Weekdays.php");
require_once("Calendar/Day.php");
//require_once("Calendar/Decorator.php");
require_once("Calendar/Decorator/Textual.php");
require_once("Calendar/Util/Textual.php");

// inspired by http://www.danielkassner.com/2010/05/22/get-date-by-position-ie-third-wednesday-of-january

class moneyday
{
  function daystillnext($current, $next)
  {
    $secondstill = $next->getTimestamp() - $current->getTimestamp();
    $daystill = round($secondstill / 60 / 60 / 24, 0);
    return $daystill;
  }

  function buildmoneydayfieldset($form)
  {
    $fieldset = $form->addElement("fieldset", "moneydayfieldset", ["attributes" => ["separator" => "aa"]])->setLabel("Select Interval");

    $group = $fieldset->addGroup("period");

    $ordinals = array("1" => "1st", "2" => "2nd", "3" => "3rd", "4" => "4th");
    $group->addElement("select", "nth", null, array("options" => $ordinals));
    
    $w = 0;
    $options = [];
    foreach (Calendar_Util_Textual::weekdayNames("long") as $weekdayname)
    {
      $options[$w] = $weekdayname;
      $w++;
    }
    $group->addElement("select", "weekdaynumber", null, array("options" => $options, "label" => "Day of week"));

    $el = $group->addElement("select", "monthnumber", null, ["label" => "Month"]);
    $m = 1;
    $options = [];
    foreach (Calendar_Util_Textual::monthNames("long") as $mon)
    {
      $el->addOption($mon, $m);
      $m++;
    }

    $el = $group->addElement("text", "year", "size=4 maxlength=4 placeholder=\"YYYY\"")->setLabel("Year"); // , ["label" => "Year"]);
    
    $el = $group->addElement("text", "delta", "size=2 maxlength=2")->setLabel("delta");// , ["label" => "Delta"]);
    
    return;
  }
  
  function buildordinaldate($buf) // $ordinal, $dayname, $monthname, $year)
  {
  //  $buf = "{$ordinal} {$dayname} {$monthname} {$year}";
    $epoch = strtotime($buf);
    $day = new Calendar_Day(1972, 1, 2);
    $day->setTimestamp($epoch);
    $day->adjust();
    return $day;
  }

  function buildcalendardata($period) // $datetime, $ordinal) // $ordinal, $dayname, $monthname, $year)
  {
    $datetime = $this->calc($period);
    $moneyday = new Calendar_Day($datetime->format("Y"), $datetime->format("n"), $datetime->format("d"));
    $moneyday->adjust();
    
    $calendardata = [];
    $calendardata["moneyday"] = $moneyday;

    $moneydaytext = new Calendar_Decorator_Textual($moneyday);
    $calendardata["moneydaytext"] = $moneydaytext;

    $monthweekdays = new Calendar_Month_Weekdays($moneyday->thisYear(), $moneyday->thisMonth(), 0);
    $monthweekdays->adjust();
    $monthweekdays->build([$moneyday]);
    $calendardata["monthweekdays"] = $monthweekdays;

    $monthweekdaystext = new Calendar_Decorator_Textual($monthweekdays);
    $calendardata["monthweekdaystext"] = $monthweekdaystext;

    $calendardata["datetime"] = $datetime;
    $calendardata["daystillnext"] = 0;
//    $calendardata["ordinal"] = $ordinal;
//    $calendardata["dayname"] = $dayname;
//    $calendardata["monthname"] = $monthname;
//    $calendardata["year"] = $year;

    return $calendardata;
  }
  
  function calc($period)
  {
//    define("SUNDAY", 0);

    $nth = $period["nth"];
    $weekdaynumber = $period["weekdaynumber"]; // 0=sunday
    $monthnumber = $period["monthnumber"];
    $year = intval($period["year"]);
    
    if ($monthnumber > 12)
    {
      $monthnumber -= 12;
      $year+=1;
    }
    
    if ($monthnumber < 1)
    {
      $monthnumber += 12;
      $year -= 1;
    }

    $DAYS = Calendar_Util_Textual::weekdayNames("long"); // [ "sunday", "monday", "tuesday", "wednesday", "thursday", "friday", "saturday" ];
    $ORDINAL = [ "first", "second", "third", "fourth", "fifth" ];
/*
    $tmpday = (SUNDAY+7)*$nth;
    logentry("moneyday.440: tmpday=".var_export($tmpday, True));

//    $mk = mktime($year, $monthnumber, $tmpday);
    
    $date = new DateTime("{$year}-{$monthnumber}-{$tmpday}");
//    $date->setTimestamp($mk);
//    logentry("moneyday.410: date=".var_export($date, True));
    
    $tmpday = (0+7)-$weekdaynumber;
    $d = new DateTime("now"); // "{$year}-{$monthnumber}-{$tmpday}");
    $mk = mktime($year, $monthnumber, $tmpday);
    $d->setTimestamp($mk);

    $weekday = $d->format("w");
    logentry("moneyday.420: weekday=".var_export($weekday, True));

    $result = $date->sub(new DateInterval("P{$weekday}D"));
    logentry("moneyday.430: result=".var_export($result, True));
*/
    $buf = $ORDINAL[$nth-1] . " " . $DAYS[$weekdaynumber] . " of " . $year . "-" . $monthnumber;
    logentry("moneyday.450: buf=".var_export($buf, True));
//    $result = date("Y-m-d", strtotime($buf));
    $result = new DateTime($buf);
    logentry("moneyday.440: result=".var_export($result, True));
    return $result;
  }
  
  function show($values)
  {
    // goal of "second wednesday"
    // calculate "first wednesday" of given month and year
    // add 7 days to get "second wednesday", etc
    $currentperiod = $values["period"];
    logentry("moneyday.400: period=".var_export($currentperiod, True));
    
    $previousperiod = $currentperiod;
    $previousperiod["monthnumber"] = $currentperiod["monthnumber"]-1;
    
    $nextperiod = $currentperiod;
    $nextperiod["monthnumber"] = $currentperiod["monthnumber"]+1;

    $delta = intval($currentperiod["delta"]);

    $calendars = [];
    
    logentry("moneyday.420: delta=".var_export($delta, True));

    for ($i = -$delta; $i < 0; $i++)
    {
      $y = $currentperiod["year"];
      $m = $currentperiod["monthnumber"] + $i;
      if ($m < 0)
      {
        $m = 12 + $m;
        $y--;
      }
      $period = $currentperiod;
      $period["monthnumber"] = $m; // abs($currentperiod["monthnumber"] + $i);
      $period["year"] = $y;
      $calendars[] = $this->buildcalendardata($period);
      logentry("moneyday.422: period=".var_export($period, True));
    }
    
    $calendars[] = $this->buildcalendardata($currentperiod);
    
    for ($i = 1; $i < $delta+1; $i++)
    {
      $period = $currentperiod;
      $period["monthnumber"] = $currentperiod["monthnumber"] + $i;
      
      $calendars[] = $this->buildcalendardata($period);
      logentry("moneyday.424: period=".var_export($period, True));
    }

    $j = count($calendars);
    for ($i=0; $i < $j-1; $i++)
    {
      $dtc = $calendars[$i]["datetime"];
      $dtn = $calendars[$i+1]["datetime"];
      $calendars[$i]["daystillnext"] = $dtc->diff($dtn)->format("%a");
    }
    $content = getsmarty();
    $content->assign("calendars", $calendars);

    $page = getpage("moneyday");
//    $page->addStyleSheet(SKINURL."css/moneyday.css");
    $data = [];
    $data["pagetemplate"] = "calendars.tmpl";
    $data["calendars"] = $calendars;
    displaypage($page, $data);
    return True;
  }

  function main()
  {
    startsession();
    
    setcurrentpage("moneyday");
    setcurrentsite("www");
    setcurrentaction("view");

    $form = getquickform("moneyday", "POST", "/moneyday/");
    $this->buildmoneydayfieldset($form);
    $form->addElement("submit", "submitmoneyday", ["value" => "calculate"]);

    $period = [];
    $period["monthname"] = date("F");
    $period["year"] = date("Y");
    $period["weekdayname"] = date("l");
    $period["delta"] = 3;

    $defaults = [];
    $defaults["period"] = $period;

    logentry("moneyday.98: defaults=".var_export($defaults, True));

    $form->addDataSource(new HTML_QuickForm2_DataSource_Array($defaults));

    $res = handleform($form, [$this, "show"], "select moneyday period");
    if ($res === True)
    {
      logentry("moneyday.102: handleform(...) returned True");
      return True;
    }
    if (PEAR::isError($res))
    {
      logentry("moneyday.104: ".$res->toString());
      return $res;
    }

    $renderer = getquickformrenderer();
    $form->render($renderer);
  
    $tmpl = getsmarty();

    $data = [];
//    $data["pagetemplate"] = "form.tmpl";
    $res = displayform($renderer, "select moneyday period", $data);
    if (PEAR::isError($res))
    {
      logentry("moneyday.232: " . $res->toString());
      return PEAR::raiseError("error displaying form (code: moneyday.232)");
    }
    return;
  }
};

$a = new moneyday();
$b = $a->main();
if (PEAR::isError($b))
{
  logentry("moneyday.1000: " . $b->toString());
  exit;
}

?>
