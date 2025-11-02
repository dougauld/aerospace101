<?php
include "../aero4-header.php";
include "perfr.php";
?>
<h1><font color="#800000">Rotor Blade Profile Drag</font></h1>

<p>As well as the power
requirements to create induced velocity and to
climb at a given velocity, additional power is required to over come
the profile drag of the rotor blades as they move within the rotor
disk.</p>
<p align="center"><img src="rotor_disk_view.png" name="Image1" align="center" width="581" height="535" border="0"/>
</p><p>The drag on a small element of a blade will be</p>
<center>
$$ &Delta; D = C_D 1/2 &rho; V_{local}^2 c dr $$
</center>
<p>where <br />
$C_D $ is the local drag coerfficient of the blade aerofoil section<br />
$&rho; $ is the density of the air <br />
$ c $ is the local blade chord length and <br />
$V_{local}$ is the local magnitude of flow velocity at this section
</p>
<p>The
small component of torque to be overcome by the motor will be this
force times the moment arm from the central shaft, $ r $.</p>
<center>
$$ &Delta; Q = C_D 1/2 &rho; V_{local}^2 c r dr $$
</center>
<p>The
local velocity will be due to the blade's angular velocity</p>
<center>
$$ V = &Omega; r $$
</center>
<p>Thus 
</p>
<center> 
$$ &Delta; Q = C_D 1/2 &rho; &Omega;^2 c r^3 dr $$
</center>
<p>If it
is assumed that blade chord and section drag coefficient are roughly constant
from hub to maximum radius, $ R $, then for a rotor disk with $ N $ blades, the total
torque required to overcome profile drag will be</p>
<center>
$$ Q = C_D 1/2 &rho; &Omega;^2 c N &int;_0^R r^3 dr $$
<br />
$$ Q = C_D 1/8 &rho; &Omega;^2 c N R^4 $$ 
</center> 
<p>The
power required to overcome the profile drag will thus be the torque
times the rotation rate.</p>
<center>
$$ P_{profile} = &Omega; Q = C_D 1/8 &rho; c N &Omega;^3 R^4 $$
</center>
<p>The
profile power required will be in addition to the power required to
produce the induced momentum to balance weight and the power required
to climb and is a significant addition to the ideal momentum
calculation.</p>
<center> 
$$ P_{hover} = P_{vi} + P_{profile} $$
<br />
$$ P_{climb} = P_{Vc} + P_{vi} + P_{profile} $$
</center>
<p>For
example, 
</p>
<p align="left" >A
typical 3 bladed helicopter of mass 3000Kg with 16m rotor diameter
will have the following power requirements. 
</p>
<p align="center" ><img src="rotor_power_comparison.png" name="Image2" width="643" height="384" border="0"/>
</p>
<p>
This
agrees well with a blade element analysis of the rotor, although the
comparison diverges for higher climb rates. Higher collective angle
is required for higher climb rates whereas the above profile drag
calculation is based only on an average $ C_D $ for normal
collective angles.</p>
<p align="left" > 
</p>
<p align="left" ><br/>

</p>
</td></tr></table>

<?php
include "../aero4-footer.php";
?>

