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
  var o = RA_GetElem("settings-link");
  o.onclick=function() { RA_changeVisibility('settings'); return false; };

/*
  var vi = ["theme", "color"];
  var vj = [1, 2, 3];
  for(var i in vi) {
    for(var j in vj) {
      var ni = vi[i];
      var nj = vj[j];
      var name = "settings-" + ni + nj;
      o = RA_GetElem(name);
      RA_Debug("ptr", ni+" "+nj+" = "+name+" "+o);
      if (o) {
        o.onclick=function() { RA_changeSetting(ni2.toString(), nj2.toString()); return false; };
        RA_Debug("func", o.onclick);
      }
    }
  }
  */
}

//------------------
function RA_Init() {
	try {
		RA_SetContent("debug", "Debug Zone -- Started.<br>");
	    RA_changeVisibility("settings", false);
	    RA_SetEvents();
		} catch(e) {
			RA_Debug("Exception", e);
		}
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
