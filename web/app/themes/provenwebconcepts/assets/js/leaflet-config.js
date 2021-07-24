jQuery(document).ready(function ($) {

    var mymap = L.map('mapid', {
        zoomControl: false,
        attributionControl: false
    }).setView([52.1987551, 5.5440951], 8);

    L.tileLayer.provider('OpenStreetMap.Mapnik', {
        maxZoom: 30,
        id: 'mapbox/streets-v11',
        minZoom: 8
    }).addTo(mymap);

    var traces = {};

    $.each(latlngs, function(){
        traces[this.id] = L.polyline(this.coordinates, {color: this.color, trace_id:this.id}).addTo(mymap).on('click', function(e){
			$('[data-id="'+e.sourceTarget.options.trace_id+'"]').toggleClass('is-active');
        e.stopPropagation();
		});
    });

    mymap.on('click', function(e) {
        $('aside .information').removeClass('is-active');
        e.stopPropagation();
    });

    $(document).on('click', 'aside .information', function(){
        mymap.flyToBounds(traces[$(this).attr('data-id')].getBounds());
        $(this).addClass('is-active');
    });

    function copyright() {
        var today = new Date();
        return '2021' === today.getFullYear().toString() ? today.getFullYear() : '2021 - ' + today.getFullYear();
    }

});