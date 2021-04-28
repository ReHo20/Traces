jQuery(document).ready(function ($) {

var mymap = L.map('mapid',{zoomControl:false, attributionControl:false}).setView([52.1987551,5.5440951], 8);

	L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
		maxZoom: 30,
		id: 'mapbox/streets-v11',
		tileSize: 512,
		zoomOffset: -1,
		minZoom:8
	}).addTo(mymap);

    $.each(latlngs, function(){
        L.polyline(this.coordinates, {color: this.color, trace_id:this.id}).addTo(mymap).on('click', (e) => {
			console.log(e);
			$('[data-id="'+e.sourceTarget.options.trace_id+'"]').toggleClass('is-active');
		});
    });

function copyright(){
    var today = new Date ();
    return '2021' === today.getFullYear().toString() ? today.getFullYear() : '2021 - ' + today.getFullYear();
}

    // zoom the map to the polyline
// map.fitBounds(polyline.getBounds());

});