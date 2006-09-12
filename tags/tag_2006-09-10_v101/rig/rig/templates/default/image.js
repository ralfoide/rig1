//-------------------------
/* $Id$ */
//-------------------------

//------------------
function RA_Init() {
	try {
		RA_SetContent("debug", "Debug Zone -- Started.<br>");
	    //RA_changeVisibility("settings", false);
	    RA_Reposition();
	    RA_SetEvents();
	} catch(e) {
		RA_Debug("Exception", e);
	}
}

//-------------------------
function RA_GetElem(name) {
  var o = document.getElementById(name);
  return o;
}

//----------------------------------
function RA_SetContent(name, text) {
  var o = RA_GetElem(name);
  if (o)
	o.innerHTML = text;
}

//----------------------------------
function RA_AddContent(name, text) {
  var o = RA_GetElem(name);
  if (o)
	o.innerHTML += text;
}

//-----------------------------
function RA_Debug(info, text) {
	RA_AddContent("debug", "<pre>" + info + ":\n" + text + "</pre>");
}

//-----------------------
function RA_SetEvents() {
}

//---------------------------------
function RA_Reposition() {
	
	// get window total height/width
	// cf http://www.howtocreate.co.uk/tutorials/index.php?tut=0&part=16
	var ww = window.innerWidth;
	var wh = window.innerHeight;
	// TBDL: account for actual borders, margins and paddings (currently hacked manually)

	var i = RA_GetElem("content-img");
	var borders=2+10+5+40; // 40 is just to make it work
	i.style.width  = (ww-borders)+"px";
	i.style.height = (wh-borders)+"px";
	
	RA_Debug("i", i);
	RA_Debug("size", "ww="+ww+", wh="+wh);

	var n = RA_GetElem("next");
	nw = n.clientWidth;
	borders=30;
	n.style.left = (ww-nw-borders)+"px";

	RA_Debug("n", n);
	RA_Debug("size", "nw="+nw+", nsl="+ww-nw-borders);
}

//-----------------------------------------
function RA_changeVisibility(name, state) {
  var o = RA_GetElem(name);
  if (o) {
    if (o.ra_display == undefined)
      o.ra_display=false;
    o.ra_display = (state != undefined ? state : !o.ra_display);
    o.style.display = (o.ra_display ? 'block' : 'none');
    RA_Debug('display for ' + name, o.ra_display);
  }
}

//----------------------
function RA_changeSetting(name, value) {
//  alert(name + " " + value);
}

//
