clf;
x=(-10:0.1:10);
a=3.0;
y=x;
for i=1:201,
 for j=1:201,
   r = sqrt(x(j)*x(j)+y(i)*y(i));
   theta = atan2(y(i),x(j));
%      horizontal uniform flow
%   psi(i,j) = y(i);
%  phi(i,j) = x(j);
%   N=21; 
%       uniform flow at angle of attack 10o
%   alpha=10/180*pi;
%   psi(i,j) = y(i)*cos(alpha) - x(j)*sin(alpha);
%   phi(i,j) = x(j)*cos(alpha) + y(i)*sin(alpha);
%   N=21; 
%       source flow
%    psi(i,j)= theta;
%    phi(i,j)= log(r);
%    N=51;
%       vortex flow
%    psi(i,j)= log(r);
%    phi(i,j)= theta;
%    N=101;
%       source in uniform flow
%    psi(i,j)= theta + 0.5*y(i);
%    phi(i,j)= log(r) + 0.5*x(j);
%    N=51;
%       vortex in uniform flow
%    psi(i,j)= log(r) + 0.5*y(i);;
%    phi(i,j)= -theta +0.5*x(j);
%    N=101;
%       source sink pair
%  psi(i,j)=atan2(y(i),(x(j)+a)) - atan2(y(i),(x(j)-a));
%  r1 = sqrt( (x(j)-a)*(x(j)-a) + y(i)*y(i));
%  r2 = sqrt( (x(j)+a)*(x(j)+a) + y(i)*y(i));
%  phi(i,j)=-log(r1) + log(r2);
%  N=101;
%       source sink pair in uniform stream
%  psi(i,j)=atan2(y(i),(x(j)+a)) - atan2(y(i),(x(j)-a)) + y(i)*0.5;
%  r1 = sqrt( (x(j)-a)*(x(j)-a) + y(i)*y(i));
%  r2 = sqrt( (x(j)+a)*(x(j)+a) + y(i)*y(i));
%  phi(i,j)=-log(r1) + log(r2) + x(j)*0.5;
%  N=51;
%       doublet
%   a=0.03;
%  psi(i,j)=(atan2(y(i),(x(j)+a)) - atan2(y(i),(x(j)-a)));
%  r1 = sqrt( (x(j)-a)*(x(j)-a) + y(i)*y(i));
%  r2 = sqrt( (x(j)+a)*(x(j)+a) + y(i)*y(i));
%  phi(i,j)=-log(r1) + log(r2);
%  N=501;
%       doublet in uniform flow = cylinder flow
%   a=0.03;
%   psi(i,j)=(atan2(y(i),(x(j)+a)) - atan2(y(i),(x(j)-a))) + y(i)/100.0;
%   r1 = sqrt( (x(j)-a)*(x(j)-a) + y(i)*y(i));
%   r2 = sqrt( (x(j)+a)*(x(j)+a) + y(i)*y(i));
%   phi(i,j)=-log(r1) + log(r2) + x(j)/100.0;
%  N=501;
%       rotating cylinder flow
%   a=0.03;
%   psi(i,j)=(atan2(y(i),(x(j)+a)) - atan2(y(i),(x(j)-a))) + y(i)/100.0 + log(r)/100.;
%   r1 = sqrt( (x(j)-a)*(x(j)-a) + y(i)*y(i));
%   r2 = sqrt( (x(j)+a)*(x(j)+a) + y(i)*y(i));
%   phi(i,j)=-log(r1) + log(r2) + x(j)/100.0 - theta/100.0;
%  N=501;
%       corner flow
%  psi(i,j)=x(j)*y(i);
%  phi(i,j)=x(j)*x(j)-y(i)*y(i);
%  N=51;
%       cylinder image flow 
%   a=0.03;
%   h=3.2;
%   psi(i,j)=(atan2(y(i)-h,(x(j)+a)) - atan2(y(i)-h,(x(j)-a))) + y(i)/100.0;
%   psi(i,j)=psi(i,j)+(atan2(y(i)+h,(x(j)+a)) - atan2(y(i)+h,(x(j)-a)));
%   r1 = sqrt( (x(j)-a)*(x(j)-a) + (y(i)-h)*(y(i)-h));
%   r2 = sqrt( (x(j)+a)*(x(j)+a) + (y(i)-h)*(y(i)-h));
%   phi(i,j)=-log(r1) + log(r2) + x(j)/100.0;
%   r1 = sqrt( (x(j)-a)*(x(j)-a) + (y(i)+h)*(y(i)+h));
%   r2 = sqrt( (x(j)+a)*(x(j)+a) + (y(i)+h)*(y(i)+h));
%   phi(i,j)=phi(i,j)-log(r1) + log(r2);
%  N=521;
%   r1 = sqrt( (x(j)-a)*(x(j)-a) + y(i)*y(i));
%   r2 = sqrt( (x(j)+a)*(x(j)+a) + y(i)*y(i));
%   phi(i,j)=-log(r1) + log(r2) + x(j)/100.0;
%  N=501;
%       source sink distribution in uniform flow
  a=2.0;
  psi(i,j)=atan2(y(i),(x(j)+a));
  psi(i,j)=psi(i,j) - 0.5*atan2(y(i),(x(j)-a));
  psi(i,j)=psi(i,j) - 0.25*atan2(y(i),(x(j)-2*a));
  psi(i,j)=psi(i,j) - 0.25*atan2(y(i),(x(j)-3*a));
  psi(i,j)=psi(i,j) + 0.5*y(i);
  r1 = sqrt( (x(j)+a)*(x(j)+a) + y(i)*y(i));
  phi(i,j)=log(r1);
  r1 = sqrt( (x(j)-a)*(x(j)-a) + y(i)*y(i));
  phi(i,j)=phi(i,j)-log(r1);
  r1 = sqrt( (x(j)-2*a)*(x(j)-2*a) + y(i)*y(i));
  phi(i,j)=phi(i,j)-0.5*log(r1);
  r1 = sqrt( (x(j)-2*a)*(x(j)-2*a) + y(i)*y(i));
  phi(i,j)=phi(i,j)-0.25*log(r1);
  r1 = sqrt( (x(j)-3*a)*(x(j)-3*a) + y(i)*y(i));
  phi(i,j)=phi(i,j)-0.25*log(r1);
  phi(i,j)=phi(i,j) + 0.5*x(j);
  N=81;
 end;
end;
hold on;
contour(x,y,phi,N,'g');
contour(x,y,psi,N,'b');
contour(x,y,psi,0:100:100,'r');
axis("square")
