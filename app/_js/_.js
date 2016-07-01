;
toastr.options = {
	"closeButton": false,
	"debug": false,
	"newestOnTop": false,
	"progressBar": false,
	"positionClass": "toast-top-center",
	"preventDuplicates": true,
	"onclick": null,
	"showDuration": "300",
	"hideDuration": "1000",
	"timeOut": "3000",
	"extendedTimeOut": "1000",
	"showEasing": "swing",
	"hideEasing": "linear",
	"showMethod": "slideDown",
	"hideMethod": "slideUp"
}


$.fn.modal.Constructor.prototype.enforceFocus =function(){};
var toolbar = [
	['Source'],
	[
		'Cut',
		'Copy',
		'Paste',
		'Find',
		'Replace'
	],
	[
		'Bold',
		'Italic',
		'Underline',
		'StrikeThrough'
	],
	[
		'Styles',
		'Format',
		'Font',
		'FontSize'
	],
	[
		'NumberedList',
		'BulletedList'
	],
	[
		'Outdent',
		'Indent'
	]
];
var toolbar_small = [
	['Source'],
	[
		'Cut',
		'Copy',
		'Paste',
		'Find',
		'Replace'
	],
	[
		'Bold',
		'Italic',
		'Underline',
		'StrikeThrough'
	]
];
var ckeditor_config = {
	height            : '150px',
	toolbar           : toolbar,
	extraPlugins      : 'autogrow',
	autoGrow_minHeight: 150,
	autoGrow_maxHeight: 0,
	removePlugins     : 'elementspath',
	resize_enabled    : false,
	skin : 'bootstrapck,/app/_css/ckeditor/bootstrapck/',
	on :{
		instanceReady : function( ev ){
			this.dataProcessor.writer.setRules( '*',
					{
						indent : false,
						breakBeforeOpen : true,
						breakAfterOpen : false,
						breakBeforeClose : false,
						breakAfterClose : true
					});
		}
	}
};
var ckeditor_config_small = {
	height            : '117px',
	toolbar           : toolbar_small,
	removePlugins     : 'elementspath',
	resize_enabled    : false,
	extraPlugins      : 'autogrow',
	autoGrow_minHeight: 117,
	autoGrow_maxHeight: 0,
	skin : 'bootstrapck,/app/_css/ckeditor/bootstrapck/',
	on :{
		instanceReady : function( ev ){
			this.dataProcessor.writer.setRules( '*',
					{
						indent : false,
						breakBeforeOpen : true,
						breakAfterOpen : false,
						breakBeforeClose : false,
						breakAfterClose : true
					});
		}
	}
};


var lastScroll = 0;
$(window).scroll(function() {
	menucalcs()
});
$(window).resize(function() {
	menucalcs()
});

$(document).ready(function () {
	menucalcs();
	
	
	$(document).on("submit",".status-lookup-form",function(e){
		e.preventDefault();
		var data = $(this).serialize();
		var key = $("#IDNumber",this).val();
		if (key){
			$.bbq.pushState({"IDNumber": key});
			getIDNumberDetails()
			
		} else {
			$(".form-validation",this).remove();
			var $IDNumber = $("#IDNumber",this);
			var IDNumber = $IDNumber.val();
			$field = $IDNumber.parent();
			$field.addClass("has-error");
			$field.after('<span class="help-block s form-validation">ID Number required</span>');
		}
		
	});
	$(".status-lookup-btn").on("click",function(e){
		alert("from _.js")
	});
	$(document).on("click",".status-lookup-btn",function(e){
		e.preventDefault();
		$.bbq.pushState({"IDNumber": ""});
		getIDNumberDetails()
	});
	
	
	
});
function menucalcs(){
	var currentScroll = $(window).scrollTop();
	
	if ($(window).width()>991){
		var nbbfs = 0;
		var s = 30;
		var nbbfss = nbbfs - currentScroll;
		
		nbbfs = nbbfss < -s?-s:nbbfss;
		
		var fs = 46;
		var sfs = 34;
		
		
		var p = 0;
		p = (currentScroll/s)*100;
		
		var t = fs - sfs;
		t =  (p/100)*t;
		
		fs = fs - t;
		fs = fs<sfs?sfs:fs;
		
		
		
		
		var t = fs /2;
		t =  (p/100)*t;
		
		var fs_mt = fs - t;
		fs_mt = fs - fs_mt;
		fs_mt = fs_mt > fs/2?fs/2:fs_mt;



//	console.log("nbbfs:"+nbbfs+" | s:"+s+" | currentScroll:"+currentScroll+" | fs:"+fs+" | p:"+p+" | fs_mt:"+fs_mt)
		$("#main-nav").css({
			"margin-top":nbbfs
		})
		$(".navbar-brand").css({
			"font-size":fs,
			"margin-bottom":-fs,
			"margin-top":fs_mt,
		})
	} else {
		
		
		$(".navbar-brand").css({
			"font-size":34,
			"margin-bottom":-34,
			"margin-top":2,
		})
	}
	
	
	
}
var ajaxRequests = 0;

function ajaxRequestLoading(){
	if (ajaxRequests>0){
		$('#loadingmask').stop(true,true).fadeIn(10);
	} else {
		$('#loadingmask').stop(true,true).fadeOut(500);
	}
	
}


window.onerror = function() {
	$('#loadingmask').stop(true,true).fadeOut(500);
}


$(document).ajaxSend(function(event, request, settings) {
	
	if (settings.url.indexOf("hiddenajax")!=-1){
		
	} else {
		ajaxRequests = ajaxRequests+1;
		ajaxRequestLoading();
	}
	
});

$(document).ajaxComplete(function(event, request, settings) {
	if (settings.url.indexOf("hiddenajax")!=-1){
		
	} else {
		ajaxRequests = ajaxRequests-1;
		ajaxRequestLoading();
	}
	
	
});

;$(document).ready(function () {
	
	$('[data-toggle="tooltip"]').tooltip();
	$('[data-toggle="popover"]').popover();
	
	
	//$('#loadingmask').stop(true,true).fadeOut(500);
	
	$( document ).ajaxError(function( event, jqxhr, settings, thrownError) {
		//console.log(settings.url.indexOf("true"))
		if (jqxhr.status == 403) {
			alert("Sorry, your session has expired. Please login again to continue");
			window.location.href ="/login";
		} else if (thrownError === 'abort') {
		} else if (settings.url.indexOf("hiddenajax")!=-1) {
			
		} else {
			alert("An error occurred: " + jqxhr.status + "\nError: " + thrownError);
		}
	});
	
	
	
	
	
	$(document).on('click', '.btn-row-details', function (e) {
		var $this = $(this), $table = $this.closest("table");
		var $clicked = $(e.target).closest("tr.btn-row-details");
		var active = true;

		if ($this.hasClass("active") && $clicked) active = false;

		$("tr.btn-row-details.active", $table).removeClass("active");
		if (active) {
			$this.addClass("active");
		}

		var show = $("tr.btn-row-details.active", $table).nextAll("tr.row-details");

		$("tr.row-details", $table).hide();
		if (show.length) {
			show = show[0];
			$(show).show();
		}

	});

	
	resize();


	
	
	$(window).resize(function () {
		$.doTimeout(250, function () {
			resize();
			
		});
	});
	
	$(window).scroll(function (event) {
		scroll();
		// Do something
	});
	

	
	$(".select2").select2();
	
	
});



$(document).on('show.bs.modal', '.modal', function () {
	var zIndex = 1040 + (10 * $('.modal:visible').length);
	$(this).css('z-index', zIndex);
	setTimeout(function() {
		$('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
	}, 0);
});

$(document).on('hidden.bs.modal', '.modal', function () {
	$('.modal:visible').length && $(document.body).addClass('modal-open');
});


function resize() {
	var wh = $(window).height();
	var ww = $(window).width();
	var mh = wh - $("#navbar-header").outerHeight() - 6;
	$("#menu").css({"max-height":mh});
	scroll();
	$(".panel-fixed").each(function(){
		var $this = $(this);
		var h = $this.find("> .panel-heading").outerHeight();
		var f = $this.find("> .panel-footer").outerHeight();
		$this.find("> .panel-body").css({top:h,bottom:f});
	//	console.log(h)
	});
}

function updatetimerlist(d, page_size) {
	//d = jQuery.parseJSON(d);
	if (!d || !typeof d == 'object') {
		return false;
	}
	//console.log(d);
	var data = d['timer'];
	var page = d['page'];
	var models = d['models'];
	var menu = d['menu'];




	if (data) {
		
		var highlight = "";
		if (page['time'] > 0.5)    highlight = 'style="color: red;"';

		var th = '<tr class="heading" style="background-color: #fdf5ce;"><td >' + page['page'] + '</td><td class="s g"' + highlight + '>' + page['time'] + '</td></tr>';
		var thm = "";
		if (models) {
			thm = $("#template-timers-tr-models").jqote(models);
		} 
		//console.log(thm)
		var timers = $("#template-timers-tr").jqote(data);
		//console.log(timers)

		//console.log($("#template-timers-tr"))
		//console.log(thm)
		
		$("#systemTimers").prepend(th + timers + thm);
		
		
		
		
		
	}
	
		resize()

};

$(document).on("change",".has-error input",function(){
	var $field = $(this);
	$field.closest(".has-error").removeClass("has-error").find(".form-validation").remove();
	submitBtnCounter($field.closest("form"));
})



function validationErrors(data, $form) {
	
	if (!$.isEmptyObject(data['errors'])) {
		
		var i = 0;
		//console.log(data.errors);
		$(".form-validation",$form).remove();
		$.each(data.errors, function (k, v) {
			i = i + 1;
			var $field = $("#" + k);
			//console.info(k)
			var $block = $field.closest(".form-group");

			$block.addClass("has-error");
			if ($field.parent().hasClass("input-group")) $field = $field.parent();
			
			
			if (v != "") {
				
				$field.after('<span class="help-block s form-validation">' + v + '</span>');
			}
			if ($block.hasClass("has-feedback")){
				$field.after('<span class="fa fa-times form-control-feedback form-validation" aria-hidden="true"></span>')
			}


		});
		$("button[type='submit']", $form).addClass("btn-danger").html("(" + i + ") Error(s) Found");
		
			if (i>1){
				toastr["error"]("There were " + i + " errors saving the form", "Error");
			} else {
				toastr["error"]("There was an error saving the form", "Error");
			}
		
		
		
	} else {
			toastr["success"]("Record Saved", "Success");
		
	}

	//submitBtnCounter($form);
	
	
}

function submitBtnCounter($form) {
	var c = $(".has-error",$form).length;
	var $btn = $("button[type='submit']", $form);
	if (c) {
		$btn.addClass("btn-danger").html("(" + c + ") Error(s) Found");
	} else {
		
		var tx = $btn.attr("data-text")||"Save";
		
		$btn.html(tx).removeClass("btn-danger");
	}
}

function scroll(){
	var ww = $(window).width();
	var $toolbar = $("#toolbar");
	
	if ($toolbar.length){
		var toolbartop = $toolbar.offset().top;
		var navbarheight = $(".navbar-fixed-top").outerHeight();
		var toolbarheight = $toolbar.outerHeight();
		var scrollTop = $(window).scrollTop();
		
		var contentOffset = $("#content-area").offset().top;
		
		var toolboxtopscroll = (contentOffset - toolbarheight)-15
		
	//	console.log("toolbartop: "+toolbartop+" | navbarheight: "+navbarheight+" | scroll:"+scrollTop + " | toolbar fixed: "+$toolbar.hasClass("fixed")+" | v:"+toolboxtopscroll);
		
		if ((scrollTop > (toolboxtopscroll - navbarheight))&& ww > 768){
			$toolbar.addClass("fixed").css({"top":navbarheight - 1});
			$("#content-area").css({"margin-top":$toolbar.outerHeight()+30});
		} else {
			$toolbar.removeClass("fixed");
			$("#content-area").css({"margin-top":0});
		}
	}
	
}
function uniqueid(){
	// always start with a letter (for DOM friendlyness)
	var idstr=String.fromCharCode(Math.floor((Math.random()*25)+65));
	do {
		// between numbers and characters (48 is 0 and 90 is Z (42-48 = 90)
		var ascicode=Math.floor((Math.random()*42)+48);
		if (ascicode<58 || ascicode>64){
			// exclude all chars between : (58) and @ (64)
			idstr+=String.fromCharCode(ascicode);
		}
	} while (idstr.length<12);
	
	idstr = idstr + (new Date()).getTime();
	return (idstr);
}
function sizeselect2(item) {
	if (!item.id) return item.text; // optgroup
	var str = item.text;
	str = str.split("||");
	str = "<span class='g pull-right'>" + str[1] + "</span><span>" + str[0] + "</span>";
	
	return str;
}


$(document).on("click", ".my-ward", function (e) {
	e.preventDefault();
	getMyWard();
})

function getMyWard() {
	var $wardmodal = $("#modal-window");
	$wardmodal.modal("show");
	
	if (navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(successFunctionMyWard, errorFunctionMyWard);
	} else {
		errorFunctionMyWard();
	}
	$.doTimeout(3000, function () {
		if (!$.bbq.getState("ward")) {
			getWardDetails()
		}
		
	})
}
function successFunctionMyWard(position) {
	var lat = position.coords.latitude;
	var lng = position.coords.longitude;
	$.bbq.pushState({"ward": lng + "," + lat});
	getWardDetails()
}
function errorFunctionMyWard(position) {
	$.bbq.removeState("ward");
	getWardDetailsNoKey()
}



function getIDNumberDetails() {
	var key = $.bbq.getState("IDNumber");
	$.getData("/iec/voter?IDNumber="+key,function(data){
		$("#modal-window").jqotesub($("#template-voter-status"), data).modal("show").on("hidden.bs.modal", function () {
			
		})
	}, "idnumber-details")
}






$(document).on("submit",".address-lookup-form",function(e){
	e.preventDefault();
	var data = $(this).serialize();
	var key = $("#address",this).val();
	if (key){
		$.getData("/data/lookup/address", data, function (data) {
			//console.log(data.Ward.codes.MDB);
			var key = "";
			if (data.geometry){
				var lat = data.geometry.location.lat;
				var lng = data.geometry.location.lng;
				
				key = lng + "," + lat;
			}
			$.bbq.pushState({"ward": key});
			getWardDetails()
		})
		
	} else {
		$(".form-validation",this).remove();
		var $IDNumber = $("#address",this);
		var IDNumber = $IDNumber.val();
		$field = $IDNumber.parent();
		$field.addClass("has-error");
		$field.after('<span class="help-block s form-validation">Address required</span>');
	}
	
	
	
	
	
	
});

$(document).on("click",".address-lookup-btn",function(e){
	e.preventDefault();
	
		$.bbq.pushState({"ward": ""});
		getWardDetails()

	
});

function getWardDetails() {
	var key = $.bbq.getState("ward");
	
	
	$.getData("/data/ward/data", {"ward": key}, function (data) {
		
		$("#modal-window").jqotesub($("#template-ward-details"), data).modal("show").on("hidden.bs.modal", function () {
			$.bbq.removeState("ward");
		})
		if (data.geojson) {
			$.doTimeout(400, function () {
				
				var mapm = new L.Map("leaflet-modal", {zoomControl: false});
				mapm.attributionControl.setPrefix('');
				
				var osmm = new L.TileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
					attribution: 'Map © <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> | Powered by <a href="http://mapit.code4sa.org/">MapIt</a>',
					maxZoom: 18,
					fillColor: "#ffffff"
				});
				
				console.log(data.VotingStation)
				
				mapm.addLayer(osmm);
				
				var aream = new L.GeoJSON(data.geojson);
				console.log(aream)
				mapm.addLayer(aream);
				mapm.fitBounds(aream.getBounds());
				
				if (data.VotingStation.Location.Latitude && data.VotingStation.Location.Longitude){
					
				//	L.Icon.Default.imagePath = 'path-to-your-leaflet-images-folder';
					
					var LeafIcon = L.Icon.extend({
						options: {
						
							iconSize:     [100, 62],
							iconAnchor:   [50, 62],
						
						}
					});
					
					var greenIcon = new LeafIcon({iconUrl: '/app/_images/marker_iec.png'});
					
					
					
					
					L.marker([data.VotingStation.Location.Latitude,data.VotingStation.Location.Longitude], {icon: greenIcon}).addTo(mapm);
				}
				
				
			})
		}
		
	}, "ward-details")
}
function getWardDetailsNoKey() {
	$.bbq.removeState("ward");
	getWardDetails()
	
}

;