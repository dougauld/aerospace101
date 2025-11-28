<?php
//       program NACA
//       IMPLICIT NONE
//       REAL*8 XU(201),XL(201),YU(201),YL(201),XT(201),YT(201),YTP(201),YTPP(201)
//       REAL*8 phi(201),EPS(201),PSI(201),load,g,f,frac,omxl,omxl1,omxl2,pia,xo,xsv,atp,ali
//       REAL*8 X0(201),Y0(201),DYC(201),e,pi,dx,twopi,amxl,amxl1,amxl2,an,am,xtp,xuc,xll
//       REAL*8 bn,as,cli,chd,clis,cli1,cn,costh,dt,dx1,dx2,dy1,dy2,ap,rat,h,q,y,xym
//       REAL*8 SINTH,TANTH,SF,TANTH0,theta,thick,toc,thp,u,tr,x,v,xl201,xlc,xc,ak2,rnp
//       real*8 ylp,ylc,ymax,ym,yp,ypp,yuc,yup,z1,z2,ycp2,ycmb,ak,yc,a,a1,acrat,z
//       real*8 DIF,COSH,SINH
//       CHARACTER*20 series,section
//       CHARACTER*40 filename,header
//       character*10 NAME(8)
//       character*80 buffer
//       integer*4 numpoint,icky,i,j,itemp,jtemp,it,kf6xa,numx,temp

//       COSH(Z) = 0.5*(EXP(Z)+EXP(-Z))
//       SINH(Z) = 0.5*(EXP(Z)-EXP(-Z))
      
       $file1=fopen("./tmp/section.tmp","r");
       if ($file1) {
        $text=fgets($file1);
        $series=$text;
        $text=fgets($file1);
        $section=$text;
        $text=fgets($file1);
        $numpoint=intval($text);
        $text=fgets($file1);
        $load=floatval($text);
        fclose($file1);
//      
        $section=str_replace(" ","",$section);
        $E  = 1.0e-5;
        $DX = 0.01;
        $TWOPI=pi()*2.0;
        if (($load<0.1) || ($load>0.9999)) {
         $load=0.9999;
        }
        if (($numpoint>121) || ($numpoint<12)) {
         $numpoint=121;
        } 
        if (($numpoint % 2)==0) {
         $numpoint=$numpoint-1;
        }
        $series=str_replace(" ","",$series);
//        echo "series    = ".$series."\n";
//        echo "series[0] = ".$series[0]."\n";          
        if ($series[0]=='4') {
         $itemp=intval($section[0]);
         $am=$itemp*0.01;
         $itemp=intval($section[1]);
         $ap=$itemp*0.1;
         $itemp=intval($section[2]);
         $itemp=$itemp*10+intval($section[3]);
         $thick=$itemp*0.01;
         $numx=floor($numpoint/2)+1;
         $dt=-pi()/($numx-1);
         $theta=pi();
//         echo "AM   : ".$am."\n";
//         echo "AP   : ".$ap."\n";
//         echo "Thick: ".$thick."\n";
         $i=0;
         while ($i<$numx) {
          $X0[$i]=0.5+0.5*cos($theta);
          if ($X0[$i]<$ap) {
           $Y0[$i]=$am/($ap*$ap)*(2.0*$ap*$X0[$i]-$X0[$i]*$X0[$i]);
           $DYC[$i]=$am/($ap*$ap)*(2.0*$ap-2.0*$X0[$i]);
          } else {
           $Y0[$i]=$am/(1.0-$ap)/(1.0-$ap)*((1.0-2.0*$ap)+2.0*$ap*$X0[$i]-$X0[$i]*$X0[$i]);
           $DYC[$i]=$am/(1.0-$ap)/(1.0-$ap)*(2.0*$ap-2.0*$X0[$i]);
          }
          $YT[$i]=$thick/0.2*(0.29690*sqrt($X0[$i])-0.126*$X0[$i]-0.3516*$X0[$i]*$X0[$i]+0.2843*$X0[$i]*$X0[$i]*$X0[$i]-0.1015*$X0[$i]*$X0[$i]*$X0[$i]*$X0[$i]);
          $theta=$theta-$dt;
          $i++;
         }
        }   
       
        if ($series[0]=="5") {
         $itemp=intval($section[0]);
         $itemp=$itemp*10+intval($section[1]);
         $jtemp=intval($section[2]);
         $ap=0.0;
         $am=0.0;
         $ak=0.0;
         $ak2=0.0;
         if ($itemp==21) { 
          $ap=0.05;
          $am=0.058;
          $ak=361.4;
          $ak2=0.0;
         }
         if ($itemp==22) { 
          $ap=0.10;
          $am=0.126;
          $ak=51.64;
          if ($jtemp==1) {
           $am=0.130;
           $ak=51.99;
           $ak2=0.000764;
          }
         }
         if ($itemp==23) {
          $ap=0.15;
          $am=0.2025;
          $ak=15.957;
          if ($jtemp==1) {
           $am=0.2170;
           $ak=15.793;
           $ak2=0.006770;
          }
         }
         if ($itemp==24) { 
          $ap=0.20;
          $am=0.29;
          $ak=6.643;
          if ($jtemp==1) {
           $am=0.3180;
           $ak=6.520;
           $ak2=0.0303;
          } 
         }
         if ($itemp==25) { 
          $ap=0.25;
          $am=0.391;
          $ak=3.23;
          if ($jtemp==1) {
           $am=0.4410;
           $ak=3.191;
           $ak2=0.1355;
          } 
         }
         $itemp=intval($section[3]);
         $itemp=$itemp*10+intval($section[4]);
         $thick=$itemp*0.01;
         $numx=floor($numpoint/2)+1;
         $dt=-pi()/($numx-1.0);
         $theta=pi();
         $j=0;
         while ($j<$numx) {
          $X0[$j]=0.5+0.5*cos($theta);
          $theta=$theta-$dt;
          if ($X0[$j]<$ap) {
           if ($jtemp==0) {
            $Y0[$j]=1./6.*$ak*($X0[$j]*$X0[$j]*$X0[$j]-3.0*$am*$X0[$j]*$X0[$j]+$am*$am*(3.0-$am)*$X0[$j]);
            $DYC[$j]=1./6.*$ak*(3.0*$X0[$j]*$X0[$j]-6.0*$am*$X0[$j]+$am*$am*(3.0-$am));
           } else {
            $Y0[$j]=($X0[$j]-$am)*($X0[$j]-$am)*($X0[$j]-$am);
            $Y0[$j]=$Y0[$j]-$ak2*(1.0-$am)*(1.0-$am)*(1.0-$am)*$X0[$j]-$am*$am*$am*$X0[$j]+$am*$am*$am;
            $Y0[$j]=1./6.*$ak*$Y0[$j];
            $DYC[$j]=3.0*($X0[$j]-$am)*($X0[$j]-$am);
            $DYC[$j]=1./6.*$ak*($DYC[$j]-$ak2*(1.0-$am)*(1.0-$am)*(1.0-$am)-$am*$am*$am);
           }
          } else { 
           if ($jtemp==0) {
            $Y0[$j]=1./6.*$ak*$am*$am*$am*(1.0-$X0[$j]);
            $DYC[$j]=-1./6.*$ak*$am*$am*$am;
           } else { 
            $Y0[$j]=$ak2*($X0[$j]-$am)*($X0[$j]-$am)*($X0[$j]-$am);
            $Y0[$j]=$Y0[$j]-$ak2*(1.0-$am)*(1.0-$am)*(1.0-$am)*$X0[$j]-$am*$am*$am*$X0[$j]+$am*$am*$am;
            $Y0[$j]=1./6.*$ak*$Y0[$j];
            $DYC[$j]=$ak2*3.0*($X0[$j]-$am)*($X0[$j]-$am);
            $DYC[$j]=1./6.*$ak*($DYC[$j]-$ak2*(1.0-$am)*(1.0-$am)*(1.0-$am)-$am*$am*$am);
           }
          }
          $YT[$j]=$thick/0.2*(0.29690*sqrt(abs($X0[$j]))-0.126*$X0[$j]-0.3516*$X0[$j]*$X0[$j]+0.2843*$X0[$j]*$X0[$j]*$X0[$j]-0.1015*$X0[$j]*$X0[$j]*$X0[$j]*$X0[$j]);
          $j++;
         } 
/*
         $j=1;
         while ($j<$numx-1) {
          $DX1=$X0[$j]-$X0[$j-1];
          $DY1=$Y0[$j]-$Y0[$j-1];
          $DX2=$X0[$j+1]-$X0[$j];
          $DY2=$Y0[$j+1]-$Y0[$j];
          $DYC[$j]=($DY1/$DX1 + $DY2/$DX2)*0.5;
          $j++;
         }
*/         
        }
        if (($series[0]=='4') || ($series[0]=='5')) {
         $j=0;
         while ($j<$numx) {
          $theta=atan($DYC[$j]);
          $XU[$j]=$X0[$j]-SIN($theta)*$YT[$j];
          $YU[$j]=$Y0[$j]+COS($theta)*$YT[$j];
          $XL[$j]=$X0[$j]+SIN($theta)*$YT[$j];
          $YL[$j]=$Y0[$j]-COS($theta)*$YT[$j];
          $j++;
         } 
        }
        
        if ($series[0]=='6') {
//       6 series sections from.....
//       from NASA TM X-3069, September 1974
//       by Ladson, C.L., and Brooks, C.W., Jr.
//      ku ported to the Mac by W.H. Mason, March 1991
//       ported to Linux D.Auld June 2006
//       phped  D.Auld 2021
         $TOC=0.01;
         $A=$load;
         $numx=floor($numpoint/2)+1;         
         if ($A<1.0e-5) {
          $A=1.0;
         } 
         $i=0;
//         echo " Section :".$section."\n";
         $length=strlen($section);
         while ($i<$length) {
          $j=$length-$i-1;
          if (($section[$j]>="0") && ($section[$j]<="9")) {
           $TOC = intval($section[$j-1]);
           $TOC = $TOC*10.0 + intval($section[$j]);
           $TOC = $TOC/100.0;
           $i=$length;
          }
          $i++;
         } 
//         echo " TOC : ".$TOC."\n";
         $CLI=0.0;
         $i=0;
         while ($i<$length) {
          if (($section[$i]=="-") || ($section[$i]=="A")) {
           if ($section[$i+1]=="-") {
            $temp=intval($section[$i+2]);
           } else { 
            $temp=intval($section[$i+1]);
           }
           $CLI=$temp*0.1;
           $i=$length;
          }
          $i++;
         }
         $KF6XA = 0;
         $FRAC  = 1.0;
         $CLIS  = $CLI;
         $AS    = $A;
         $A1    = $A;
         $CLI1  = $CLI;
         $X     = 0.0;
         $Y     = 0.0;
         $XC    = 0.0;
         $YC    = 0.0;
         $XU0[0] = 0.0;
         $YU0[0] = 0.0;
         $XL0[0] = 0.0;
         $YL0[0] = 0.0;
         $U     = 0.005;
         $V     = -($A1-$U)/abs($A1-$U);
         $OMXL  = (1.0-$U)*log(1.0-$U);
         $AMXL  = ($A1-$U)*log(abs($A1-$U));
         $OMXL1 = -log(1.0-$U)-1.0;
         $AMXL1 = -log(($A1-$U))+$V;
         $OMXL2 = 1.0/(1.0-$U);
         $AMXL2 = -$V/abs($A1-$U);
         if (($A1>$E) && (abs(1.0-$A1)>$E)) {
          $G  = -($A1*$A1*(0.5*log($A1)-0.25)+0.25)/(1.0-$A1);
          $Q  = 1.0;
          $H  = (0.5*(1.0-$A1)^2*log(1.0-$A1)-0.25*(1.-$A1)^2)/(1.-$A1)+$G;
          $Z  = 0.5*($A1-$U)*$AMXL-0.5*(1.0-$U)*$OMXL-0.25*($A1-$U)^2+0.25*(1.0-$U)^2;
          $Z1 = 0.5*(($A1-$U)*$AMXL1-$AMXL-(1.0-$U)*$OMXL1+$OMXL+($A1-$U)-(1.0-$U));
          $Z2 = 0.5*($A1-$U)*$AMXL2-$AMXL1-0.5*(1.0-$U)*$OMXL2+$OMXL1;
         } 
         if ($A1<$E) {
          $H  = -0.5;
          $Q  = 1.0;
          $Z1 = $U*log($U)-0.5*$U-0.5*(1.0-$U)*$OMXL1+0.5*$OMXL-0.5;
         } elseif (abs($A1-1.0)<$E) { 
          $H  = 0.0;
          $Q  = $H;
          $Z1 = -$OMXL1;
         } else {
          $H  = -0.5;
          $Q  = 1.0;
          $Z1 = $U*log($U)-0.5*$U-0.5*(1.0-$U)*$OMXL1+0.5*$OMXL-0.5;
         }
//         echo "CLI1,A1,Q,H,U :".$CLI1.":".$A1.":".$Q.":".$H.":".$U."\n";
         $TANTH0=$CLI1*($Z1/(1.0-$Q*$A1)-1.0-log($U)-$H)/pi()/($A1+1.0)/2.0;
         $YP       = 10.^10;
         $YPP      = 10.^10;
//         $YUP      = -1.0/$TANTH0;
//         $YLP      = -1.0/$TANTH0;
         $i        = 0;
         $X        = 0.00025;
         $XMAX = 0.0;
         $XMIN = 0.0;
         while ($X<=1.0) {
          if ($i==0) {
//           SELECT SERIES
           $partial=substr($section,1,2);
           switch ($partial) {
             case "3-" : $filename="PEP63.txt"; $numpnt=201; break;
             case "4-" : $filename="PEP64.txt"; $numpnt=201; break;
             case "5-" : $filename="PEP65.txt"; $numpnt=251; break;
             case "6-" : $filename="PEP66.txt"; $numpnt=201; break;
             case "7-" : $filename="PEP67.txt"; $numpnt=251; break;
             case "3(" : $filename="PEP63.txt"; $numpnt=201; break;
             case "4(" : $filename="PEP64.txt"; $numpnt=201; break;
             case "5(" : $filename="PEP65.txt"; $numpnt=251; break;
             case "6(" : $filename="PEP66.txt"; $numpnt=201; break;
             case "7(" : $filename="PEP67.txt"; $numpnt=251; break;
             case "3A" : $filename="PEP63A.txt"; $numpnt=251; break;
             case "4A" : $filename="PEP64A.txt"; $numpnt=251; break;
             case "5A" : $filename="PEP65A.txt"; $numpnt=251; break;
           }
           $file2=fopen($filename,"r");
           $n=0;
           while ($n<$numpnt) {
            fscanf($file2," %f  %f ",$x00,$y00);
            $PHI[$n]=$x00;
            $EPS[$n]=$y00;
//            echo " PHI[".$n."],EPS[".$n."] = ".$x00.", ".$y00."\n";
            $n++;
           }
           fclose($file2);
           switch ($partial) {
             case "3-" : $filename="PPS63.txt"; break;
             case "4-" : $filename="PPS64.txt"; break;
             case "5-" : $filename="PPS65.txt"; break;
             case "6-" : $filename="PPS66.txt"; break;
             case "7-" : $filename="PPS67.txt"; break;
             case "3(" : $filename="PPS63.txt"; break;
             case "4(" : $filename="PPS64.txt"; break;
             case "5(" : $filename="PPS65.txt"; break;
             case "6(" : $filename="PPS66.txt"; break;
             case "7(" : $filename="PPS67.txt"; break;
             case "3A" : $filename="PPS63A.txt"; break;
             case "4A" : $filename="PPS64A.txt"; break;
             case "5A" : $filename="PPS65A.txt"; break;
           }
           $file2=fopen($filename,"r");
           $n=0;
           while ($n<$numpnt) {
            fscanf($file2," %f  %f ",$x00,$y00);
            $PHI0[$n]=$x00;
            $PSI0[$n]=$y00;
//            echo " PHI[".$n."],PSI[".$n."] = ".$x00.", ".$y00."\n";
            $n++;
           }
           fclose($file2);
           $n=0;
           while ($n<$numpnt) {
            $x00=$PHI[$n];
            if (($PHI0[$n]-$x00)<$E) {
             $PSI[$n]=$PSI0[$n];
            } else {
             $PSI[$n]=FTLUP($numpnt,$x00,$PHI0,$PSI0);
            }
//            echo "PSI[".$n."] = ".$PSI[$n]; 
//            echo " or ".$PSI[$n]."\n";
            $n++;
           }
//           IF (section(1:3).EQ.'64A') CALL PEP64A (phi,EPS)
//           IF (section(1:3).EQ.'65A') CALL PEP65A (phi,EPS)
           $RAT= 1.0;
           $IT = 0;
//           LOOP START FOR THICKNESS ITERATION
           while($IT<11) {
            $IT=$IT+1;
//           write(6,510) IT,RAT
            $YMAX=0.0;
            $XYM=0.0;
            $j=0;
            while ($j<$numpnt) {
             $temp=$PSI[$j]*$RAT;
             $COSH=0.5*(exp($temp)+exp(-$temp));
             $XT[$j] = -2.0*$COSH*cos($PHI[$j]-$EPS[$j]*$RAT);
             $SINH=0.5*(exp($temp)-exp(-$temp));
             $YT[$j] = 2.0*$SINH*sin($PHI[$j]-$EPS[$j]*$RAT);
//             echo " XT[".$j."],YT[".$j."] = ".$XT[$j].", ".$YT[$j]."\n";
             if ($YT[$j]>$YMAX) {
              $XYM = $XT[$j];
              $YMAX = $YT[$j];
             }
             $j++;
            } 
            $XTP=1.0;
            $j=1;
            $YTP[0]=10.0**6;
            while ($j<$numpnt) {
             $YTP[$j] = DIF($j,5,$numpnt,$XT,$YT);
//             echo " YTP[".$j."] = ".$YTP[$j]."\n";
             if ($j>=2) {
              if (($YTP[$j]<0.0) && ($YTP[$j-1]>0.0)) {
               $XTP=$XT[$j-1]+$YTP[$j-1]*($XT[$j]-$XT[$j-1])/($YTP[$j-1]-$YTP[$j]);
              }
             }
             $j++;
            }
            $YM=FTLUP($numpnt,$XTP,$XT,$YT);
//            echo " XTP,YM ".$XTP.", ".$YM."\n";
            $j=1;
            while ($j<$numpnt) {
             $YTPP[$j]  = DIF($j,5,$numpnt,$XT,$YTP);
             $j++;   
            } 
            $YTPP[0] = 10.0**6;
            $XO = $XT[0];
            $XL201 = $XT[$numpnt-1];
            $TR = 2.0*$YM/($XL201-$XO);
            if ($TOC<$E) {
             $IT=20;
            } else {
//            TEST THICKNESS
             $RAT = $TOC/$TR;
             if (abs($RAT-1.0)<0.0001) {
              $IT=20;
             }
            }
           } 
           $SF=$RAT;
           if ($TOC<$E) {
            $SF=0.0;
           }
           if($i==0) {   
            $j=0;
            while ($j<$numpnt) {
             $XT[$j] = ($XT[$j]-$XO)/($XL201-$XO);
//            SCALE LINEARLY TO EXACT THICKNESS
             $YT[$j]   = $SF*$YT[$j]/($XL201-$XO);
             $YTP[$j]  = $SF*$YTP[$j];
             $YTPP[$j] = $SF*$YTPP[$j]*($XL201-$XO);
             $j++;
            } 
           }
           $XTP     = ($XTP-$XO)/($XL201-$XO);
           $YMAX    = $YMAX*$SF/($XL201-$XO);
           $YM      = $YM*$SF/($XL201-$XO);
           $XYM     = ($XYM-$XO)/($XL201-$XO);
           $XL0[0]   = 0.0;
           if ($TOC>$E) {
//                FIT TILTED ELLIPSE AT ELEVENTH PROFILE POINT
            $CN = 2.0*$YTP[10]-$YT[10]/$XT[10]+0.1;
            $AN = $XT[10]*($YTP[10]*$XT[10]-$YT[10])/($XT[10]*(2.0*$YTP[10]-$CN)-$YT[10]);
            $BN = sqrt(($YT[10]-$CN*$XT[10])**2/(1.0-($XT[10]-$AN)**2/$AN**2));
            $j=0;
            while ($j<10) {
             $YT[$j] = $BN*sqrt(1.0-($XT[$j]-$AN)**2/$AN**2)+$CN*$XT[$j];
             if ($XT[$j]>$E) {
              $YTP[$j]   = $BN**2*($AN-$XT[$j])/$AN**2/($YT[$j]-$CN*$XT[$j])+$CN;
              $YTPP[$j]  = -$BN**4/$AN**2/($YT[$j]-$CN*$XT[$j])**3;
             }
             $j++;
            } 
            $RNP=$BN**2/$AN;
           }
           $ALI = abs($CLI);
           $X = 0.00025;
           $XL0[0] = 0.0;
          } 
//          INTERPOLATE FOR THICKNESS AND DERIVATIVES AT DESIRED VALUES OF X
          $Y=FTLUP($numpnt,$X,$XT,$YT);
          $YP=FTLUP($numpnt,$X,$XT,$YTP);
          $YPP=FTLUP($numpnt,$X,$XT,$YTPP);
//          COMPUTE CAMBERLINE
          $A   = $AS;
          $CLI = $CLIS;
          $A1  = $A;
          $CLI1 = $CLI;
          $XC   = $X;
          $YC   = $Y;
          $XLL  = $X*log($X);
          $Q    = 1.0;
          if ((abs(1.0-$A1)<$E) && (abs(1.0-$X)<$E)) {
           $G  = 0.0;
           $H  = $G;
           $Q  = $G;
           $Z  = 0.0;
           $Z1 = -10.0**10;
           $Z2 = -10.0**10;
          } elseif (($A1<$E) && ((1.0-$X)<$E)) {
           $G  = -0.25;
           $H  = -0.5;
           $Q  = 1.0;
           $Z  = -0.25;
           $Z1 = 0.0;
           $Z2 = -10.0**10;
          } elseif (abs($A1-$X)<$E) {
           $Z  = -0.5*(1.0-$X)**2*log(1.0-$X)+0.25*(1.0-$X)**2;
           $Z1 = -0.5*(1.0-$X)*(-log(1.0-$X)-1.0)+0.5*(1.0-$X)*log(1.0-$X)-0.5*(1.0-$X);
           $Z2 = -log(1.0-$X)-0.5;
           $G  = -($A1**2*(0.5*log($A1)-0.25)+0.25)/(1.0-$A1);
           $H  = (0.5*(1.0-$A1)**2*log(1.0-$A1)-0.25*(1.0-$A1)**2)/(1.0-$A1)+$G;
          } elseif (abs(1.0-$X)<$E) {
           $G  = -($A1**2*(0.5*log($A1)-0.25)+0.25)/(1.0-$A1);
           $H  =  (0.5*(1.0-$A1)**2*log(1.0-$A1)-0.25*(1.0-$A1)**2)/(1.0-$A1)+$G;
           $Z  =  0.5*($A1-1.0)**2*log(abs($A1-1.0))-0.25*($A1-1.0)**2;
           $Z1 = -($A1-1.0)*log(abs($A1-1.0));
           $Z2 = -10.0**10;
          } elseif (abs($A1-1.0)<$E) {
           $G  = 0.0;
           $H  = G;
           $Q  = G;
           $Z  = -(1.0-$X)*log(1.0-$X);
           $Z1 = log(1.0-$X)+1.0;
           $Z2 = -1.0/(1.0-$X);
          } else { 
           $V     = -($A1-$X)/abs($A1-$X);
           $OMXL  =  (1.0-$X)*log(1.0-$X);
           $AMXL  =  ($A1-$X)*log(abs($A1-$X));
           $OMXL1 = -log(1.0-$X)-1.0;
           $AMXL1 = -log(abs($A1-$X))-1.0;
           $OMXL2 =  1.0/(1.0-$X);
           $AMXL2 =  1.0/($A1-$X);
           $Z  = 0.5*($A1-$X)*$AMXL-0.5*(1.0-$X)*$OMXL-0.25*($A1-$X)**2+0.25*(1.0-$X)**2;
           $Z1 = 0.5*(($A1-$X)*$AMXL1-$AMXL-(1.0-$X)*$OMXL1+$OMXL+($A1-$X)-(1.0-$X));
           $Z2 = 0.5*($A1-$X)*$AMXL2-$AMXL1-0.5*(1.0-$X)*$OMXL2+$OMXL1;
           if ($A1<=$E) { 
            $G  = -0.25;
            $H  = -0.50;
           } else {           
            $G  = -($A1*$A1*(0.5*log($A1)-0.25)+0.25)/(1.0-$A1);
            $H  =  (0.5*(1.0-$A1)**2*log(1.0-$A1)-0.25*(1.0-$A1)**2)/(1.0-$A1)+$G;
           } 
          } 
          $YCMB  = $CLI1*($Z/(1.0-$Q*$A1)-$XLL+$G-$H*$X)/pi()/($A1+1.0)/2.0;
          $XSV   = $X;
          if ($X<0.005) {
           $X=0.005;
          } 
          $TANTH = $CLI1*($Z1/(1.0-$Q*$A1)-1.0-log($X)-$H)/pi()/($A1+1.0)/2.0;
          $X=$XSV;
          if ($KF6XA==1) {
           $TANTH=-5.0;
          }
          if ($X<=0.005) {
           $YCP2  = 0.0;
          } else {
           if (abs(1.0-$X)<=$E) {
            $YCP2 = 1.0/$E;
           } else {
            $PIA= pi()*($A1+1.0)*2.0;
            $YCP2 = $CLI1*($Z2/(1.0-$Q*$A1)-1.0/$X)/$PIA;
           }
          }  
//        MODIFIED CAMBERLINE OPTION
          $partial=substr($section,0,3);
          if (($partial=="63A") || ($partial=="64A") || ($partial=="64A") || ($partial=="65A")) {
           $YCMB=$YCMB*0.97948;
           $TANTH=$TANTH*0.97948;
           $YCP2=$YCP2*0.97948;
           $A1 = 0.8;
           $CLI1 = $CLI;
//          WRITE (*,*) '<BR>MODIFIED CAMBERLINE ONLY FOR A=0.8<BR>'
           if ($TANTH<=-0.24521*$CLI1) {
            $YCMB  =  0.24521*$CLI1*(1.0-$X);
            $YCP2  =  0.0;
            $TANTH = -0.24521*$CLI1;
            $KF6XA =  1;
           }
          }
          $F = sqrt(1.0+$TANTH**2);
          $THP = $YCP2/$F**2;
          $SINTH = $TANTH/$F;
          $COSTH = 1.0/$F;
//          CAMBERLINE AND DERIVATIVES COMPUTED
//          COMBINE THICKNESS DISTRIBUTION AND CAMBERLINE
          $XU0[$i]  = $X - $Y*$SINTH;
          if ($XU0[$i]>$XMAX) {
           $XMAX=$XU0[$i];
          }
          if ($XU0[$i]<$XMIN) {
           $XMIN=$XU0[$i];
          }
          $YU0[$i]  = $YCMB + $Y*$COSTH;
          $XL0[$i]  = $X + $Y*$SINTH;
          if ($XL0[$i]>$XMAX) {
           $XMAX=$XU0[$i];
          }
          if ($XL0[$i]<$XMIN) {
           $XMIN=$XU0[$i];
          }
          $YL0[$i]  = $YCMB - $Y*$COSTH;  
          $i=$i+1;
          if ($ALI>$E) {
//           FIND LOCAL SLOPE OF CAMBERED PROFILE
//           $YUP=($TANTH*$F+$YP-$TANTH*$Y*$THP)/($F-$YP*$TANTH-$Y*$THP);
//           $YLP=($TANTH*$F-$YP+$TANTH*$Y*$THP)/($F+$YP*$TANTH+$Y*$THP);
          } 
//           FIND X INCREMENT
          if ($X<=0.09750) {
           $FRAC = 0.250;
          }
          if ($X<=0.01225) {
           $FRAC = 0.025;
          }
//         INCREMENT X AND RETURN TO START OF X LOOP
          $X = $X + $FRAC*$DX;
          $FRAC = 1.0;
         } 
         $numtotal=$i;
         $i=0;
         $dt=-pi()/($numx-1);
         $theta=-pi();
         $CHORD=$XMAX-$XMIN;
         $XCENTRE=($XMAX+$XMIN)*0.5;
         while ($i<$numx) {
          $X=$XCENTRE+$CHORD*0.5*cos($theta);
          $Y=FTLUP($numtotal,$X,$XU0,$YU0);
          $XU[$i]=$X;
          $YU[$i]=$Y;
          $Y=FTLUP($numtotal,$X,$XL0,$YL0);
          $XL[$i]=$X;
          $YL[$i]=$Y;
          $theta=$theta+$dt;
          $i++;
         } 
        }
  
//      create section file
        $file2=fopen("./tmp/section1.tmp","w");
        if ($file2) {
         $outtext=2*$numx."\n";
         fwrite($file2,$outtext);
         $i=0;
         while ($i<$numx) {
          $outtext=$XU[$i]."  ".$YU[$i]."\n";
          fwrite($file2,$outtext);
          $i++;
         }
         $i=0;
         while ($i<$numx) {
          $j=$numx-$i-1;
          $outtext=$XL[$j]."  ".$YL[$j]."\n";
          fwrite($file2,$outtext);
          $i++;
         }
         fclose($file2);
        } else {
         echo " Cannot OPEN output file \n";
        } 
       }

       function FTLUP($n,$x,$vari,$vard) {
//       implicit REAL*8 (A-H,O-Z)
//       real*8 vari(n), vard(n), v(3), yy(2), unit
//       integer*4 ii(43)
 
//       initialize all interval pointers to -1.0  for monotonicity check
//       data (ii(j),j=1,43)/43* -1/
        $ma=1;
        $unit=1.0;
//       assign interval pointers for given vari table
//       the same pointer will be used on a given vari table every time
//        $n=201;
        $i=0;
        $j=0;
        $found=0;
        while ($i<($n-1)) {
         $test=($vari[$i]-$x)*($vari[$i+1]-$x);
         if ($test<0.0) { 
          $found=1;
          $j=$i;
          $i=$n;
         }
         $i++;
        }
        if ($found==0) {    
//         if x outside endpoints, extrapolate from end interval
         if ($x<$vari[0]) {
          $j=0;
         } else {
          $j=$n-2;
         }
        }      
//        first order
//        echo " ...j,x,xi,xd,y ".$j.", ".$x.", ".$vari[$j].", ".$vard[$j].", ";
        $y = ($vard[$j]*($vari[$j+1]-$x)-$vard[$j+1]*($vari[$j]-$x))/($vari[$j+1]-$vari[$j]);
//        echo $y."\n";
        return $y;
       }


       function DIF($L,$M,$NP,$VARI,$VARD) {
//!     *** DOCUMENT DATE 08-01-68   SUBROUTINE REVISED 08-01-68 *********
//!        THIS FUNCTION SUBPROGRAM FINDS THE DERIVATIVE AT A GIVEN POINT,
//!        L, FOR THE DESIRED X AND Y IN A GIVEN TABLE.  THE N-POINT
//!        LAGRANGIAN FORMULA IS USED WHERE N IS ODD.
//!        L = INTEGER, THE POINT OF X AND Y AT WHICH DERIVATIVE IS FOUND
//!        M = INTEGER, 1-5, TO DETERMINE THE POINT FORMULA, N.  N=2*M+1
//!        NP= INTEGER, THE NUMBER OF POINTS IN TABLE OF VARIABLES
//!        VARI = ARRAY OF INDEPENDENT VARIABLE, X.  VARI(NP)
//!        VARD = ARRAY OF DEPENDENT VARIABLE, Y.    VARD(NP)
//      DIF=O17770000000000000000
//!     FOR NOS SYSTEM, PREVIOUS CARD MUST BE REPLACED BY:
        if ($M<1) {
         $DIF=0.0;
        } else {
         $N=2*$M+1;
         IF (($M>5) || ($N>$NP)) {
          $DIF=0.0;
         } else {
          $M1=$M+1;
          $M2=$NP-$M+1;
          $K=$L;
          if (($L>$M1) && ($N!=$NP)) { 
           $K=$M1;
           if ($L>=$M2) {
            $K=$L-($NP-$N);
           }
          }  
          $MX=$L-$K;
          $j=0;
          while ($j<$N) {
           $MJ=$MX+$j;
           $X[$j]=$VARI[$MJ];
           $Y[$j]=$VARD[$MJ];
           $j++;
          }
         
          $A=1.0;
          $B=0.0;
          $C=0.0;
          $j=0;
          while ($j<$N) {
           if ($j!=$K) {
            $P=1.0;
            $i=0;
            while ($i<$N) {
             if ($i!=$j) {
              $P=$P*($X[$j]-$X[$i]);
             } 
             $i++;
            }
            $T=$X[$K]-$X[$j];
            $B=$B+$Y[$j]/($P*$T);
            $A=$A*$T;
            $C=$C+1.0/$T;
           } 
           $j++;
          } 
          $DIF=$A*$B+$Y[$K]*$C;
         }
        }  
        return $DIF;
       }   

?>
	
