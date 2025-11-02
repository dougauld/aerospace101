% Preformance envelope calculation for dhc6 twin otter.
% This is the MATLAB version

clear all;
clf;

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
vals=[0.:1.:50.];
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
  Cl=Weight/(0.5*rho*Vel*Vel*S);
  M=Vel/a;
  if (M<0.99),
% compressibility correction
   beta=sqrt(1.-M*M);
   Cd=(0.02+0.0425*Cl*Cl)/beta;
   D=Cd*(0.5*rho*Vel*Vel*S);
% Specific Excess Power
   Ps(i,j)=Vel/Weight*(Thrust-D);
  else
% program is not set to handle supersonic drag.
   Ps(i,j)=0;
  end;
% if (Cl>Clmax),
%stall boundary     
%  Ps(i,j)=0;
% end;
% Specific Energy
  he(i,j)=alt+Vel*Vel/2./g;
 end;
 %stall boundary
 vstall(i)=sqrt(Weight/Clmax/0.5/rho/S);
end;

% Contour Specific Excess Power and Specific Energy
figure(1);
[c1,h1]=contour(V,h,Ps,vals);
clabel(c1,h1);
title('Specific Excess Power');
xlabel('Velocity (m/s)');
ylabel('Altitude (m)');
hold on;
plot (vstall,h,'-r');
legend('Ps','stall boundary');
%contour(V,h,he,h);

MTOW = 12500;    % lb
empty= 8100;     % lb
fuel = 2500;     % lb of fuel used
ratio3=log(MTOW/(MTOW-fuel));
Winitial = MTOW/2.2*9.8;
Wfinal = Winitial - fuel/2.2*9.8;
TSFC = 0.6/3600.0;
% Contour range
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
  Cl=Weight/(0.5*rho*Vel*Vel*S);
  M=Vel/a;
  if (M<0.99),
%  compressibility correction
   beta=sqrt(1.-M*M);
   Cd=(0.02+0.0425*Cl*Cl)/beta;
   D=Cd*(0.5*rho*Vel*Vel*S);
%  Range
   Range(i,j)=Vel/TSFC*Cl/Cd*ratio3/1000000.0;
  else
   Range(i,j)=0;
  end;
%  if (Cl>Clmax),
%   Range(i,j)=0;
%  end;
 end;
end;

vals=[0.0:0.1:5.];
% Contour Range
figure(2);
[c1,h1]=contour(V,h,Range,vals);
clabel(c1,h1);
title('Range');
xlabel('Velocity (m/s)');
ylabel('Altitude (m)');
hold on;
contour(V,h,Ps,[0.5]);
plot (vstall,h,'-r');
legend('Range (km/1000)','Ps=0.5','stall boundary');
% Contour endurance
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
  Cl=Weight/(0.5*rho*Vel*Vel*S);
  M=Vel/a;
  if (M<0.99),
%  compressibility correction
   beta=sqrt(1.-M*M);
   Cd=(0.02+0.0425*Cl*Cl)/beta;
   D=Cd*(0.5*rho*Vel*Vel*S);
%  Endurance
   endurance(i,j)=1.0/(TSFC*D)*(Winitial-Wfinal)/60.0;
  else
   endurance(i,j)=0;
  end;
%  if (Cl>Clmax),
%   endurance(i,j)=0;
%  end;
 end;
end;

vals=[0.0:20.:500.];
% Contour endurance
figure(3);
[c1,h1]=contour(V,h,endurance,vals);
clabel(c1,h1);
title('Endurance');
xlabel('Velocity (m/s)');
ylabel('Altitude (m)');
hold on;
contour(V,h,Ps,[0.5]);
plot (vstall,h,'-r');
legend('Endurance (min)','Ps=0.5','stall boundary');

k=1;
n=0;
figure(4);
hold on;
hestart=38.0*38.0/2.0/9.8;
k1=0;
clear x;
clear y;
for i=1:81,
 ymin0=10.0;
 xmin0=0.;
 for j=1:81,
  k1=k1+1;
  x(k1)=he(i,j);
  y(k1)=Ps(i,j);
  if y(k1)<0.5,
   y(k1)=0.5;
  end;
  y(k1)=1./y(k1);
  if i==1,
   if x(k1)>hestart,
    if y(k1)<ymin0,
     n=n+1;
     ymin(n)=y(k1);
     xmin(n)=x(k1);
    end;
  end;
 end;
 if y(k1)<ymin0,
  ymin0=y(k1);
  xmin0=x(k1);
 end;
 end;
 if i>1,
  if (ymin0<1.001),
   n=n+1;
   ymin(n)=ymin0;
   xmin(n)=xmin0;
  end; 
 end;
 k=k+1;
 if k==2,
  k=0;
 else
 % plot(x,y);
 end;
end;
% plot flight paths on 1/Ps
figure(4);
plot(x,y);
title('Time to Climb');
xlabel('Energy Height (m)');
ylabel('1/Ps (s/m)');
hold on;
plot(xmin,ymin,'-r');
sum=0.0;
for i=1:(n-1),
 sum=sum+(xmin(i+1)-xmin(i))*(ymin(i+1)+ymin(i))*0.5;
end;

% integral should be time taken (but from where to where???)
display ('Time Taken to get to Ps=1 on minimum time path (min):');
sum=sum/60.0;
sum

