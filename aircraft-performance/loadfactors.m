% Performance envelope calculation for dhc6 twin otter.
% This is the OCTAVE version, remember to do output editing mods to run in MATLAB
% comment in the contour statements to get the desired figures.

gset term post enh color solid "Times" 10
gset out "ps.ps"

clear all;

% Aircraft data and ISA
Weight=5760.0*9.8;
Power_sl=2.0*690.0*745.;
T0=288.;
g=9.8;
R=287.;
S=39.0;
rho0=1.225;
L=0.0065;
P0=101300.;
gamma=1.4;
eff=0.8;
vals=[0.0:2.5:30.0];
Clmax=1.7;

%Altitude and Velocity range
h=[0.:500.:40000.];
V=[0.:1.5:120.];

for i=1:81,
 alt=h(i)*0.3048;
 temp=T0-alt*L;
 press=P0*(temp/T0)^(g/L/R);
 rho=press/(R*temp);
 Power=Power_sl*rho/rho0;
 a=sqrt(gamma*R*temp);
 for j=1:81,
  Vel=V(j);
  Thrust=Power*eff/Vel;
  flag=0;
  for nload=1.0:0.1:5.0; 
   Cl=nload*Weight/(0.5*rho*Vel*Vel*S);
   M=Vel/a;
   if (M<0.99),
% compressibility correction
    beta=sqrt(1.-M*M);
    Cd=(0.02+0.0425*Cl*Cl)/beta;
    D=Cd*(0.5*rho*Vel*Vel*S);
% Specific Excess Power
    Ps=Vel/Weight*(Thrust-D);
   else
    Ps=0;
   end;
   if (flag==1),
    if (Ps<0),
     slope=(nload-n0)/(Ps-Ps0);
     intercept= nload - slope*Ps;
     nmax=Clmax/Weight*0.5*rho*Vel*Vel*S;
     if nmax<intercept,
      intercept=nmax;
     end;
     if intercept<1.001,
      phi=0.0;
     else 
      phi = acos(1.0/intercept);
     end;
     omega = 9.8/Vel*tan(phi); 
     amat(i,j)=omega*180.0/pi;
     flag=2;
    end;
    n0=nload;
    Ps0=Ps;
   end; 
   if (flag==0),
    Ps0=Ps;
    n0=nload;
    flag=1;
   end;
  end;
 end;
end;

% Contour load factor values for max turn rate.
contour(V,h,amat,vals);

