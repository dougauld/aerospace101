<?php
include "../aero4-header.php";
$expression = htmlspecialchars($_GET["expr"]);
?>
<h2>Potential Flow Streamlines</h2>
<table><tr><td width='50%'>
<p> 
Streamlines can be define by a streamfunction expression in cartesion coordinates.<br />
Coordinates can be entered as follows:
<pre>      x -- x ordinate, y -- y ordinate
           Operators : * / + - ^
           Functions : sin,cos,ln,atan,sqrt</pre>
Note : there is no guarantee that any chosen function is a valid potential flow. You need to verify all functions using the continuity equation.</p>           
<form action="potflow.php" method="get">
Streamfunction Expression : <br />
<?php
 echo "<input type='text' name='expr' value='".$expression."' size='80'>";
?>
<br /><input type='submit' name='Draw' value='Draw'>
</form></td><td>
<?php
if ($expression=="") {
 echo "<center><img src='potflow1.png'><br />";
} else {
 $expression0=str_replace("+","z",$expression);
 echo "<center><img src='potflowpng.php?expr=".$expression0."&'><br />";
}
echo $expression."</td></tr>";
echo "<tr><td colspan='2'>";
echo "E.Gs.<br /> <a href='potflow.php?expr=y-4000*y%2F(x^2%2By^2)&Draw=Draw'>Circular Cylinder Flow</a>&nbsp;,&nbsp;&nbsp;";
echo "<a href='potflow.php?expr=y-4000*y%2F(x^2%2By^2)+%2B40.58*ln(x^2+%2B+y^2)&Draw=Draw'>Rotating Cylinder Flow</a>&nbsp;,&nbsp;&nbsp;";
echo "<a href='potflow.php?expr=atan(y%2Fx)&Draw=Draw'>Source Flow</a>&nbsp;,&nbsp;&nbsp;";
echo "<a href='potflow.php?expr=ln(x^2%2By^2)&Draw=Draw'>Vortex Flow</a>&nbsp;,&nbsp;&nbsp;";
echo "<a href='potflow.php?expr=atan(y%2F(x-100))-atan(y%2F(x%2B100))&Draw=Draw'>Source Sink Pair</a>,<br />";
echo "<a href='potflow.php?expr=50*atan((y%2B50)%2Fx)%2B50*atan((y-50)%2Fx)%2By&Draw=Draw'>Imaged Source Pair in Stream (Close)</a>&nbsp;,&nbsp;&nbsp;";
echo "<a href='potflow.php?expr=50*atan((y%2B100)%2Fx)%2B50*atan((y-100)%2Fx)%2By*1.365&Draw=Draw'>Imaged Source Pair in Stream(Far)</a>&nbsp;,&nbsp;&nbsp;";
echo "<a href='potflow.php?expr=x*y&Draw=Draw'>Right Angle Corner Flow</a>&nbsp;,&nbsp;&nbsp;";
echo "<a href='potflow.php?expr=y-0.1x&Draw=Draw'>Inclined Uniform Flow</a>&nbsp;,&nbsp;&nbsp;";
echo "</td></tr></table>";
include "../aero4-footer.php";
