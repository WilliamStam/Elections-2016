


function map(){
	var map = new L.Map("leaflet");
	map.attributionControl.setPrefix('');
	
	
	
	
	var osm = new L.TileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
		attribution: 'Map © <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> | Powered by <a href="http://mapit.code4sa.org/">MapIt</a>',
		maxZoom: 18,
		fillColor:"#ffffff"
	});
	
	map.addLayer(osm);
	
	
	
	
	
	var ward = "93404020";
	//var ward = "93404001";
	
	// http://elections.local/lookup/area/MDB:93404001
	
	$.getData("http://elections.local/lookup/area/MDB:"+ward,{"simplify_tolerance":"0.0001"},function(data){
		var area = new L.GeoJSON(data);
		var area1 = new L.GeoJSON(data);
		map.addLayer(area);
		map.fitBounds(area.getBounds());
		
		
	});
	
	/*
	 $.getData("http://elections.local/lookup/areas/MDB-levels:MN-LIM344|WD",{"simplify_tolerance":"0.0001"},function(data){
	 
	 
	 var area = new L.GeoJSON(data, {style: {weight: 2.0}});
	 map.addLayer(area);
	 map.fitBounds(area.getBounds());
	 
	 
	 });
	 */
	
}

function getData() {
	var ID = $.bbq.getState("ID") || '';
	var page = $.bbq.getState("page") || '1';
	
	$(".loadingmask").show();
	
	
	
	
	
}
