$(document).ready(function(){
	
	map();
	
	
	

	
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
	
	for (var i in _data){
		
		var area = new L.GeoJSON(_data[i].data, {
			onEachFeature: function(feature, layer) {
				layer.on('click', function(e) {
				//	console.log("click")
				//	console.log(e)
				});
			}
		});
		map.addLayer(area);
		
		groupBounds.push(area);
		
	}
	var group = new L.featureGroup(groupBounds);
	map.fitBounds(group.getBounds());
	
	
}
function getData(){
	var ID = $.bbq.getState("ID");
	
	
	
	$.getData("/data/ward_map/data", {"ID": ID}, function (data) {
		
		
		$("#modal-window").jqotesub($("#template-ward-details"), data).modal("show");
		
		
		
		
	})
	
	
}