<?php
include("evalmath.class.php");
$m = new EvalMath;

// Add values to the graph
$imgWidth=400;
$imgHeight=400;

// Create image and define colors
$image=imagecreate($imgWidth, $imgHeight);
$colorWhite=imagecolorallocate($image, 255, 255, 255);
$colorRed=imagecolorallocate($image, 205, 0, 0);
$colorGrey1=imagecolorallocate($image, 230, 230, 230);
$colorGrey2=imagecolorallocate($image, 120, 120, 120);
$colorGreen=imagecolorallocate($image, 0, 255, 0);
$colorBlue=imagecolorallocate($image, 0, 0, 205);
$colorBlue2=imagecolorallocate($image, 200, 200, 255);
$colorBlack=imagecolorallocate($image,0,0,0);

// Create a background
imagefilledrectangle($image,0,0,$imgWidth,$imgHeight,$colorGrey1);

$expression0 = htmlspecialchars($_GET["expr"]);
$expression = str_replace("z","+",$expression0);
//
//      REAL*4 ZMESH(101,101),VCONT(51)
//      REAL*4 XMESH(101),YMESH(101),ATEM(128)
//      CHARACTER*1 CH1,BUFSTR(80)
//      CHARACTER*80 BUFF
//      INTEGER*2 IC1,IC2,CMDCNT
//      EQUIVALENCE (BUFSTR,BUFF)
//      COMMON /CEVAL/ BUFSTR,ATEM
 $Xlow=-201.0;
 $Ylow=-201.0;
 $Xhigh=199.0;
 $Yhigh=199.0;
 $expressionf = "f(x,y) = ".$expression;
 $m->evaluate($expressionf);
//
 $i=0;
 while ($i<101) {
  $XMESH[$i]=$Xlow;
  $YMESH[$i]=$Ylow;
  $Xlow=$Xlow+4.0; 
  $Ylow=$Ylow+4.0;
  $i++;
 }
 $j=0;
 while ($j<101) {
  $Y=$YMESH[$j];
  $k=0;
  while ($k<101) {
   $X=$XMESH[$k];
   $val = $m->evaluate("f(".$X.",".$Y.")");
   $ZMESH[$k][$j]=$val;
   $k++;
  }
  $j++;
 }
//      write (*,*) 'Drawing Streamlines'
 $ZMAX=$ZMESH[0][0]; 
 $ZMIN=$ZMAX;
 $i=0;
 while ($i<101) {
  $j=0;
  while ($j<101) {
   if ($ZMESH[$i][$j]>$ZMAX) {
    $ZMAX=$ZMESH[$i][$j];
   } 
   if ($ZMESH[$i][$j]<$ZMIN) {
    $ZMIN=$ZMESH[$i][$j];
   }
   $j++;
  }
  $i++;
 } 
 if ($ZMAX>500000.0) {
  $ZMAX=500000.0;
 }
 if ($ZMIN<-500000.0) {
  $ZMIN=-500000.0;
 }
 $CSTEP=($ZMAX-$ZMIN)/50.0;
 $X0=$ZMIN;
 $i=0;
 while ($i<50) {
  $V[$i]=$X0;
  $X0=$X0+$CSTEP;
  $i++;
 }
 $V[50]=0.0;
//      CALL CONTOUR(XMESH,YMESH,ZMESH,VCONT,ILINE2,ILINE3,ILINE4)
//
//      SUBROUTINE CONTOUR(X,Y,Z,V,IC1,IC2,IC3)
//      REAL*4 X(101),Y(101),Z(101,101),V(51)
//      REAL*4 XT(3),YT(3),VT(3),XR(5),YR(5),VR(5),XP(2),YP(2)
//      INTEGER ITRI(3,4)
//      DATA ITRI/ 1,2,5, 2,3,5, 3,4,5, 4,1,5 /
 $ITRI[0][0]=0;
 $ITRI[1][0]=1;
 $ITRI[2][0]=4;

 $ITRI[0][1]=1;
 $ITRI[1][1]=2;
 $ITRI[2][1]=4;

 $ITRI[0][2]=2;
 $ITRI[1][2]=3;
 $ITRI[2][2]=4;

 $ITRI[0][3]=3;
 $ITRI[1][3]=0;
 $ITRI[2][3]=4;

 $i=0;
 while ($i<100) {
  $YR[0]=$YMESH[$i];
  $YR[1]=$YMESH[$i];
  $YR[2]=$YMESH[$i+1];
  $YR[3]=$YMESH[$i+1];
  $YR[4]=0.25*($YR[0]+$YR[1]+$YR[2]+$YR[3]);
  $j=0;
  while ($j<100) {
   $XR[0]=$XMESH[$j];
   $XR[1]=$XMESH[$j+1];
   $XR[2]=$XMESH[$j+1];
   $XR[3]=$XMESH[$j];
   $XR[4]=0.25*($XR[0]+$XR[1]+$XR[2]+$XR[3]);
   $VR[0]=$ZMESH[$j][$i];
   $VR[1]=$ZMESH[$j+1][$i];
   $VR[2]=$ZMESH[$j+1][$i+1];
   $VR[3]=$ZMESH[$j][$i+1];
   $VR[4]=0.25*($VR[0]+$VR[1]+$VR[2]+$VR[3]);
   $k=0;
   while ($k<4) {
    $n=0;
    while ($n<3) {
     $l=$ITRI[$n][$k];
     $XT[$n]=$XR[$l];
     $YT[$n]=$YR[$l];
     $VT[$n]=$VR[$l];
     $n++;
    }
    $VMAX=$VT[0];
    $VMIN=$VMAX;
    if ($VT[1]>$VMAX) {
     $VMAX=$VT[1];
    } 
    if ($VT[2]>$VMAX) {
     $VMAX=$VT[2];
    } 
    if ($VT[1]<$VMIN) {
     $VMIN=$VT[1];
    }
    if ($VT[2]<$VMIN) {
     $VMIN=$VT[2];
    }
    $n=0;
    while ($n<51) {
     $IPNT=0;
     if ($V[$n]>=$VMIN && $V[$n]<=$VMAX) {
      $found=1;
      if ($V[$n]<$VT[0] && $V[$n]<$VT[1]) {
       $found=0;
      }
      if ($V[$n]>$VT[0] && $V[$n]>$VT[1]) {
       $found=0;
      }
      if ($found==1) {
       $FRAC=$VT[0]-$VT[1];
       if (abs($FRAC)>1.0E-10) {
	$FRAC=($V[$n]-$VT[1])/$FRAC;
       }
       $IPNT=1;
       $XP[0]=$FRAC*($XT[0]-$XT[1])+$XT[1];
       $YP[0]=$FRAC*($YT[0]-$YT[1])+$YT[1];
      } 
      $found=1;
      if ($V[$n]<$VT[2] && $V[$n]<$VT[1]) {
       $found=0;
      }
      if ($V[$n]>$VT[2] && $V[$n]>$VT[1]) {
       $found=0;
      }
      if ($found==1) {
       $FRAC=$VT[2]-$VT[1];
       if (abs($FRAC)>1.0E-10) {
	$FRAC=($V[$n]-$VT[1])/$FRAC;
       }
       $IPNT=$IPNT+1;
       $XP[$IPNT-1]=$FRAC*($XT[2]-$XT[1])+$XT[1];
       $YP[$IPNT-1]=$FRAC*($YT[2]-$YT[1])+$YT[1];
      } 
      if ($IPNT<2) {
       $found=1;
       if ($V[$n]<$VT[2] && $V[$n]<$VT[0]) {
        $found=0;
       }
       if ($V[$n]>$VT[2] && $V[$n]>$VT[0]) {
        $found=0;
       }
       if ($found==1) {
        $FRAC=$VT[2]-$VT[0];
        if (abs($FRAC)>1.0E-10) {
   	 $FRAC=($V[$n]-$VT[0])/$FRAC;
        }
        $IPNT=$IPNT+1;
        $XP[$IPNT-1]=$FRAC*($XT[2]-$XT[0])+$XT[0];
        $YP[$IPNT-1]=$FRAC*($YT[2]-$YT[0])+$YT[0];
       }
      }
      if ($IPNT==2) {
       $Xint0=floor($XP[0]+200.0);
       $Yint0=floor(200.0-$YP[0]);
       $Xint1=floor($XP[1]+200.0);
       $Yint1=floor(200.0-$YP[1]);
       if ($n==50) {
        imageline($image,$Xint0,$Yint0,$Xint1,$Yint1,$colorRed);
       } else {
        imageline($image,$Xint0,$Yint0,$Xint1,$Yint1,$colorBlue);
       } 
      }
     }  
     $n++;
    }
    $k++;
   }
   $j++;
  }
  $i++;
 }   



// Output graph and clear image from memory
header("Content-type: image/png");
imagepng($image);
imagedestroy($image);

?>
