var windowHeight = $(window).height();

$("#leaflet").css({"height":windowHeight-200})


$(document).ready(function(){
	
	map();
	
	if ($.bbq.getState("key")){
		getWardDetailsMap()
	}
	

	
});


function map(){
	
	
	
	var map = new L.Map("leaflet");
	map.attributionControl.setPrefix('');
	
	
	
	
	var osm = new L.TileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
		attribution: 'Map © <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> | Powered by <a href="http://mapit.code4sa.org/">MapIt</a>',
		maxZoom: 18,
		fillColor:"#ffffff"
	});
	
	map.addLayer(osm);
	groupBounds = [];
	
	L.Icon.Default.imagePath = './app/_plugins/leaflet/images';
	
	for (var i in _data){
		
		var area = new L.GeoJSON(_data[i].data, {
			onEachFeature: function(feature, layer) {
				layer.on('click', function(e) {
				//	console.log("click")
					$.bbq.pushState({"key":e.latlng.lng+","+e.latlng.lat});
					getWardDetailsMap()
					console.log(e)
				});
				
				
			}
		});
		
		
		var label = L.marker(area.getBounds().getCenter(), {
			icon: L.divIcon({
				iconSize: null,
				className: 'ward-label',
				iconAnchor:   [60, 15],
				html: '<div>Ward:<br>' + _data[i].wardID + '</div>'
			})
		}).addTo(map);
		
		
		//marker.bindLabel("My Label", {noHide: true, className: "my-label", offset: [0, 0] });
		//marker.addTo(map);
		
		map.addLayer(area);
		groupBounds.push(area);
		/*
		
		label = new L.Label()();
		label.setContent(_data[i].wardID)
		label.setLatLng(area.getBounds().getCenter())
		map.showLabel(label);
		*/
		
		
	}
	var group = new L.featureGroup(groupBounds);
	map.fitBounds(group.getBounds());
	
	
}
function getWardDetailsMap(){
	var key = $.bbq.getState("key");
	$.getData("/data/ward_map/clicked", {"key": key}, function (data) {
		
		
		$("#modal-window").modal("show").jqotesub($("#template-ward-details"), data).on("hidden.bs.modal",function(){
			$.bbq.removeState("key");
		})
		
		$.doTimeout(400,function(){
			var mapm = new L.Map("leaflet-modal",{zoomControl:false});
			mapm.attributionControl.setPrefix('');
			
			var osmm = new L.TileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
				attribution: 'Map © <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> | Powered by <a href="http://mapit.code4sa.org/">MapIt</a>',
				maxZoom: 18,
				fillColor:"#ffffff"
			});
			
			mapm.addLayer(osmm);
			
			var aream = new L.GeoJSON(data.geojson);
			//console.log(aream)
			mapm.addLayer(aream);
			mapm.fitBounds(aream.getBounds());
			if (data.VotingStation){
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
			}
			
			
			
		})
		
		
		
	})
	
	
}