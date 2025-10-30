<!--This script is part of the Aerodynamics4students suite and 
    is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
                
    Aerodynamics4students is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
                               
    To obtain the details of the GNU General Public License
    see <http://www.gnu.org/licenses/>.
-->

<?php
include "../aero4-header.php";

 $action=htmlspecialchars($_GET["go"]);
 if ($action=="Compute Metric") {
  $alt=htmlspecialchars($_GET["alt"]);
  $hunits=htmlspecialchars($_GET["hunits"]);
  if ($hunits!="m") {
   $alt=$alt*0.3048;
  } 
  $hunits="m";
  $speed=htmlspecialchars($_GET["speed"]);
  $vunits=htmlspecialchars($_GET["vunits"]);
  if ($vunits!="m/s") {
   $speed=$speed*0.3048;
  } 
  $vunits="m/s";
  $rlength=htmlspecialchars($_GET["rlength"]);
  $lunits=htmlspecialchars($_GET["lunits"]);
  if ($lunits!="m") {
   $rlength=$rlength*0.3048;
  } 
  $lunits="m";
 } elseif ($action=="Compute Imperial") {
  $alt=htmlspecialchars($_GET["alt"]);
  $hunits=htmlspecialchars($_GET["hunits"]);
  if ($hunits!="ft") {
   $alt=$alt/0.3048;
  } 
  $hunits="ft";
  $speed=htmlspecialchars($_GET["speed"]);
  $vunits=htmlspecialchars($_GET["vunits"]);
  if ($vunits!="ft/s") {
   $speed=$speed/0.3048;
  } 
  $vunits="ft/s";
  $rlength=htmlspecialchars($_GET["rlength"]);
  $lunits=htmlspecialchars($_GET["lunits"]);
  if ($lunits!="ft") {
   $rlength=$rlength/0.3048;
  } 
  $lunits="ft";
 } else {
  $alt=0;
  $hunits="m";
  $speed=1;
  $vunits="m/s";
  $rlength=1;
  $lunits="m";
 } 
 echo "<center><table width='800' height='600' background='atmos.jpg'><tr><td>"; 
 echo "<font face='Arial' size=2>";
 echo "<center><table width=760 border=1><tr><td colspan=2><font color=white>";
 echo "<B>Standard Atmosphere Computations</B><br>";
 echo "This form calculates properties related to the 1976 ";
 echo "standard atmosphere up to 230,000 ft.<br />  The code used is based on the original Javascript of"; 
 echo " Ilan Kroo (kroo@leland.stanford.edu)  31 Dec 95.";
 echo "<form name = 'units' action='atmosphere7.php' method='get'>";
 echo "</font></td></tr><tr><td>";
 echo "<B>Inputs</B><br><table><tr><td>Altitude</td>";
 printf ("<td><INPUT NAME='alt' TYPE=text VALUE=%10d SIZE=15></td>",$alt);
 echo "<td><INPUT NAME='hunits' TYPE=text SIZE=6 VALUE=".$hunits."></td></tr>";
 echo "<tr><td>Speed</td>";
 printf ("<td><INPUT NAME='speed' TYPE=text  VALUE=%10.1f SIZE='15'></td><td>",$speed);
 echo "<INPUT NAME='vunits' TYPE=text SIZE='6' VALUE=".$vunits."></td></tr>";
 echo "<tr><td>Reference Length</td>";
 printf ("<td><INPUT NAME='rlength' TYPE=text  VALUE=%10.1f SIZE='15'></td>",$rlength);
 echo "<td><INPUT NAME='lunits' TYPE=text SIZE='6' VALUE=".$lunits."></td></tr>";
 echo "</table>";
 echo "<BR><center>";
 echo "<INPUT NAME='go' TYPE='submit' VALUE='Compute Metric'> ";
 echo "<INPUT NAME='go' TYPE='submit' VALUE='Compute Imperial'> ";
 echo "<INPUT NAME='go' TYPE='submit' VALUE='Reset' >";
 echo "<br>&nbsp;<br>";
 echo "<a href='/resources'>Tabulated Data and Downloads </a><br>";
 echo "</center></form></td><td>";
 echo "<B>Results</B>";
 echo "<form name='results'>";
// calcs 
// calculate in Imperial Units.
 $h = $alt;
 $v = $speed;
 $rl = $rlength;
 if ($hunits=="m") {
  $h = $h/0.3048;
  $v = $speed/0.3048;
  $rl=$rl/0.3048;
 } 
 $TEMPSL = 518.67;
 $RHOSL = 0.00237689;
 $PRESSSL = 2116.22;
 $saTheta = 1.0;
 $saSigma = 1.0;
 $saDelta = 1.0;
 if ( $h>232939 ){
  $saTheta = 0.0;
  $saSigma = 0.0;
  $saDelta = 0.0;
 }
 if ( $h<232940 ){
  $saTheta = 1.434843 - $h/337634;
  $saSigma = pow( 0.79899-$h/606330, 11.20114 );
  $saDelta = pow( 0.838263-$h/577922, 12.20114 );
 }
 if ( $h<167323 ){
  $saTheta = 0.939268;
  $saSigma = 0.00116533 * exp( ($h-154200)/-25992 );
  $saDelta = 0.00109456 * exp( ($h-154200)/-25992 );
 }
 if ( $h<154199 ){
  $saTheta = 0.482561 + $h/337634;
  $saSigma = pow( 0.857003+$h/190115, -13.20114 );
  $saDelta = pow( 0.898309+$h/181373, -12.20114 );
 }
 if ( $h<104987 ){
  $saTheta = 0.682457 + $h/945374;
  $saSigma = pow( 0.978261+$h/659515, -35.16319 );
  $saDelta = pow( 0.988626+$h/652600, -34.16319 );
 }
 if ( $h<65617 ){
  $saTheta = 0.751865;
  $saSigma = 0.297076 * exp( (36089-$h)/20806 );
  $saDelta = 0.223361 * exp( (36089-$h)/20806 );
 }
 if ( $h<36089 ){
  $saTheta = 1.0 - $h/145442;
  $saSigma = pow( 1.0-$h/145442, 4.255876 );
  $saDelta = pow( 1.0-$h/145442, 5.255876 );
 }
 $tempVal = $TEMPSL * $saTheta;
 $rhoVal = $RHOSL * $saSigma;
 $pVal = $PRESSSL * $saDelta;
 $viscVal = 0.0000000226968*pow( $tempVal, 1.5 ) / (($tempVal)+198.72);
 $soundVal = sqrt( 1.4*1716.56*($tempVal) );
 $machVal = $v/$soundVal;
 $qVal = 0.7*$pVal*$machVal*$machVal;
 $reynolds = $v*$rl*$rhoVal/$viscVal;
 $cfturb = 0.455/pow((log($reynolds)/log(10)),2.58);
 $cflam = 1.328/sqrt($reynolds);
 $temp = $tempVal;
 $rho =  $rhoVal;
 $press =  $pVal;
 $ssound =  $soundVal;
 $visc =  $viscVal;
 $mach =  $machVal;
 $q =  $qVal;
 $cpstar =  (pow((1/1.2 + $machVal*$machVal/6.0),3.5)-1)/(0.7*$machVal*$machVal);
 $cpmin =  -1.0/(0.7*$machVal*$machVal);
 $reno = $reynolds;
 $cfl = $cflam;
 $cft = $cfturb;
 if ($hunits=="m") {
  $temp =$tempVal/1.8;
  $rho = $rhoVal / .068521  / .028317;
  $press = $pVal / .020885;
  $ssound = $soundVal/3.2808;
  $visc = $viscVal/.22481/.092903;
  $q = $qVal / .020885;
 }
 echo "<table><tr><td>Temperature</td>";
 printf ("<td><INPUT NAME='temp' TYPE=text SIZE=15 value=%10.1f></td>",$temp);
 if ($hunits=="m") {
  echo "<td><INPUT NAME=tunits TYPE=text SIZE=9 VALUE='Deg K'></td>";
 } else { 
  echo "<td><INPUT NAME=tunits TYPE=text SIZE=9 VALUE='Deg R'></td>";
 }
 echo "</tr><tr><td>Density</td>";
 printf ("<td><INPUT NAME='rho' TYPE=text SIZE=15 value=%10.5f></td>",$rho);
 if ($hunits=="m") {
  echo "<td><INPUT NAME='runits' TYPE=text SIZE=9 VALUE='Kg/m^3'></td>";
 } else {
  echo "<td><INPUT NAME='runits' TYPE=text SIZE=9 VALUE='sl/ft^3'></td>";
 } 
 echo "</tr><tr><td>Pressure</td>";
 printf ("<td><INPUT NAME='press' TYPE=text SIZE=15 value=%10.1f></td>",$press);
 if ($hunits=="m") {
  echo "<td><INPUT NAME='punits' TYPE=text SIZE=9 VALUE='N/m^2'></td>";
 } else {
  echo "<td><INPUT NAME='punits' TYPE=text SIZE=9 VALUE='lb/ft^2'></td>";
 } 
 echo "</tr><tr><td>Speed of Sound</td>";
 printf ("<td><INPUT NAME='ssound' TYPE=text SIZE=15 value=%10.1f></td>",$ssound);
 if ($hunits=="m") {
  echo "<td><INPUT NAME='sunits' TYPE=text SIZE=9 VALUE='m/s'></td>";
 } else {
  echo "<td><INPUT NAME='sunits' TYPE=text SIZE=9 VALUE='ft/s'></td>";
 } 
 echo "</tr><tr><td>Viscosity</td>";
 printf ("<td><INPUT NAME='ssound' TYPE=text SIZE=15 value=%10.4e></td>",$visc);
 if ($hunits=="m") {
  echo "<td><INPUT NAME='vuunits' TYPE=text SIZE=9 VALUE='N sec/m^2'></td>";
 } else {
  echo "<td><INPUT NAME='vunits' TYPE=text SIZE=9 VALUE='lb sec/ft^2'></td>";
 } 
 echo "</tr><tr><td>Mach Number</td>";
 printf ("<td><INPUT NAME='mach' TYPE=text SIZE=15 value=%10.3f></td>",$mach);
 echo "<td> </td></tr><tr><td>Dynamic Pressure</td>";
 printf ("<td><INPUT NAME='q' TYPE=text SIZE=15 value=%10.3f></td>",$q);
 if ($hunits=="m") {
  echo "<td><INPUT NAME='qunits' TYPE=text SIZE=9 VALUE='N/m^2'></td>";
 } else {
  echo "<td><INPUT NAME='qunits' TYPE=text SIZE=9 VALUE='lb/ft^2'></td>";
 } 
 echo "</tr><tr><td>Critical Cp</td>";
 printf ("<td><INPUT NAME='cpstar' TYPE=text SIZE=15 value=%10.3f></td>",$cpstar);
 echo "<td> </td></tr><tr><td>Vacuum Cp</td>";
 printf ("<td><INPUT NAME='cpmin' TYPE=text SIZE=15 value=%10.3f></td>",$cpmin);
 echo "<td> </td></tr><tr><td>Reynolds Number</td>";
 printf ("<td><INPUT NAME='reno' TYPE=text SIZE=15 value=%10d></td>",$reno);
 echo "<td> </td></tr><tr><td>Laminar Cf</td>";
 printf ("<td><INPUT NAME='cfl' TYPE=text SIZE=15 value=%10.5e></td>",$cfl);
 echo "<td> </td></tr><tr><td>Turbulent Cf</td>";
 printf ("<td><INPUT NAME='cft' TYPE=text SIZE=15 value=%10.5e></td>",$cft);
 echo "</tr></table></form></td></tr></table></font></td></tr></table></center>";
 echo "<p><a href='/properties-of-the-atmosphere/'>Back to Properties of Atmosphere Section</a></p>";

include "../aero4-footer.php";

?>


