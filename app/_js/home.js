$(document).ready(function () {
	
	$(document).on("click", ".my-ward", function (e) {
		e.preventDefault();
		getMyWard();
	})
	
	
	$(document).on("submit",".address-lookup-form",function(e){
		e.preventDefault();
		var data = $(this).serialize();
		$.getData("/data/lookup/address", data, function (data) {
			//console.log(data.Ward.codes.MDB);
			
			var lat = data.geometry.location.lat;
			var lng = data.geometry.location.lng;
			
			$.bbq.pushState({"ward": lng + "," + lat});
			getWardDetails()
		})
		
	})
});

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
				
				mapm.addLayer(osmm);
				
				var aream = new L.GeoJSON(data.geojson);
				console.log(aream)
				mapm.addLayer(aream);
				mapm.fitBounds(aream.getBounds());
			})
		}
		
	}, "ward-details")
}
function getWardDetailsNoKey() {
	$.bbq.removeState("ward");
	getWardDetails()
	
}