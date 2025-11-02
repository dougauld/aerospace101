/* shock tube simulation by Vanja Zecevic 2015.  */
/* length of tube variable DJA 2017 */

var view = {x:800, y:500};
var fps = 60;
var frame_time = 1000/fps;
var ito;
var time;
var running = false;
var P0, P1, T0, T1;
var dom;
var arr_o;
var arr_n;
var wrk;

var canv    = document.getElementById("view_cnv");
var len_set = document.getElementById("len");
var P0_in   = document.getElementById("P0_in");
var P1_in   = document.getElementById("P1_in");
var T0_in   = document.getElementById("T0_in");
var T1_in   = document.getElementById("T1_in");
var R0_in   = document.getElementById("R0_in");
var R1_in   = document.getElementById("R1_in");
var gam0_in = document.getElementById("gam0_in");
var gam1_in = document.getElementById("gam1_in");
var rp_btn  = document.getElementById("rp_btn");
var err_cn  = document.getElementById("err_cn");
canv.setAttribute("width", view.x);
canv.setAttribute("height", view.y);

var ctx = canv.getContext("2d");
var inputs = document.getElementsByClassName("input");
for(var i=0; i<inputs.length; i++) {
  inputs[i].onkeyup = pause_sim;
  inputs[i].onpaste = pause_sim;
}
init_sim();

function init_null(dom) {
  var state = [];
  for(var i=0; i<dom.nx; i++) {
    state.push({ r: 0.0, u: 0.0, e: 0.0, n: 0.0 });
  }
  return state;
}

function init_step(dom, nhp) {
  var state = [];
  for(var i=0; i<dom.nx; i++) {
    if (i<nhp) state.push({
      r: P0/(dom.R0*T0),
      u: 0.0,
      e: P0/(dom.gam0-1),
      n: 0.0});
    else state.push({
      r: P1/(dom.R1*T1),
      u: 0.0,
      e: P1/(dom.gam1-1),
      n: 1.0*P1/(dom.R1*T1)});
  }
  return state;
}

function get_wrk(fld, dom) {
  var o = {};
  o.rho_inv = 1.0/fld.r;
  o.vel = fld.u*o.rho_inv;
  o.N = fld.n*o.rho_inv;
  var eth = fld.e - 0.5*fld.u*fld.u*o.rho_inv;
  o.P = eth*(gamma(o.N, dom)-1);
  o.T = o.P*o.rho_inv*R_inv(o.N, dom);
  o.fr = fld.u;
  o.fu = fld.u*fld.u*o.rho_inv + o.P;
  o.fe = fld.u*(fld.e + o.P)*o.rho_inv;
  o.fn = fld.u*fld.n*o.rho_inv;
  return o;
}

function gamma(N, dom) { return dom.gam0 + N*dom.gamd; }

function R_inv(N, dom) { return 1.0/(dom.R0 + N*dom.Rd); }

function init_wrk(arr, dom) {
  var wrk = [];
  for(var i=0; i<dom.nx; i++) {
    wrk.push(get_wrk(arr[i], dom));
  }
  return wrk;
}

function error(text) { err_cn.innerHTML = "Error: " + text; }

function success() { err_cn.innerHTML = ""; }

function init_sim() {
  var P0_tmp = parseFloat(P0_in.value);
  var P1_tmp = parseFloat(P1_in.value);
  P0 = (P0_tmp + 14.7)*6894.75729;
  P1 = P1_tmp*133.32239;
  T0 = parseFloat(T0_in.value);
  T1 = parseFloat(T1_in.value);
  time = 0.0;
  var R0 = parseFloat(R0_in.value);
  var R1 = parseFloat(R1_in.value);
  var gam0 = parseFloat(gam0_in.value);
  var gam1 = parseFloat(gam1_in.value);
  var len  = parseFloat(len_set.value);
  
  /* Sanity testing.  */
  if (len>10.0 || len<3.0) {
   error(
    "Tube length must be in the range 3 to 10 meters");
    return;
  }
  if (P0_tmp>80) {
    error(
    "Driver Pressure requested higher than Maximum supplied by line (80 PSIG)");
    return;
  }
  if (P0_tmp<0) {
    error(
    "Driver Pressure cannot be negative");
    return;
  }
  if (P1_tmp<100) {
    error(
    "Driven Gas Pressure requested below minimum produced by Vacuum Pump (100 mmHg)");
    return;
  }
  if (P1_tmp>760) {
    error(
    "Driven Gas Pressure cannot be greater than atmospheric");
    return;
  }
  if (P0/P1<7) {
    error(
    "Pressure Ratio specified does not cleanly break diaphragm");
    return;
  }
  if (T0>1000 || T0<1 || isNaN(T0)) {
    error("Bad T0");
    return;
  }
  if (T1>1000 || T1<1 || isNaN(T1)) {
    error("Bad T1");
    return;
  }
  if (R0>10000 || R0<10 || isNaN(R0)) {
    error("Bad R0");
    return;
  }
  if (R1>10000 || R1<10 || isNaN(R1)) {
    error("Bad R1");
    return;
  }
  if (gam0>2 || gam0<=1 || isNaN(gam0)) {
    error("Bad gamma 0");
    return;
  }
  if (gam1>2 || gam1<=1 || isNaN(gam1)) {
    error("Bad gamma 1");
    return;
  }
  success();
  rp_btn.disabled = false;

  if(running) {
    clearTimeout(ito);
    running = false;
    rp_btn.innerHTML = "RUN";
  }

  var nhp = 150;
  dom = {
     l: len,
     nx: 600,
     cfl: 0.99,
     R0: R0,
     R1: R1,
     gam0: gam0,
     gam1: gam1,
  };
  dom.nx = 120*dom.l;
  dom.gamd = dom.gam1 - dom.gam0;
  dom.Rd = dom.R1 - dom.R0;
  dom.dx = dom.l/(dom.nx-1);
  arr_o = init_step(dom, nhp);
  arr_n = init_null(dom);
  wrk = init_wrk(arr_o, dom);
  draw(arr_o, wrk, dom);
}

function pause_sim() {
  if(running) {
    clearTimeout(ito);
    running = false;
    rp_btn.innerHTML = "RUN";
  }
  rp_btn.disabled = true;
}

function rp_sim() {
  if(running) {
    clearTimeout(ito);
    running = false;
    rp_btn.innerHTML = "RUN";
  } else {
    running = true;
    rp_btn.innerHTML = "PAUSE";
    loop(arr_n, arr_o, wrk, dom);
  }
}

function loop(arr_n, arr_o, wrk, dom) {
  var t0 = performance.now();
  update(arr_n, arr_o, wrk, dom);
  update(arr_o, arr_n, wrk, dom);
  draw(arr_o, wrk, dom);
  var t1 = performance.now();
  var spare_time = frame_time - t1 + t0;
  if(spare_time<0.0) spare_time = 0.0;
  ito = setTimeout(function(){loop(arr_n, arr_o, wrk, dom);}, spare_time);
}

function update(arr_n, arr_o, wrk, dom) {
  /* Determine maximum velocity.  */
  var c_tmp;
  var c_max = 0.0;
  for(var i=0; i<dom.nx; i++) {
    c_tmp = Math.sqrt(gamma(wrk[i].N, dom)*wrk[i].P*wrk[i].rho_inv)
      + Math.abs(arr_o[i].u*wrk[i].rho_inv);
    if (c_tmp > c_max) c_max = c_tmp;
  }
  /* Perform update.  */
  var dt = (dom.cfl*dom.dx)/c_max;
  var tx = 0.5*dt/dom.dx;
  time = time + dt;
  for(var i=1; i<(dom.nx-1); i++) {
    arr_n[i].r = 0.5*(arr_o[i+1].r + arr_o[i-1].r) - tx*(wrk[i+1].fr - wrk[i-1].fr);
    arr_n[i].u = 0.5*(arr_o[i+1].u + arr_o[i-1].u) - tx*(wrk[i+1].fu - wrk[i-1].fu);
    arr_n[i].e = 0.5*(arr_o[i+1].e + arr_o[i-1].e) - tx*(wrk[i+1].fe - wrk[i-1].fe);
    arr_n[i].n = 0.5*(arr_o[i+1].n + arr_o[i-1].n) - tx*(wrk[i+1].fn - wrk[i-1].fn);
  }
  /* Boundary condition.  */
  arr_n[0].r        =  arr_n[1].r;
  arr_n[0].u        = -arr_n[1].u;
  arr_n[0].e        =  arr_n[1].e;
  arr_n[0].n        =  arr_n[1].n;
  arr_n[dom.nx-1].r =  arr_n[dom.nx-2].r;
  arr_n[dom.nx-1].u = -arr_n[dom.nx-2].u;
  arr_n[dom.nx-1].e =  arr_n[dom.nx-2].e;
  arr_n[dom.nx-1].n =  arr_n[dom.nx-2].n;
  for(var i=0; i<dom.nx; i++) {
    wrk[i] = get_wrk(arr_n[i], dom);
  }
}

function draw_graph(graph, arr, dom, type, col) {
  /* Title.  */
  ctx.fillStyle = "#000000";
  ctx.font = "10pt sans-serif";
  ctx.textAlign = "left";
  ctx.textBaseline = "top";
  ctx.fillText(graph.title, graph.lmar + 15, graph.vofst);

  /* Draw axes.  */
  ctx.lineWidth = 1.5;
  ctx.lineCap = "round";
  ctx.strokeStyle = "#000000";
  ctx.beginPath();
  /* Draw y-axis.  */
  var plot_top = graph.vofst + graph.tmar;
  var plot_bot = graph.vofst + graph.h - graph.bmar;
  ctx.moveTo(graph.lmar, plot_top);
  ctx.lineTo(graph.lmar, plot_bot);
  /* Draw x-axis.  */
  var y_zero = -graph.min*(graph.h - graph.tmar - graph.bmar)/(graph.max - graph.min);
  var x_axis = plot_bot - y_zero;
  ctx.moveTo(graph.lmar, x_axis);
  ctx.lineTo(graph.l - graph.rmar, x_axis);
  ctx.stroke();

  /* Draw y-tics.  */
  ctx.textAlign = "right";
  ctx.textBaseline = "middle";
  ctx.beginPath();
  ctx.moveTo(graph.lmar - 5, plot_top);
  ctx.lineTo(graph.lmar, plot_top);
  ctx.stroke();
  ctx.beginPath();
  ctx.moveTo(graph.lmar - 5, plot_bot);
  ctx.lineTo(graph.lmar, plot_bot);
  ctx.stroke();
  ctx.fillText(graph.max.toFixed(0), graph.lmar - 10, plot_top);
  ctx.fillText(graph.min.toFixed(0), graph.lmar - 10, plot_bot);
  
  /* Draw x-tic.  */
  ctx.textAlign = "left";
  ctx.textBaseline = "top";
  ctx.beginPath();
  ctx.moveTo(graph.l - graph.rmar, x_axis);
  ctx.lineTo(graph.l - graph.rmar, x_axis + 5);
  ctx.stroke();
  var l_label = dom.l
/*  ctx.fillText("5 m", graph.l - graph.rmar + 10, x_axis);*/
  ctx.fillText(l_label.toFixed(1) + " m", graph.l - graph.rmar +10, x_axis);

  /* Graph data.  */
  ctx.lineWidth = 1.5;
  ctx.lineCap = "round";
  ctx.strokeStyle = col ;
  ctx.beginPath();
  var l_draw = graph.l - graph.lmar - graph.rmar;
  var dx = l_draw/(dom.nx-1);
  var fact = (graph.h - graph.tmar - graph.bmar)/(graph.max - graph.min);
  ctx.moveTo(graph.lmar, x_axis - fact*graph.scale*arr[0][type]);
  for(var i=1; i<dom.nx; i++) {
    ctx.lineTo(graph.lmar + i*dx, x_axis - fact*graph.scale*arr[i][type]);
  }
  ctx.stroke();
}

function draw_tube(pos, arr, dom) {
  ctx.lineWidth = 1.5;
  ctx.strokeStyle = "#000000";
  var plot_top = pos.vofst + pos.tmar;
  var plot_bot = pos.vofst + pos.h - pos.bmar;
  var l_draw = pos.l - pos.lmar - pos.rmar;
  ctx.beginPath();
  ctx.rect(pos.lmar, plot_top, l_draw, pos.h - pos.tmar - pos.bmar);
  /* Fill gradient.  */
  var grad = ctx.createLinearGradient(pos.lmar,0,pos.lmar + l_draw,0);
  //grad.addColorStop(0.0, '#000000');
  //grad.addColorStop(1.0, '#FFFFFF');
  for(var i=0; i<dom.nx; i++) {
    grad.addColorStop(i/(dom.nx-1), "rgb(" + (255*arr[i].P*pos.scale).toFixed(0) + ",0,0)");
  }
  ctx.fillStyle = grad;
  ctx.fill(); 
  ctx.stroke(); 
  ctx.fillStyle = "#000000";

  /* Labels.  */
  var label_x;
  var P_tmp;
  var l_label
  ctx.font = "10pt sans-serif";
  ctx.textAlign = "center";
  ctx.textBaseline = "top";

  label_x = pos.lmar + 1.25/dom.l*l_draw;
  ctx.beginPath();
  ctx.moveTo(label_x, plot_bot);
  ctx.lineTo(label_x, plot_bot + 5);
  ctx.stroke();
  ctx.fillText("Diaphragm", label_x, plot_bot + 10);
  ctx.fillText("(1.25m)", label_x, plot_bot + 26);
  P_tmp = time*1000.0;
  ctx.fillText("Time : " + P_tmp.toFixed(1) + " msec", label_x, plot_bot + 42);
  label_x = pos.lmar + 0.5*l_draw;
  ctx.beginPath();
  ctx.moveTo(label_x, plot_bot);
  ctx.lineTo(label_x, plot_bot + 5);
  ctx.stroke();
  ctx.fillText("Pressure 1", label_x, plot_bot + 10);
  l_label=dom.l*0.5;
/*  ctx.fillText("(2.5m)", label_x, plot_bot + 26); */
  ctx.fillText("(" + l_label.toFixed(2) + ")", label_x, plot_bot + 26);
  P_tmp = 7.50061561e-3*arr[Math.floor(0.5*(dom.nx - 1))].P;
  ctx.fillText(P_tmp.toFixed(0) + " mm Hg", label_x, plot_bot + 42);
  label_x = pos.lmar + 0.75*l_draw;
  ctx.beginPath();
  ctx.moveTo(label_x, plot_bot);
  ctx.lineTo(label_x, plot_bot + 5);
  ctx.stroke();
  ctx.fillText("Pressure 2", label_x, plot_bot + 10);
  l_label=dom.l*0.75;
  ctx.fillText("(" + l_label.toFixed(2) + ")", label_x, plot_bot + 26);
/*  ctx.fillText("(3.75m)", label_x, plot_bot + 26); */
  P_tmp = 7.50061561e-3*arr[Math.floor(0.75*(dom.nx - 1))].P;
  ctx.fillText(P_tmp.toFixed(0) + " mm Hg", label_x, plot_bot + 42);
  label_x = pos.lmar + 1.0*l_draw;
  ctx.beginPath();
  ctx.moveTo(label_x, plot_bot);
  ctx.lineTo(label_x, plot_bot + 5);
  ctx.stroke();
  ctx.fillText("Pressure 3", label_x, plot_bot + 10);
/*  ctx.fillText("(5m)", label_x, plot_bot + 26); */
  l_label=dom.l;
  ctx.fillText("(" + l_label.toFixed(2) + ")", label_x, plot_bot + 26);
  P_tmp = 7.50061561e-3*arr[Math.floor(1.0*(dom.nx - 1))].P;
  ctx.fillText(P_tmp.toFixed(0) + " mm Hg", label_x, plot_bot + 42);
}

function draw(arr, wrk, dom) {
  var P_graph = {
    min: 0.0,
    max: Math.ceil(P0*1.45037e-4*0.1)*10,
    scale: 1.45037e-4,
    h: 120.0,
    l: view.x,
    vofst: 10.0,
    lmar: 60.0,
    rmar: 60.0,
    tmar: 20.0,
    bmar: 15.0,
    title: "Pressure (psi abs)"};

  var vel_graph = {
    min: -400.0,
    max: 400.0,
    scale: 1.0,
    h: 120.0,
    l: view.x,
    vofst: 130.0,
    lmar: 60.0,
    rmar: 60.0,
    tmar: 20.0,
    bmar: 15.0,
    title: "Velocity (m/s)"};

  var t_graph = {
    min: 0.0,
    max: Math.ceil(2.8*T0*0.1)*10,
    scale: 1.0,
    h: 120.0,
    l: view.x,
    vofst: 260.0,
    lmar: 60.0,
    rmar: 60.0,
    tmar: 20.0,
    bmar: 15.0,
    title: "Temperature (K)"};

  var n_graph = {
    min: 0.0,
    max: 1.0,
    scale: 1.0,
    h: 120.0,
    l: view.x,
    vofst: 390.0,
    lmar: 60.0,
    rmar: 60.0,
    tmar: 20.0,
    bmar: 15.0,
    title: "Driven number fraction"};

  var pos = {
    scale: 1.0/P0,
    h: 110.0,
    l: view.x,
    vofst: 390.0,
    lmar: 60.0,
    rmar: 60.0,
    tmar: 20.0,
    bmar: 60.0,
  };
  ctx.clearRect(0, 0, view.x, view.y);
  draw_graph(P_graph, wrk, dom, "P", "#0000CC");
  draw_graph(vel_graph, wrk, dom, "vel", "#006600");
  draw_graph(t_graph, wrk, dom, "T", "#DD0000");
  draw_tube(pos, wrk, dom);

}

